<?php

require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

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
            // Dùng virtual-hosted-style URL (mặc định AWS SDK v3).
            // Đặt tường minh để tránh path-style bị deprecated theo AWS policy.
            'use_path_style_endpoint' => false,
        ]);
    }

    /**
     * Upload một file ảnh lên S3, đặt tên file theo BookID.
     * Key sẽ là: <folder>/<bookId>.<ext>  (ví dụ: media/44.jpg)
     * Nếu $bookId null, dùng uniqid() như cũ.
     *
     * @param array    $file    Phần tử từ $_FILES
     * @param int|null $bookId  BookID để đặt tên file
     * @return string|false     Public URL trên S3, hoặc false nếu thất bại
     */
    public function uploadImage(array $file, ?int $bookId = null): string|false
    {
        // Validate: phải là ảnh thật
        if (getimagesize($file['tmp_name']) === false) {
            return false;
        }

        // Validate: tối đa 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Validate: chỉ cho phép các định dạng ảnh phổ biến
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedTypes)) {
            return false;
        }

        // Đặt tên file theo BookID nếu có, không thì dùng uniqid
        $filename = ($bookId !== null) ? "{$bookId}.{$ext}" : uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($file['name']));
        $key = $this->folder . '/' . $filename;

        // Nếu đặt tên theo BookID: xóa file cũ cùng key (mọi extension) trước khi upload
        if ($bookId !== null) {
            foreach ($allowedTypes as $oldExt) {
                $oldKey = $this->folder . '/' . $bookId . '.' . $oldExt;
                if ($oldKey !== $key) {
                    try {
                        $this->client->deleteObject(['Bucket' => $this->bucket, 'Key' => $oldKey]);
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
     * Xóa một object khỏi S3 theo URL đầy đủ.
     * Dùng khi thay ảnh cũ bằng ảnh mới để tránh file rác trên bucket.
     *
     * @param string $url  URL đầy đủ của ảnh cũ (vd: https://bucket.s3.region.amazonaws.com/uploads/xxx.jpg)
     * @return bool
     */
    public function deleteByUrl(string $url): bool
    {
        $prefix = "https://{$this->bucket}.s3.{$this->region}.amazonaws.com/";
        if (!str_starts_with($url, $prefix)) {
            return false; // URL không thuộc bucket này (ví dụ ảnh default hoặc URL local cũ)
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
     * Tạo presigned URL để browser có thể đọc ảnh từ private bucket.
     * URL có hiệu lực trong $expiresIn giây (mặc định 1 giờ).
     *
     * @param string $key     Object key trong bucket (ví dụ: media/abc_img.jpg)
     * @param int    $expiresIn Thời gian hiệu lực tính bằng giây
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
     * Từ URL đầy đủ của S3 object, trích ra key rồi tạo presigned URL.
     * Trả về null nếu URL không thuộc bucket này.
     *
     * @param string $url  URL dạng https://<bucket>.s3.<region>.amazonaws.com/<key>
     * @return string|null
     */
    public function presignFromUrl(string $url): ?string
    {
        $prefix = "https://{$this->bucket}.s3.{$this->region}.amazonaws.com/";
        if (!str_starts_with($url, $prefix)) {
            return null;
        }
        $key = substr($url, strlen($prefix));
        // URL có thể dùng '+' để encode space (application/x-www-form-urlencoded).
        // AWS SDK cần key thật (không encoded), nên decode trước khi presign.
        $key = urldecode($key);
        return $this->getPresignedUrl($key);
    }
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
