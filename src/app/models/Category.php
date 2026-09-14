<?php
require_once __DIR__ . '/../../config/database.php';

class Category
{
    private $conn;
    private $table = "Category";

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
            error_log("Lỗi khi lấy tất cả danh mục: " . $e->getMessage());
            return [];
        }
    }

    public function getCategoryById($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE CategoryID = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy danh mục theo ID: " . $e->getMessage());
            return null;
        }
    }

    public function create($name, $description)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (Name, Description) VALUES (?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$name, $description]);
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo danh mục: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $name, $description)
    {
        try {
            $query = "UPDATE " . $this->table . " SET Name = ?, Description = ? WHERE CategoryID = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$name, $description, $id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật danh mục: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE CategoryID = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi xóa danh mục: " . $e->getMessage());
            return false;
        }
    }

    public function addCategory($data)
    {
        try {
            $name = $data['Name'] ?? '';
            $description = $data['Description'] ?? '';

            if (empty($name)) {
                return false;
            }

            return $this->create($name, $description);
        } catch (PDOException $e) {
            error_log("Lỗi khi thêm danh mục: " . $e->getMessage());
            return false;
        }
    }

    public function updateCategory($id, $data)
    {
        try {
            $name = $data['Name'] ?? '';
            $description = $data['Description'] ?? '';

            if (empty($name)) {
                return false;
            }

            return $this->update($id, $name, $description);
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật danh mục: " . $e->getMessage());
            return false;
        }
    }

    // Check if a category name exists
    public function categoryExists($name)
    {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE Name = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra tên danh mục tồn tại: " . $e->getMessage());
            return false;
        }
    }

    // Check if a category name exists for other categories (used when updating)
    public function categoryExistsForOtherCategory($name, $id)
    {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE Name = ? AND CategoryID != ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$name, $id]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra tên danh mục tồn tại: " . $e->getMessage());
            return false;
        }
    }
}
