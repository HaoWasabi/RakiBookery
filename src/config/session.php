<?php

$lifetime = 7200; // 2 giờ (giây)

ini_set('session.gc_maxlifetime', $lifetime);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// *** THÊM MỚI: tránh race condition khi browser gửi nhiều request song song ***
// lazy_write = 1 nghĩa là PHP chỉ ghi session xuống file khi dữ liệu thực sự thay đổi,
// giảm nguy cơ 2 request ghi đè lên nhau cùng lúc
ini_set('session.lazy_write', 1);

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
    // Session đã hết hạn — hủy và bắt đầu lại
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
