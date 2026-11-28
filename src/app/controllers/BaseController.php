<?php

class BaseController
{
    // Trả JSON và kết thúc
    protected function responseJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        // *** THÊM: CloudFront không được cache JSON response ***
        // Đặc biệt quan trọng với 401 — nếu cache thì mọi request sau
        // (kể cả khi đã đăng nhập lại) vẫn nhận 401 cũ từ CloudFront.
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        echo json_encode($data);
        exit;
    }

    // Kiểm tra phương thức POST
    protected function requirePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->responseJson([
                'success' => false,
                'message' => 'Phương thức không hợp lệ'
            ], 405);
        }
    }

    // Đọc dữ liệu từ body JSON hoặc form
    protected function getRequestData()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $data = $_POST;
        }
        return $data;
    }

    protected function redirect($url)
    {
        // *** THÊM: CloudFront không được cache redirect ***
        // Nếu cache redirect 302 về /admin/login thì admin đăng nhập
        // rồi truy cập /admin/dashboard vẫn bị redirect về login.
        if (!headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }
        header('Location: ' . $url);
        exit;
    }

    // Kiểm tra quyền admin cho các API endpoint (trả JSON thay vì redirect)
    protected function requireAdmin()
    {
        if (!isset($_SESSION['UserID']) || !isset($_SESSION['Role']) || $_SESSION['Role'] !== 'admin') {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này'
            ], 401);
        }
    }
}
