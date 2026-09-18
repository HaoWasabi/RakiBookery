<?php

/**
 * Đọc file .env và nạp vào $_ENV + getenv()
 * Không dùng thư viện ngoài — parse thủ công, đủ dùng cho dự án nhỏ.
 */
function loadEnv(string $envFilePath): void
{
    if (!file_exists($envFilePath)) {
        // Không tìm thấy .env — có thể đang chạy trên server dùng biến môi trường hệ thống
        return;
    }

    $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Bỏ qua comment
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Tách KEY=VALUE (chỉ tách lần đầu để VALUE có thể chứa '=')
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        [$key, $value] = $parts;
        $key   = trim($key);
        $value = trim($value);

        // Bỏ dấu nháy bao quanh value nếu có ("value" hoặc 'value')
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Chỉ set nếu chưa có (ưu tiên biến môi trường hệ thống)
        if (!isset($_ENV[$key]) && getenv($key) === false) {
            $_ENV[$key]  = $value;
            putenv("{$key}={$value}");
        }
    }
}

/**
 * Lấy giá trị biến môi trường, trả về $default nếu không tồn tại.
 */
function env(string $key, mixed $default = null): mixed
{
    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    // Tự động convert string sang bool/null
    return match (strtolower((string)$value)) {
        'true',  '1', 'yes' => true,
        'false', '0', 'no'  => false,
        'null', 'none'      => null,
        default             => $value,
    };
}
