<?php

/**
 * Cấu hình và các hàm tiện ích tương tác AWS S3.
 *
 * Cung cấp 3 hàm chính:
 *   - s3_upload(array $file): string|false
 *   - s3_delete(string $url): bool
 *   - s3_url(string|null $imageURL, string $fallback = IMG_FALLBACK): string
 *
 * File này là "cổng vào" duy nhất cho mọi thao tác ảnh — controller và view
 * chỉ cần require file này, không cần biết chi tiết S3Client hay S3Service.
 *
 * File an toàn khi bị require nhiều lần (dùng defined-guard cho constants,
 * function_exists-guard cho các hàm).
 */

require_once __DIR__ . '/../app/services/S3Service.php';

// ── Hằng cấu hình S3 ───────────────────────────────────────────────────────

if (!defined('S3_REGION')) {
    define('S3_REGION',   env('AWS_REGION',     'ap-southeast-1'));
}
if (!defined('S3_BUCKET')) {
    define('S3_BUCKET',   env('AWS_BUCKET',     ''));
}
if (!defined('S3_FOLDER')) {
    define('S3_FOLDER',   trim(env('AWS_S3_FOLDER', 'uploads'), '/'));
}
if (!defined('S3_BASE_URL')) {
    define('S3_BASE_URL', 'https://' . S3_BUCKET . '.s3.' . S3_REGION . '.amazonaws.com');
}

/** Ảnh mặc định khi sách chưa có ảnh hoặc URL không hợp lệ. */
if (!defined('IMG_FALLBACK')) {
    define('IMG_FALLBACK', '/img/img-not-available.png');
}

// ── Singleton S3Service (nội bộ) ───────────────────────────────────────────

if (!function_exists('_s3')) {
    /**
     * Trả về instance S3Service duy nhất (lazy singleton).
     * Chỉ dùng nội bộ trong file này.
     */
    function _s3(): S3Service
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new S3Service();
        }
        return $instance;
    }
}

// ── Hàm công khai ──────────────────────────────────────────────────────────

if (!function_exists('s3_upload')) {
    /**
     * Upload một file ảnh lên S3, đặt tên theo BookID nếu cung cấp.
     * Nếu S3 thất bại, fallback lưu local vào public/uploads/.
     *
     * @param  array    $file    Phần tử từ $_FILES
     * @param  int|null $bookId  BookID để đặt tên file (vd: 44.jpg)
     * @return string|false      URL ảnh, hoặc false khi thất bại hoàn toàn
     */
    function s3_upload(array $file, ?int $bookId = null): string|false
    {
        $url = _s3()->uploadImage($file, $bookId);

        if ($url !== false) {
            return $url;
        }

        // ── Fallback: lưu local khi S3 không khả dụng ──────────────────
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes)) {
            return false;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        // Đặt tên theo BookID nếu có
        $filename = ($bookId !== null)
            ? "{$bookId}.{$ext}"
            : uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));

        // Xóa file cũ cùng BookID với extension khác
        if ($bookId !== null) {
            foreach ($allowedTypes as $oldExt) {
                $oldPath = $uploadDir . $bookId . '.' . $oldExt;
                if (file_exists($oldPath) && $oldExt !== $ext) {
                    unlink($oldPath);
                }
            }
        }

        $destPath = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            error_log('[S3 fallback] Lưu local: /uploads/' . $filename);
            return '/uploads/' . $filename;
        }

        return false;
    }
}

if (!function_exists('s3_delete')) {
    /**
     * Xóa một ảnh khỏi S3 theo URL đầy đủ.
     *
     * Gọi khi thay ảnh cũ bằng ảnh mới để tránh file rác trên bucket.
     * Tự động bỏ qua (trả về false) nếu URL không thuộc bucket hiện tại
     * — ví dụ ảnh local hay ảnh mặc định IMG_FALLBACK.
     *
     * Ví dụ:
     *   s3_delete($book['ImageURL']);
     *
     * @param  string $url  URL đầy đủ của ảnh cần xóa
     * @return bool         true nếu xóa thành công, false nếu bỏ qua hoặc lỗi
     */
    function s3_delete(string $url): bool
    {
        return _s3()->deleteByUrl($url);
    }
}

if (!function_exists('s3_url')) {
    /**
     * Trả về URL hiển thị ảnh, tự động fallback về IMG_FALLBACK nếu rỗng.
     *
     * - Nếu $imageURL là URL S3 (https://bucket.s3...): tạo presigned URL để
     *   browser đọc được từ private bucket. Presigned URL cache trong $_SESSION
     *   để tránh tạo lại mỗi request.
     * - Nếu $imageURL là đường dẫn local (/uploads/...) hoặc bất kỳ URL khác:
     *   trả về nguyên bản.
     * - Nếu rỗng: trả về $fallback.
     *
     * @param  string|null $imageURL
     * @param  string      $fallback
     * @return string
     */
    function s3_url(?string $imageURL, string $fallback = IMG_FALLBACK): string
    {
        if (empty($imageURL)) {
            return $fallback;
        }

        // Chỉ presign URL thuộc S3 bucket này
        if (str_starts_with($imageURL, S3_BASE_URL . '/')) {
            // Cache presigned URL trong session để không tạo lại mỗi lần render
            $cacheKey = 's3_presigned_' . md5($imageURL);
            if (!empty($_SESSION[$cacheKey]) && !empty($_SESSION[$cacheKey . '_exp']) && $_SESSION[$cacheKey . '_exp'] > time() + 60) {
                return $_SESSION[$cacheKey];
            }

            $presigned = _s3()->presignFromUrl($imageURL);
            if ($presigned) {
                $_SESSION[$cacheKey]          = $presigned;
                $_SESSION[$cacheKey . '_exp'] = time() + 3600;
                return $presigned;
            }

            // Presign thất bại (credentials sai, bucket không khớp...) → fallback
            return $fallback;
        }

        return $imageURL;
    }
}
