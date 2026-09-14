<?php

$lifetime = 1800000; // set thời gian 30 phút(1800 giây)

ini_set('session.gc_maxlifetime', $lifetime); // Set thời gian sống của session trên server

session_set_cookie_params([
    'lifetime' => $lifetime, // Set thời gian sống của cookie trên client
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
