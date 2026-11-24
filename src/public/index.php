<?php

// Load biến môi trường từ .env (phải chạy đầu tiên)
require_once "../config/env.php";

// Thư mục gốc project (chứa vendor/, .env, composer.json)
// Local: RakiBookery/  |  Docker: /var/www/html/
define('BASE_PATH', dirname(__DIR__, 2));

loadEnv(BASE_PATH . '/.env');

// Hiển thị lỗi chỉ khi APP_DEBUG=true (không bao giờ bật trên production)
error_reporting(E_ALL);
ini_set('display_errors', env('APP_DEBUG', false) ? 1 : 0);

// Hiển thị lỗi chi tiết chỉ ở môi trường dev, ẩn ở prod/staging
$appEnv = env('APP_ENV', 'prod');
if ($appEnv === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);  // Ẩn lỗi với người dùng, nhưng vẫn báo qua SNS
    ini_set('log_errors', 1);
}

// Global error/exception handler → gửi SNS khi có lỗi hệ thống nghiêm trọng
// (phải load sau env vì handler cần hàm env())
require_once "../config/error_handler.php";

// Khởi tạo session
require_once "../config/session.php";

// Kết nối database
require_once "../config/database.php";

// Cấu hình S3 và các hàm tiện ích ảnh
require_once "../config/s3.php";

// Định tuyến (sử dụng routes.php để xử lý request)
require_once "../routes/routes.php";
