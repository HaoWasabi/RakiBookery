<?php

require_once ROOT_PATH . '/config/paypal.php';
require_once ROOT_PATH . '/app/controllers/BaseController.php';
require_once ROOT_PATH . '/app/models/Order.php';
require_once ROOT_PATH . '/app/models/Address.php';
require_once ROOT_PATH . '/app/models/OrderDetail.php';
require_once ROOT_PATH . '/app/models/Promo.php';
require_once ROOT_PATH . '/app/models/PaymentMethod.php';

class PayPalController extends BaseController
{
    private $orderModel;
    private $addressModel;
    private $orderDetailModel;
    private $promoModel;
    private $paymentMethodModel;

    public function __construct()
    {
        $this->orderModel       = new Order();
        $this->addressModel     = new Address();
        $this->orderDetailModel = new OrderDetail();
        $this->promoModel       = new Promo();
        $this->paymentMethodModel = new PaymentMethod();
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
            CURLOPT_SSL_VERIFYPEER => false, // tắt SSL verify cho môi trường local/XAMPP
            CURLOPT_SSL_VERIFYHOST => false,
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

        // Tính tổng tiền
        $subtotal = array_reduce($_SESSION['cart'], function ($sum, $item) {
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

        return compact('userId', 'addressId', 'subtotal', 'totalAmount', 'promoId', 'paymentMethodId');
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
            'cart'            => $_SESSION['cart'],
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
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
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
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
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

            // Thanh toán thành công → tạo đơn hàng trong DB
            $orderId = $this->orderModel->createOrder(
                $checkout['userId'],
                $checkout['totalAmount'],
                $checkout['addressId'],
                $checkout['paymentMethodId'],
                $checkout['promoId'],
                $checkout['subtotal']
            );

            if (!$orderId) {
                // Capture đã thành công nhưng DB lỗi — log để xử lý thủ công
                error_log('PayPal capture OK nhưng createOrder thất bại. PayPal Order ID: ' . $paypalOrderId);
                throw new RuntimeException('Thanh toán thành công nhưng có lỗi khi lưu đơn hàng. Vui lòng liên hệ hỗ trợ.');
            }

            // Lưu chi tiết đơn hàng
            foreach ($checkout['cart'] as $item) {
                $this->orderDetailModel->createOrderDetail(
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                );
            }

            // Cập nhật trạng thái đơn hàng sang 'processing' vì đã thanh toán
            $this->orderModel->updateOrderStatus($orderId, 'processing');

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
