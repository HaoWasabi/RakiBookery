<?php

// Định nghĩa BASE_PATH an toàn cho cả Local và Docker Container (/var/www/html)
if (!defined('BASE_PATH')) {
    
    $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
    
    if (file_exists($autoloadPath)) {
        define('BASE_PATH', dirname($autoloadPath));
    } else {
        // 2. Fallback duyệt ngược cây thư mục lên tới 4 cấp để tìm vendor/autoload.php
        $dir = __DIR__;
        while ($dir !== dirname($dir)) {
            if (file_exists($dir . '/vendor/autoload.php')) {
                define('BASE_PATH', $dir);
                break;
            }
            $dir = dirname($dir);
        }
    }
}

// Kiểm tra và nhúng autoloader của Composer
if (defined('BASE_PATH') && file_exists(BASE_PATH . '/vendor/autoload.php')) {
    require_once BASE_PATH . '/vendor/autoload.php';
} else {
    error_log('[ERROR] S3Service: Không tìm thấy file vendor/autoload.php tại BASE_PATH: ' . (defined('BASE_PATH') ? BASE_PATH : 'undefined'));
}

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Service
{
    private S3Client $client;
    private string $bucket;
    private string $folder;
    private string $region;

    public function __construct()
    {
        $this->region = env('AWS_REGION', 'ap-southeast-1');
        $this->bucket = env('AWS_BUCKET', '');
        $this->folder = trim(env('AWS_S3_FOLDER', 'uploads'), '/');

        $this->client = new S3Client([
            'version'                 => 'latest',
            'region'                  => $this->region,
            'credentials'             => [
                'key'    => env('AWS_ACCESS_KEY_ID', ''),
                'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
            ],
            // Sử dụng Virtual-hosted-style URL (chuẩn khuyến nghị của AWS SDK v3)
            'use_path_style_endpoint' => false,
        ]);
    }

    /**
     * Upload một file ảnh lên S3, đặt tên file theo BookID nếu có.
     * Key sẽ có dạng: <folder>/<bookId>.<ext> (ví dụ: uploads/44.jpg)
     *
     * @param array    $file    Phần tử lấy từ $_FILES
     * @param int|null $bookId  BookID dùng để đặt tên file
     * @return string|false     Public URL trên S3, hoặc false nếu thất bại
     */
    public function uploadImage(array $file, ?int $bookId = null): string|false
    {
        // Validate: Kiểm tra định dạng ảnh thật
        if (getimagesize($file['tmp_name']) === false) {
            return false;
        }

        // Validate: Dung lượng tối đa 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Validate: Chỉ cho phép các định dạng ảnh hợp lệ
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes)) {
            return false;
        }

        // Đặt tên file theo BookID nếu có, hoặc tạo tên ngẫu nhiên uniqid
        $filename = ($bookId !== null) 
            ? "{$bookId}.{$ext}" 
            : uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
        $key = $this->folder . '/' . $filename;

        // Nếu đặt tên theo BookID: Xóa các file cũ cùng BookID khác extension trên S3
        if ($bookId !== null) {
            foreach ($allowedTypes as $oldExt) {
                $oldKey = $this->folder . '/' . $bookId . '.' . $oldExt;
                if ($oldKey !== $key) {
                    try {
                        $this->client->deleteObject([
                            'Bucket' => $this->bucket, 
                            'Key'    => $oldKey
                        ]);
                    } catch (\Exception $e) {
                        // Bỏ qua nếu file cũ không tồn tại
                    }
                }
            }
        }

        try {
            $this->client->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $key,
                'SourceFile'  => $file['tmp_name'],
                'ContentType' => $this->guessMimeType($ext),
            ]);

            return "https://{$this->bucket}.s3.{$this->region}.amazonaws.com/{$key}";
        } catch (AwsException $e) {
            error_log('[S3 upload FAILED] ' . $e->getAwsErrorCode() . ': ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('[S3 upload EXCEPTION] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa một object khỏi S3 dựa theo URL đầy đủ.
     *
     * @param string $url  URL đầy đủ (vd: https://bucket.s3.region.amazonaws.com/uploads/xxx.jpg)
     * @return bool
     */
    public function deleteByUrl(string $url): bool
    {
        $prefix = "https://{$this->bucket}.s3.{$this->region}.amazonaws.com/";
        if (!str_starts_with($url, $prefix)) {
            return false;
        }

        $key = substr($url, strlen($prefix));

        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('S3 delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo presigned URL cho phép truy cập tạm thời vào object trong Private Bucket.
     *
     * @param string $key        Key của object trong bucket
     * @param int    $expiresIn  Thời gian hết hạn (tính bằng giây, mặc định 1 giờ)
     * @return string
     */
    public function getPresignedUrl(string $key, int $expiresIn = 3600): string
    {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $key,
        ]);

        $request = $this->client->createPresignedRequest($cmd, '+' . $expiresIn . ' seconds');
        return (string) $request->getUri();
    }

    /**
     * Trích xuất key từ URL S3 đầy đủ và tạo presigned URL.
     *
     * @param string $url
     * @return string|null
     */
    public function presignFromUrl(string $url): ?string
    {
        $prefix = "https://{$this->bucket}.s3.{$this->region}.amazonaws.com/";
        if (!str_starts_with($url, $prefix)) {
            return null;
        }
        $key = urldecode(substr($url, strlen($prefix)));
        return $this->getPresignedUrl($key);
    }

    /**
     * Đoán MIME Type từ định dạng đuôi file.
     */
    private function guessMimeType(string $ext): string
    {
        return match (strtolower($ext)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            default       => 'application/octet-stream',
        };
    }
}