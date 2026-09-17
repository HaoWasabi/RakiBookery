<?php
require_once __DIR__ . '/../../config/database.php';

class Promo
{
    private $conn;
    private $table = "Promo";

    public function __construct()
    {
        try {
            $this->conn = Database::getInstance()->getConnection();
        } catch (PDOException $e) {
            error_log("Lỗi kết nối DB: " . $e->getMessage());
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }

    public function getAll()
    {
        try {
            $query = "SELECT * FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy tất cả khuyến mãi: " . $e->getMessage());
            return [];
        }
    }

    public function getPromoById($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE PromoID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy khuyến mãi theo ID: " . $e->getMessage());
            return null;
        }
    }

    public function create($name, $discounted, $datecreated)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (Name, Discounted, DateCreated) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$name, $discounted, $datecreated]);
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo khuyến mãi: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $name ,$discounted, $datecreated, $status)
    {
        try {
            $query = "UPDATE " . $this->table . " SET Name = ?, Discounted = ?, DateCreated = ?, Status = ? WHERE PromoID = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$name, $discounted, $datecreated, $status, $id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật khuyến mãi: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE PromoID = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi xóa khuyến mãi: " . $e->getMessage());
            return false;
        }
    }

    public function addPromo($data)
    {
        try {
            $name = $data['Name'] ?? '';
            $discounted = $data['Discounted'] ?? '';
            $datecreated = $data['DateCreated'] ?? '';

            if (empty($name)) {
                return false;
            }

            return $this->create($name, $discounted, $datecreated);
        } catch (PDOException $e) {
            error_log("Lỗi khi thêm khuyến mãi: " . $e->getMessage());
            return false;
        }
    }

    public function updatePromo($id, $data)
    {
        try {
            $name = $data['Name'] ?? '';
            $discounted = $data['Discounted'] ?? '';
            $datecreated = $data['DateCreated'] ?? '';
            $status = $data['Status'] ?? '';

            if (empty($name)) {
                return false;
            }

            return $this->update($id, $name, $discounted, $datecreated, $status);
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật khuyến mãi: " . $e->getMessage());
            return false;
        }
    }

    // Check if a promo name exists
    public function promoExists($name)
    {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE Name = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra tên khuyến mãi tồn tại: " . $e->getMessage());
            return false;
        }
    }

    // Check if a promo name exists for other categories (used when updating)
    public function promoExistsForOtherPromo($name, $id)
    {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE Name = ? AND PromoID != ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name, $id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra tên khuyến mãi tồn tại: " . $e->getMessage());
            return false;
        }
    }
}
