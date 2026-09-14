<?php
require_once __DIR__ . '/../../config/database.php';

class Address
{
    private $conn;
    private $table = "address";

    public function __construct()
    {
        try {
            $database = new Database();
            $this->conn = $database->getConnection();
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
            error_log("Lỗi khi lấy tất cả địa chỉ: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE AddressID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy địa chỉ theo ID: " . $e->getMessage());
            return null;
        }
    }

    public function getUserAddress($userId)
    {
        try {
            $query = "SELECT a.AddressID, a.Address, a.City, a.District, a.Ward 
                      FROM User u 
                      JOIN Address a ON u.AddressID = a.AddressID
                      WHERE u.UserID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy địa chỉ người dùng: " . $e->getMessage());
            return null;
        }
    }

    public function create($address, $city, $district, $ward)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (Address, City, District, Ward) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$address, $city, $district, $ward]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo địa chỉ mới: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $address, $city, $district, $ward)
    {
        try {
            $query = "UPDATE " . $this->table . " SET Address = ?, City = ?, District = ?, Ward = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$address, $city, $district, $ward, $id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật địa chỉ: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi xóa địa chỉ: " . $e->getMessage());
            return false;
        }
    }
}
