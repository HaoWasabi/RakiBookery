<?php

require_once ROOT_PATH . '/config/paypal.php';
require_once ROOT_PATH . '/app/controllers/BaseController.php';
require_once ROOT_PATH . '/app/models/Order.php';
require_once ROOT_PATH . '/app/models/Address.php';
require_once ROOT_PATH . '/app/models/OrderDetail.php';
require_once ROOT_PATH . '/app/models/Promo.php';
require_once ROOT_PATH . '/app/models/PaymentMethod.php';
require_once ROOT_PATH . '/app/services/SnsService.php';

class PayPalController extends BaseController
{
    private $orderModel;
    private $addressModel;
    private $orderDetailModel;
    private $promoModel;
    private $paymentMethodModel;
    private $snsService;

    public function __construct()
    {
        $this->orderModel       = new Order();
        $this->addressModel     = new Address();
        $this->orderDetailModel = new OrderDetail();
        $this->promoModel       = new Promo();
        $this->paymentMethodModel = new PaymentMethod();
        $this->snsService       = new SnsService();
    }

    // -----------------------------------------------------------------------
    // Lấy Access Token từ PayPal
    // -----------------------------------------------------------------------
    private function getAccessToken(): string
    {
        $ch = curl_init(PAYPAL_API_BASE . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_CLIENT_SECRET,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        if ($curlError) {
            error_log('PayPal cURL error (' . $curlErrno . '): ' . $curlError);
            throw new RuntimeException('Lỗi kết nối đến PayPal: ' . $curlError);
        }

        if ($httpCode !== 200) {
            error_log('PayPal get token failed (' . $httpCode . '): ' . $response);
            $body = json_decode($response, true);
            $detail = $body['error_description'] ?? $body['message'] ?? $response;
            throw new RuntimeException('Xác thực PayPal thất bại: ' . $detail);
        }

        $data = json_decode($response, true);
        return $data['access_token'];
    }

    // -----------------------------------------------------------------------
    // Convert VND → USD (PayPal sandbox thường chỉ hỗ trợ USD)
    // -----------------------------------------------------------------------
    private function convertToPayPalAmount(float $vndAmount): string
    {
        if (PAYPAL_CURRENCY === 'VND') {
            // Nếu tài khoản PayPal hỗ trợ VND, truyền thẳng
            return number_format($vndAmount, 0, '.', '');
        }
        // Convert VND sang USD
        $usd = $vndAmount / PAYPAL_VND_TO_USD_RATE;
        return number_format($usd, 2, '.', '');
    }

    // -----------------------------------------------------------------------
    // Chuẩn bị dữ liệu đơn hàng từ request (dùng chung cho cả create & capture)
    // -----------------------------------------------------------------------
    private function prepareOrderData(): array
    {
        if (!isset($_SESSION['UserID']) || empty($_SESSION['cart'])) {
            throw new RuntimeException('Phiên đăng nhập hết hạn hoặc giỏ hàng trống.');
        }

        $userId = $_SESSION['UserID'];

        // Địa chỉ
        if (!empty($_POST['new_address'])) {
            $addressId = $this->addressModel->create(
                $_POST['new_address'],
                $_POST['new_city'],
                $_POST['new_district'],
                $_POST['new_ward']
            );
            if (!$addressId) {
                throw new RuntimeException('Không thể lưu địa chỉ mới.');
            }
        } else {
            $addressId = (int)($_POST['address_id'] ?? 0);
            if ($addressId <= 0) {
                throw new RuntimeException('Địa chỉ không hợp lệ.');
            }
        }

        // Tính lại giá từ database — không tin giá lưu trong session
        require_once ROOT_PATH . '/app/models/Book.php';
        $bookModel = new Book();
        $recalculatedCart = [];

        foreach ($_SESSION['cart'] as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity  = (int) ($item['quantity']   ?? 0);
            if ($productId <= 0 || $quantity <= 0) continue;

            $book = $bookModel->getAvailableBookById($productId);
            if (!$book) {
                throw new RuntimeException('Sản phẩm không còn bán: ID ' . $productId);
            }

            $recalculatedCart[] = [
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => (float) $book['Price'],
            ];
        }

        if (empty($recalculatedCart)) {
            throw new RuntimeException('Giỏ hàng không hợp lệ.');
        }

        // Tính tổng tiền
        $subtotal = array_reduce($recalculatedCart, function ($sum, $item) {
            return $sum + ($item['quantity'] * $item['price']);
        }, 0);

        // Promo
        $promoId     = null;
        $totalAmount = $subtotal;
        $promoCode   = trim($_POST['promo_code'] ?? '');

        if (!empty($promoCode)) {
            $promo = $this->promoModel->getActivePromoByName($promoCode);
            if (!$promo) {
                throw new RuntimeException('Mã khuyến mãi không hợp lệ hoặc đã hết hạn.');
            }
            $promoId     = $promo['PromoID'];
            $discount    = ($subtotal * $promo['Discounted']) / 100;
            $totalAmount = $subtotal - $discount;
        }

        // Lấy PaymentMethodID cho PayPal
        $paymentMethodId = (int)($_POST['payment_method_id'] ?? 0);

        return compact('userId', 'addressId', 'subtotal', 'totalAmount', 'promoId', 'paymentMethodId', 'recalculatedCart');
    }

    // -----------------------------------------------------------------------
    // Lưu thông tin checkout vào session để dùng khi capture
    // -----------------------------------------------------------------------
    private function storeCheckoutSession(array $orderData): void
    {
        $_SESSION['paypal_checkout'] = [
            'userId'          => $orderData['userId'],
            'addressId'       => $orderData['addressId'],
            'subtotal'        => $orderData['subtotal'],
            'totalAmount'     => $orderData['totalAmount'],
            'promoId'         => $orderData['promoId'],
            'paymentMethodId' => $orderData['paymentMethodId'],
            'cart'            => $orderData['recalculatedCart'],
        ];
    }

    // -----------------------------------------------------------------------
    // STEP 1: Tạo PayPal Order (gọi từ frontend khi user click nút PayPal)
    // POST /paypal/create-order
    // Body: FormData (giống processCheckout)
    // -----------------------------------------------------------------------
    public function createOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseJson(['success' => false, 'message' => 'Method không hợp lệ'], 405);
        }

