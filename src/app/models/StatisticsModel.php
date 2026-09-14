<?php
require_once __DIR__ . '/../../config/database.php';

class StatisticModel
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

    // Hàm lấy top 5 khách hàng có tổng tiền mua cao nhất trong khoảng thời gian
    public function getTopCustomers($startDate, $endDate)
    {
        try {
            $query = "
                SELECT u.UserID, u.Name, SUM(o.TotalAmount) AS TotalSpent
                FROM `Order` o
                JOIN User u ON o.UserID = u.UserID
                WHERE o.Status = 'delivered_success' 
                AND DATE(o.OrderDate) BETWEEN ? AND ?
                GROUP BY u.UserID, u.Name
                ORDER BY TotalSpent DESC
                LIMIT 5;
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$startDate, $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách khách hàng: " . $e->getMessage());
            return [];
        }
    }

    // Hàm lấy danh sách đơn hàng của một khách hàng trong khoảng thời gian

}
