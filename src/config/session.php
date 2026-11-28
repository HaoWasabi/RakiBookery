<?php

$lifetime = 7200; // 2 giờ (giây)

ini_set('session.gc_maxlifetime', $lifetime);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.lazy_write', 1);

// *** SỬA CHÍNH: chỉ định rõ nơi lưu session và tạo thư mục nếu chưa có ***
// Trong Docker php:8.2-apache, session.save_path mặc định có thể là
// /var/lib/php/sessions — thư mục này không thuộc www-data nên PHP không ghi
// được → session trống ở request tiếp theo → checkAdminAuth() redirect về login.
// Giải pháp: lưu session trong /tmp/php_sessions thuộc quyền www-data chắc chắn.
$sessionSavePath = '/tmp/php_sessions';

if (!is_dir($sessionSavePath)) {
    mkdir($sessionSavePath, 0700, true);
}

ini_set('session.save_path', $sessionSavePath);

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'domain'   => '',
    'secure'   => env('APP_HTTPS', false),
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra session có bị hết hạn không (dựa trên thời gian hoạt động cuối)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $lifetime) {
    session_unset();
    session_destroy();
    session_start();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
