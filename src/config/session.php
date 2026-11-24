<?php

$lifetime = 7200; // 2 giờ (giây)

ini_set('session.gc_maxlifetime', $lifetime);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'domain'   => '',
    'secure'   => env('APP_HTTPS', false),  // true khi chạy sau HTTPS (load balancer, CloudFront)
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra session có bị hết hạn không (dựa trên thời gian hoạt động cuối)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $lifetime) {
    // Session đã hết hạn — hủy và bắt đầu lại
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
