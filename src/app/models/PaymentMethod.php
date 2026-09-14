<?php
require_once __DIR__ . '/../../config/database.php';

class PaymentMethod
{
    private $conn;
    private $table = "paymentmethod";

    public function __construct()
    {
        try {
            $this->conn = Database::getInstance()->getConnection();
        } catch (PDOException $e) {
            error_log("Lỗi kết nối DB: " . $e->getMessage());
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }
    public function getAllPaymentMethods()
    {
        try {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy tất cả phương thức thanh toán: " . $e->getMessage());
            return [];
        }
    }
}
