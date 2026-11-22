<?php

/**
 * SnsService — gửi thông báo qua API Gateway → Lambda → SNS Topic.
 *
 * Thay vì gọi SNS trực tiếp bằng AWS SDK, service này POST lên
 * HTTP endpoint của API Gateway (đã deploy trong stack raki-cdn-monitoring).
 * Lambda NotifyLambda nhận request và publish lên SNS Topic.
 *
 * Lợi ích: App server không cần giữ AWS credentials cho SNS,
 * chỉ cần biết URL API Gateway.
 *
 * Biến môi trường cần có:
 *   NOTIFY_API_URL = https://<id>.execute-api.ap-southeast-1.amazonaws.com/notify
 *
 * Cách dùng:
 *   $sns = new SnsService();
 *   $sns->notifyNewOrder($orderId, $totalAmount, 'PayPal');
 *   $sns->notifyStatusChanged($orderId, 'confirmed');
 *   $sns->publish('Subject', 'Nội dung tùy ý');
 */
class SnsService
{
    private string $apiUrl;
    private bool $enabled;

    public function __construct()
    {
        $this->apiUrl  = env('NOTIFY_API_URL', '');
        $this->enabled = !empty($this->apiUrl);
    }

    /**
     * Gửi thông báo khi có đơn hàng mới.
     *
     * @param int|string $orderId     ID đơn hàng vừa tạo
     * @param float      $totalAmount Tổng tiền đơn hàng (VND)
     * @param string     $method      Phương thức thanh toán (COD, PayPal, ...)
     */
    public function notifyNewOrder($orderId, float $totalAmount, string $method = 'COD'): void
    {
        $formatted = number_format($totalAmount, 0, '.', ',');
        $this->publish(
            subject: "[RakiBookery] Đơn hàng mới #{$orderId}",
            message: "Có đơn hàng mới trên RakiBookery.\n\n"
                   . "- Mã đơn hàng : #{$orderId}\n"
                   . "- Tổng tiền   : {$formatted} VND\n"
                   . "- Thanh toán  : {$method}\n"
                   . "- Thời gian   : " . date('d/m/Y H:i:s')
        );
    }

    /**
     * Gửi thông báo khi admin cập nhật trạng thái đơn hàng.
     *
     * @param int|string $orderId ID đơn hàng
     * @param string     $status  Trạng thái mới
     */
    public function notifyStatusChanged($orderId, string $status): void
    {
        $statusLabel = match ($status) {
            'confirmed'         => 'Đã xác nhận',
            'processing'        => 'Đang xử lý',
            'delivered_success' => 'Giao hàng thành công',
            'canceled'          => 'Đã hủy',
            default             => $status,
        };

        $this->publish(
            subject: "[RakiBookery] Đơn #{$orderId} — {$statusLabel}",
            message: "Trạng thái đơn hàng đã được cập nhật.\n\n"
                   . "- Mã đơn hàng    : #{$orderId}\n"
                   . "- Trạng thái mới : {$statusLabel}\n"
                   . "- Thời gian      : " . date('d/m/Y H:i:s')
        );
    }

    /**
     * POST lên API Gateway endpoint → Lambda → SNS.
     * Silent-fail: nếu chưa cấu hình hoặc gọi lỗi, chỉ ghi error_log.
     *
     * Body gửi đi (JSON):
     *   { "subject": "...", "message": "..." }
     *
     * Lambda NotifyLambda đọc body.subject và body.message để publish SNS.
     *
     * @param string $subject Tiêu đề (hiển thị trong email subject)
     * @param string $message Nội dung
     */
    public function publish(string $subject, string $message): void
    {
        if (!$this->enabled) {
            return; // NOTIFY_API_URL chưa cấu hình → bỏ qua, không crash app
        }

        $payload = json_encode([
            'subject' => $subject,
            'message' => $message,
        ]);

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 5,       // không để app chờ lâu nếu Lambda chậm
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            error_log("SnsService::publish cURL error: {$curlErr}");
        } elseif ($httpCode !== 200) {
            error_log("SnsService::publish failed HTTP {$httpCode}: {$response}");
        }
    }
}
