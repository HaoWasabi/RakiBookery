<?php

/**
 * PayPal REST API v2 Configuration
 * Đọc credentials từ biến môi trường (.env) — không hardcode giá trị nhạy cảm ở đây.
 *
 * Để cấu hình:
 *   1. Mở file .env ở thư mục gốc dự án
 *   2. Điền PAYPAL_CLIENT_ID và PAYPAL_CLIENT_SECRET
 *   3. Lấy credentials tại: https://developer.paypal.com/ > Apps & Credentials
 */

define('PAYPAL_MODE',            env('PAYPAL_MODE',            'sandbox'));
define('PAYPAL_CLIENT_ID',       env('PAYPAL_CLIENT_ID',       ''));
define('PAYPAL_CLIENT_SECRET',   env('PAYPAL_CLIENT_SECRET',   ''));
define('PAYPAL_CURRENCY',        env('PAYPAL_CURRENCY',        'USD'));
define('PAYPAL_VND_TO_USD_RATE', (float) env('PAYPAL_VND_TO_USD_RATE', 25000));

define('PAYPAL_API_BASE', PAYPAL_MODE === 'live'
    ? 'https://api-m.paypal.com'
    : 'https://api-m.sandbox.paypal.com');
