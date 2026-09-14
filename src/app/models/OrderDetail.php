<?php
require_once __DIR__ . '/../../config/database.php';

class OrderDetail
{
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = Database::getInstance()->getConnection();
        } catch (PDOException $e) {
            error_log("Lỗi kết nối DB: " . $e->getMessage());
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }
    public function createOrderDetail($orderId, $productId, $quantity, $price) {
        try {
            $stmt = $this->conn->prepare("
            INSERT INTO `orderdetail` (OrderID, ProductID, Quantity, Price) 
            VALUES (?, ?, ?, ?)
        ");
            return $stmt->execute([$orderId, $productId, $quantity, $price]);
        } catch (PDOException $e) {
            error_log("Lỗi tạo chi tiết  đơn hàng: " . $e->getMessage());
            return false;
        }
    }
}
