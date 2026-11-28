<?php

$lifetime = 7200; // 2 giờ (giây)

ini_set('session.gc_maxlifetime', $lifetime);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.lazy_write', 1);

$sessionSavePath = '/tmp/php_sessions';
if (!is_dir($sessionSavePath)) {
    mkdir($sessionSavePath, 0700, true);
}
ini_set('session.save_path', $sessionSavePath);

// Đổi tên cookie tránh CloudFront managed policy tự strip PHPSESSID
ini_set('session.name', 'raki_sid');

// *** SỬA: đọc HTTPS từ env đúng cách ***
// APP_HTTPS=false trong ECS nhưng app thực tế chạy sau CloudFront HTTPS.
// Dùng X-Forwarded-Proto để detect thực tế thay vì tin vào APP_HTTPS.
// Nếu request đến PHP qua header X-Forwarded-Proto: https thì đang chạy HTTPS.
$isHttps = env('APP_HTTPS', false)
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_set_cookie_params([
    'lifetime' => $lifetime,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isHttps,
    // *** SỬA: SameSite=None khi HTTPS để cookie đi qua CloudFront ***
    // SameSite=Lax + secure=false → browser block cookie trên HTTPS.
    // SameSite=None + secure=true → cookie luôn được gửi kể cả cross-origin.
    'samesite' => $isHttps ? 'None' : 'Lax',
    'httponly' => true,
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// *** THÊM: báo CloudFront không cache response có session ***
// Nếu không có header này, CloudFront cache redirect 302 về /admin/login
// → admin đăng nhập rồi vẫn bị redirect về login vì nhận cached response.
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Vary: Cookie');
}

// Kiểm tra session hết hạn theo last_activity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $lifetime) {
    session_unset();
    session_destroy();
    session_start();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