        try {
            $orderData = $this->prepareOrderData();
            $this->storeCheckoutSession($orderData);

            $accessToken = $this->getAccessToken();

            $amountStr = $this->convertToPayPalAmount($orderData['totalAmount']);

            // Đảm bảo amount tối thiểu $0.01 USD
            if ((float)$amountStr <= 0) {
                throw new RuntimeException('Số tiền thanh toán không hợp lệ.');
            }

            $payload = [
                'intent'         => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount'      => [
                            'currency_code' => PAYPAL_CURRENCY === 'VND' ? 'USD' : PAYPAL_CURRENCY,
                            'value'         => $amountStr,
                        ],
                        'description' => 'RakiBookery - Đặt hàng sách',
                    ]
                ],
                'application_context' => [
                    'brand_name'          => 'RakiBookery',
                    'landing_page'        => 'NO_PREFERENCE',
                    'user_action'         => 'PAY_NOW',
                    'shipping_preference' => 'NO_SHIPPING',
                ],
            ];

            $ch = curl_init(PAYPAL_API_BASE . '/v2/checkout/orders');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                    'PayPal-Request-Id: raki-' . uniqid(),
                ],
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT        => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 201) {
                error_log('PayPal create order failed (' . $httpCode . '): ' . $response);
                $paypalError = json_decode($response, true);
                $errorMsg = $paypalError['message'] ?? 'Không thể tạo giao dịch PayPal.';
                throw new RuntimeException($errorMsg);
            }

            $result = json_decode($response, true);

            $this->responseJson([
                'success' => true,
                'id'      => $result['id'],
            ]);
        } catch (RuntimeException $e) {
            error_log('PayPalController::createOrder exception: ' . $e->getMessage());
            $this->responseJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // -----------------------------------------------------------------------
    // STEP 2: Capture PayPal Order (sau khi user approve trên popup PayPal)
    // POST /paypal/capture-order
    // Body JSON: { "paypalOrderId": "..." }
    // -----------------------------------------------------------------------
    public function captureOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseJson(['success' => false, 'message' => 'Method không hợp lệ'], 405);
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $paypalOrderId = trim($body['paypalOrderId'] ?? '');

        if (empty($paypalOrderId)) {
            $this->responseJson(['success' => false, 'message' => 'Thiếu PayPal Order ID.']);
            return;
        }

        // Đọc thông tin đơn hàng đã lưu trong session
        $checkout = $_SESSION['paypal_checkout'] ?? null;
        if (!$checkout || empty($checkout['cart'])) {
            $this->responseJson(['success' => false, 'message' => 'Phiên thanh toán hết hạn. Vui lòng thử lại.']);
            return;
        }

        // Idempotency guard: kiểm tra paypalOrderId đã xử lý thành công chưa
        if (isset($_SESSION['paypal_processed'][$paypalOrderId])) {
            $this->responseJson([
                'success' => true,
                'message' => 'Thanh toán PayPal thành công!',
                'orderId' => $_SESSION['paypal_processed'][$paypalOrderId],
            ]);
            return;
        }

        try {
            $accessToken = $this->getAccessToken();

            // Gọi PayPal Capture API
            $ch = curl_init(PAYPAL_API_BASE . '/v2/checkout/orders/' . $paypalOrderId . '/capture');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ],
                CURLOPT_POSTFIELDS     => '{}',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT        => 30,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode !== 201 || ($result['status'] ?? '') !== 'COMPLETED') {
                error_log('PayPal capture failed (' . $httpCode . '): ' . $response);
                throw new RuntimeException('Thanh toán PayPal thất bại. Vui lòng thử lại.');
            }

            // Capture thành công — lưu toàn bộ đơn hàng trong một transaction
            $this->orderModel->beginTransaction();
            try {
                $orderId = $this->orderModel->createOrder(
                    $checkout['userId'],
                    $checkout['totalAmount'],
                    $checkout['addressId'],
                    $checkout['paymentMethodId'],
                    $checkout['promoId'],
                    $checkout['subtotal']
                );

                if (!$orderId) {
                    throw new RuntimeException('createOrder trả về false.');
                }

                foreach ($checkout['cart'] as $item) {
                    $ok = $this->orderDetailModel->createOrderDetail(
                        $orderId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['price']
                    );
                    if (!$ok) {
                        throw new RuntimeException('createOrderDetail thất bại, sản phẩm ID ' . $item['product_id']);
                    }
                }

                $this->orderModel->updateOrderStatus($orderId, 'pending');
                $this->orderModel->commit();
            } catch (RuntimeException $dbEx) {
                $this->orderModel->rollBack();
                // Tiền đã capture nhưng DB lỗi — log đầy đủ để đối soát thủ công
                error_log(sprintf(
                    'PAYPAL_DB_FAILURE | PayPalOrderID=%s | UserID=%d | Amount=%.2f | Error=%s',
                    $paypalOrderId,
                    $checkout['userId'],
                    $checkout['totalAmount'],
                    $dbEx->getMessage()
                ));
                throw new RuntimeException('Thanh toán thành công nhưng có lỗi khi lưu đơn hàng. Vui lòng liên hệ hỗ trợ với mã giao dịch: ' . $paypalOrderId);
            }

            // Đánh dấu đã xử lý để chống double-submit trong cùng session
            $_SESSION['paypal_processed'][$paypalOrderId] = $orderId;

            // Thông báo SNS cho admin
            $this->snsService->notifyNewOrder($orderId, $checkout['totalAmount'], 'PayPal');

            // Xóa giỏ hàng và session checkout
            unset($_SESSION['cart']);
            unset($_SESSION['paypal_checkout']);

            $this->responseJson([
                'success' => true,
                'message' => 'Thanh toán PayPal thành công!',
                'orderId' => $orderId,
            ]);
        } catch (RuntimeException $e) {
            $this->responseJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
