<?php
require_once __DIR__ . '/BaseModel.php';

class Auth extends BaseModel
{
    public function login($email, $password)
    {
        try {
            // Lấy thông tin user theo email
            $stmt = $this->conn->prepare("SELECT UserID, Name, Email, Password, Role, Status FROM user WHERE Email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Không tìm thấy người dùng
            if (!$user) {
                return ['error' => 'invalid_credentials'];
            }

            // Tài khoản bị khóa
            if ((int) $user['Status'] === 0) {
                return ['error' => 'account_locked'];
            }

            // Kiểm tra mật khẩu
            if (!password_verify($password, $user['Password'])) {
                return ['error' => 'invalid_credentials'];
            }

            // Trả về thông tin user nếu đăng nhập thành công
            return $user;

        } catch (PDOException $e) {
            error_log("Lỗi đăng nhập: " . $e->getMessage());
            return ['error' => 'server_error'];
        }
    }

    public function register($fullName, $email, $password, $phone = null, $role = 'user')
    {
        try {
            // Mã hóa mật khẩu trước khi lưu
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Thêm người dùng vào bảng user
            $stmt = $this->conn->prepare("INSERT INTO user (Name, Email, Password, Phone, Role) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$fullName, $email, $hashedPassword, $phone, $role]);
        } catch (PDOException $e) {
            error_log("Lỗi đăng ký: " . $e->getMessage());
            return false;
        }
    }
    public function getUserById($userId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT UserID, Name, Email, Phone, Role FROM user WHERE UserID = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin người dùng: " . $e->getMessage());
            return false;
        }
    }

    //Kiểm tra xem người dùng có phải là admin hay không
    public function isAdmin($user)
    {
        return isset($user['Role']) && $user['Role'] === 'admin';
    }
    public function emailExists($email)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user WHERE Email = ?");
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra email: " . $e->getMessage());
            return false;
        }
    }
    public function phoneExists($phone)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user WHERE Phone = ?");
            $stmt->execute([$phone]);
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra số điện thoại: " . $e->getMessage());
            return false;
        }
    }
}