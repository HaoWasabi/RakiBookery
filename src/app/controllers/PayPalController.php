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
    private $bookModel;

    public function __construct()
    {
        $this->orderModel         = new Order();
        $this->addressModel       = new Address();
        $this->orderDetailModel   = new OrderDetail();
        $this->promoModel         = new Promo();
        $this->paymentMethodModel = new PaymentMethod();
        $this->snsService         = new SnsService();
        require_once ROOT_PATH . '/app/models/Book.php';
        $this->bookModel          = new Book();
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
        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        if ($curlError) {
            error_log('PayPal cURL error (' . $curlErrno . '): ' . $curlError);
            throw new RuntimeException('Lỗi kết nối đến PayPal: ' . $curlError);
        }

        if ($httpCode !== 200) {
            error_log('PayPal get token failed (' . $httpCode . '): ' . $response);
            $body   = json_decode($response, true);
            $detail = $body['error_description'] ?? $body['message'] ?? $response;
            throw new RuntimeException('Xác thực PayPal thất bại: ' . $detail);
        }

        $data = json_decode($response, true);
        return $data['access_token'];
    }

    // -----------------------------------------------------------------------
    // Convert VND → USD
    // -----------------------------------------------------------------------
    private function convertToPayPalAmount(float $vndAmount): string
    {
        if (PAYPAL_CURRENCY === 'VND') {
            return number_format($vndAmount, 0, '.', '');
        }
        $usd = $vndAmount / PAYPAL_VND_TO_USD_RATE;
        return number_format($usd, 2, '.', '');
    }

    // -----------------------------------------------------------------------
    // Chuẩn bị dữ liệu đơn hàng từ request
    // -----------------------------------------------------------------------
    private function prepareOrderData(): array
    {
        if (!isset($_SESSION['UserID']) || empty($_SESSION['cart'])) {
            throw new RuntimeException('Phiên đăng nhập hết hạn hoặc giỏ hàng trống.');
        }

        $userId = $_SESSION['UserID'];

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

        $recalculatedCart = [];
        foreach ($_SESSION['cart'] as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity  = (int) ($item['quantity']   ?? 0);
            if ($productId <= 0 || $quantity <= 0) continue;

            $book = $this->bookModel->getAvailableBookById($productId);
            if (!$book) {
                throw new RuntimeException('Sản phẩm không còn bán: ID ' . $productId);
            }
            if ($book['Stock'] < $quantity) {
                throw new RuntimeException('Sản phẩm "' . $book['Name'] . '" chỉ còn ' . $book['Stock'] . ' cuốn trong kho.');
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

        $subtotal = array_reduce($recalculatedCart, function ($sum, $item) {
            return $sum + ($item['quantity'] * $item['price']);
        }, 0);

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

        $paymentMethodId = (int)($_POST['payment_method_id'] ?? 0);

        return compact('userId', 'addressId', 'subtotal', 'totalAmount', 'promoId', 'paymentMethodId', 'recalculatedCart');
    }

    // -----------------------------------------------------------------------
    // Lưu thông tin checkout vào session
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
    // *** MỚI: Hoàn tiền tự động qua PayPal Refund API ***
    // Gọi khi capture OK nhưng ghi DB thất bại.
    // Silent-fail: nếu refund cũng lỗi thì alert admin qua SNS.
    // -----------------------------------------------------------------------
    private function attemptAutoRefund(
        string $paypalOrderId,
        string $captureId,
        string $accessToken,
        float  $totalAmount
    ): void {
        try {
            $ch = curl_init(PAYPAL_API_BASE . '/v2/payments/captures/' . $captureId . '/refund');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ],
                CURLOPT_POSTFIELDS     => '{}',   // hoàn toàn bộ số tiền
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_TIMEOUT        => 30,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 201) {
                error_log(sprintf(
                    'PAYPAL_AUTO_REFUND_OK | PayPalOrderID=%s | CaptureID=%s | Amount=%.2f',
                    $paypalOrderId, $captureId, $totalAmount
                ));
            } else {
                throw new RuntimeException('HTTP ' . $httpCode . ' — ' . $response);
            }
        } catch (\Throwable $e) {
            // Refund thất bại → cần admin xử lý thủ công, gửi alert ngay
            error_log(sprintf(
                'PAYPAL_AUTO_REFUND_FAILED | PayPalOrderID=%s | CaptureID=%s | Error=%s',
                $paypalOrderId, $captureId, $e->getMessage()
            ));
            $this->snsService->publish(
                '[RakiBookery] ⚠️ Cần hoàn tiền PayPal thủ công',
                "Capture thành công nhưng ghi DB thất bại VÀ hoàn tiền tự động cũng thất bại.\n\n"
                . "- PayPal Order ID : {$paypalOrderId}\n"
                . "- Capture ID      : {$captureId}\n"
                . "- Số tiền         : " . number_format($totalAmount, 0, '.', ',') . " VND\n"
                . "- Lỗi refund      : " . $e->getMessage() . "\n"
                . "- Thời gian       : " . date('d/m/Y H:i:s')
            );
        }
    }

    // -----------------------------------------------------------------------
    // *** MỚI: Cộng lại tồn kho cho các sản phẩm trong giỏ ***
    // Gọi khi DB transaction rollback sau khi đã trừ stock.
    // -----------------------------------------------------------------------
    private function restoreStock(array $cart): void
    {
        foreach ($cart as $item) {
            if (!$this->bookModel->restoreStock((int) $item['product_id'], (int) $item['quantity'])) {
                error_log(sprintf(
                    'RESTORE_STOCK_FAIL | BookID=%d | Qty=%d',
                    $item['product_id'], $item['quantity']
                ));
            }
        }
    }

    // -----------------------------------------------------------------------
    // STEP 1: Tạo PayPal Order — không thay đổi so với bản cũ
    // POST /paypal/create-order
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
            $amountStr   = $this->convertToPayPalAmount($orderData['totalAmount']);

            if ((float)$amountStr <= 0) {
                throw new RuntimeException('Số tiền thanh toán không hợp lệ.');
            }

            $payload = [
                'intent'         => 'CAPTURE',
                'purchase_units' => [[
                    'amount'      => [
                        'currency_code' => PAYPAL_CURRENCY === 'VND' ? 'USD' : PAYPAL_CURRENCY,
                        'value'         => $amountStr,
                    ],
                    'description' => 'RakiBookery - Đặt hàng sách',
                ]],
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
                $errorMsg    = $paypalError['message'] ?? 'Không thể tạo giao dịch PayPal.';
                throw new RuntimeException($errorMsg);
            }

            $result = json_decode($response, true);
            $_SESSION['paypal_checkout']['paypalOrderId'] = $result['id'];

            $this->responseJson(['success' => true, 'id' => $result['id']]);

        } catch (RuntimeException $e) {
            error_log('PayPalController::createOrder exception: ' . $e->getMessage());
            $this->responseJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // -----------------------------------------------------------------------
    // STEP 2: Capture PayPal Order
    // POST /paypal/capture-order
    // Body JSON: { "paypalOrderId": "..." }
    //
    // *** PHẦN ĐÃ SỬA — không đụng DB schema, chỉ thêm hoàn tiền tự động ***
    //
    // Luồng giữ nguyên thứ tự (capture trước, ghi DB sau) nhưng bổ sung:
    //   - Lấy captureId từ response PayPal
    //   - Nếu DB lỗi sau capture → gọi PayPal Refund API ngay lập tức
    //   - Nếu refund cũng lỗi → alert admin qua SNS thay vì chỉ ghi log
    // -----------------------------------------------------------------------
    public function captureOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseJson(['success' => false, 'message' => 'Method không hợp lệ'], 405);
            return;
        }

        $body          = json_decode(file_get_contents('php://input'), true);
        $paypalOrderId = trim($body['paypalOrderId'] ?? '');

        if (empty($paypalOrderId)) {
            $this->responseJson(['success' => false, 'message' => 'Thiếu PayPal Order ID.']);
            return;
        }

        // Idempotency guard
        if (isset($_SESSION['paypal_processed'][$paypalOrderId])) {
            $this->responseJson([
                'success' => true,
                'message' => 'Thanh toán PayPal thành công!',
                'orderId' => $_SESSION['paypal_processed'][$paypalOrderId],
            ]);
            return;
        }

        $checkout = $_SESSION['paypal_checkout'] ?? null;
        if (!$checkout || empty($checkout['cart'])) {
            $this->responseJson(['success' => false, 'message' => 'Phiên thanh toán hết hạn. Vui lòng thử lại.']);
            return;
        }

        if (($checkout['paypalOrderId'] ?? '') !== $paypalOrderId) {
            error_log(sprintf(
                'PAYPAL_ID_MISMATCH | SessionPayPalID=%s | ReceivedID=%s | UserID=%d',
                $checkout['paypalOrderId'] ?? 'none',
                $paypalOrderId,
                $checkout['userId'] ?? 0
            ));
            $this->responseJson(['success' => false, 'message' => 'Giao dịch không hợp lệ.']);
            return;
        }

        try {
            $accessToken = $this->getAccessToken();

            // ---------------------------------------------------------------
            // Gọi PayPal Capture API
            // ---------------------------------------------------------------
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

            // *** SỬA: Lưu captureId ngay sau khi capture thành công ***
            // captureId dùng để gọi Refund API nếu DB lỗi phía sau
            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            // Xác minh số tiền
            $capturedAmount   = (float) ($result['purchase_units'][0]['payments']['captures'][0]['amount']['value']         ?? 0);
            $capturedCurrency =          $result['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'] ?? '';
            $expectedAmount   = (float) $this->convertToPayPalAmount($checkout['totalAmount']);
            $expectedCurrency = PAYPAL_CURRENCY === 'VND' ? 'USD' : PAYPAL_CURRENCY;

            if ($capturedCurrency !== $expectedCurrency || abs($capturedAmount - $expectedAmount) > 0.01) {
                error_log(sprintf(
                    'PAYPAL_AMOUNT_MISMATCH | PayPalOrderID=%s | Captured=%s %s | Expected=%s %s | UserID=%d',
                    $paypalOrderId, $capturedAmount, $capturedCurrency,
                    $expectedAmount, $expectedCurrency, $checkout['userId']
                ));
                // Tiền đã bị thu nhưng số tiền sai → hoàn tiền ngay
                if ($captureId) {
                    $this->attemptAutoRefund($paypalOrderId, $captureId, $accessToken, $checkout['totalAmount']);
                }
                throw new RuntimeException('Số tiền giao dịch không khớp. Vui lòng liên hệ hỗ trợ.');
            }

            // ---------------------------------------------------------------
            // Capture thành công — ghi đơn hàng vào DB trong một transaction
            // *** SỬA: bắt lỗi DB và hoàn tiền tự động thay vì chỉ log ***
            // ---------------------------------------------------------------
            $orderId = null;
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
                    if (!$this->bookModel->decrementStock($item['product_id'], $item['quantity'])) {
                        throw new RuntimeException('Sản phẩm ID ' . $item['product_id'] . ' không đủ tồn kho.');
                    }

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

                error_log(sprintf(
                    'PAYPAL_DB_FAILURE | PayPalOrderID=%s | UserID=%d | Amount=%.2f | Error=%s',
                    $paypalOrderId,
                    $checkout['userId'],
                    $checkout['totalAmount'],
                    $dbEx->getMessage()
                ));

                // *** SỬA: Hoàn tiền tự động thay vì chỉ log và bắt user liên hệ ***
                if ($captureId) {
                    $this->attemptAutoRefund(
                        $paypalOrderId,
                        $captureId,
                        $accessToken,
                        $checkout['totalAmount']
                    );
                    throw new RuntimeException(
                        'Thanh toán thành công nhưng có lỗi khi lưu đơn hàng. ' .
                        'Hệ thống đã tự động hoàn tiền về tài khoản PayPal của bạn.'
                    );
                }

                throw new RuntimeException(
                    'Thanh toán thành công nhưng có lỗi khi lưu đơn hàng. ' .
                    'Vui lòng liên hệ hỗ trợ với mã giao dịch: ' . $paypalOrderId
                );
            }

            // Idempotency: lưu để chặn double-submit trong cùng session
            $_SESSION['paypal_processed'][$paypalOrderId] = $orderId;

            $this->snsService->notifyNewOrder($orderId, $checkout['totalAmount'], 'PayPal');

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
