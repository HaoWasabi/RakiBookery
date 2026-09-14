<?php

class BaseController
{
    // Trả JSON và kết thúc
    protected function responseJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
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
            ], 405); // 405 Method Not Allowed
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
        header('Location: ' . $url);
        exit;
    }
}