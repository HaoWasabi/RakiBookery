<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new Auth();
    }

    public function login()
    {
        $this->requirePost();

        $data = $this->getRequestData();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->authModel->login($email, $password);

        // Kiểm tra nếu là lỗi
        if (isset($user['error'])) {
            $message = match ($user['error']) {
                'invalid_credentials' => 'Email hoặc mật khẩu không chính xác',
                'account_locked' => 'Tài khoản của bạn đã bị khóa',
                default => 'Đã xảy ra lỗi, vui lòng thử lại'
            };

            $this->responseJson([
                'success' => false,
                'message' => $message
            ]);
            return;
        }

        $isAdmin = $this->authModel->isAdmin($user);

        if ($_SERVER['REQUEST_URI'] === "/admin/auth/login") {
            if (!$isAdmin) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không chính xác'
                ]);
            }
        } else if ($_SERVER['REQUEST_URI'] === "/auth/login") {
            if ($isAdmin) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Email hoặc mật khẩu không chính xác'
                ]);
                return;
            }
        }

        // Lưu session nếu đăng nhập thành công
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Name'] = $user['Name'];
        $_SESSION['Email'] = $user['Email'];
        $_SESSION['Role'] = $user['Role'];

        // Check for cart cookie and set a flag for client-side to sync
        if($_SESSION['Role'] == 'user'){
            $hasCartCookie = false;
            $cartCookieName = 'user_cart_' . $user['UserID'];
            if (isset($_COOKIE[$cartCookieName])) {
                $hasCartCookie = true;
                $_SESSION['has_cart_cookie'] = true;
            }
    
            $this->responseJson([
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'has_cart_cookie' => $hasCartCookie
            ]);
        } else {
            $this->responseJson([
                'success' => true,
                'message' => 'Đăng nhập thành công'
            ]);
        }
    }

    public function register()
    {
        // Kiểm tra phương thức POST
        $this->requirePost();

        // Lấy dữ liệu từ request
        $data = $this->getRequestData();
        $fullName = $data['fullName'] ?? '';
        $email = $data['email'] ?? '';
        $phone = $data['phone'] ?? '';
        $password = $data['password'] ?? '';

        // Kiểm tra email đã tồn tại chưa
        if ($this->authModel->emailExists($email)) {
            $this->responseJson([
                'success' => false,
                'message' => 'Email này đã được sử dụng, vui lòng nhập email khác'
            ]);
            return;
        }

        // Kiểm tra số điện thoại đã tồn tại chưa
        if ($phone && $this->authModel->phoneExists($phone)) {
            $this->responseJson([
                'success' => false,
                'message' => 'Số điện thoại này đã được sử dụng, vui lòng nhập số khác'
            ]);
            return;
        }

        // Đăng ký người dùng mới
        $result = $this->authModel->register($fullName, $email, $password, $phone);

        if ($result) {
            // Đăng nhập người dùng sau khi đăng ký thành công
            $user = $this->authModel->login($email, $password);

            if ($user) {
                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['Name'] = $user['Name'];
                $_SESSION['Role'] = $user['Role'];
            }

            $this->responseJson([
                'success' => true,
                'message' => 'Đăng ký thành công'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Đăng ký thất bại, vui lòng thử lại sau'
            ]);
        }
    }

    public function logout()
    {
        $redirectUrl = $_SERVER['REQUEST_URI'] === "/admin/logout" ? "/admin/login" : "/";
        $userId = $_SESSION['UserID'] ?? null;

        // Lưu cart vào cookie trước khi đăng xuất nếu có userID
        if (isset($_SESSION['Role']) && $_SESSION['Role'] == 'user' && $userId) {
            // Lưu cart vào cookie
            $cartCookieName = 'user_cart_' . $userId;
            // Lấy cart từ session nếu có
            if (isset($_SESSION['cart'])) {
                $cartItems = $_SESSION['cart'];

                
                $transformedCart = [];
                foreach ($cartItems as $item) {
                    if (isset($item['product_id']) && isset($item['quantity'])) {
                        $transformedCart[] = [
                            'id' => (int) $item['product_id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    } else if (isset($item['id']) && isset($item['quantity'])) {
                        $transformedCart[] = [
                            'id' => (int) $item['id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    }
                }

                $cartData = json_encode($transformedCart);
                // Cookie hết hạn sau 30 ngày
                setcookie($cartCookieName, $cartData, time() + (86400 * 30), '/');
            }
        }

        // Xóa tất cả dữ liệu session
        $_SESSION = array();

        // Xóa cookie phiên
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Hủy phiên
        session_destroy();

        // Chuyển hướng về trang chủ
        $this->redirect($redirectUrl);
    }

    public function getCartFromCookie()
    {
        if (!isset($_SESSION['UserID'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'User not logged in'
            ]);
            return;
        }

        $userId = $_SESSION['UserID'];
        $cartCookieName = 'user_cart_' . $userId;

        if (isset($_COOKIE[$cartCookieName])) {
            $cartData = $_COOKIE[$cartCookieName];

            // Xóa cookie sau khi đã lấy dữ liệu
            setcookie($cartCookieName, '', time() - 3600, '/');

            // Chuyển đổi từ chuỗi JSON thành mảng PHP
            $cartItems = json_decode($cartData, true);


            $transformedCart = [];
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    if (isset($item['product_id'])) {
                        $transformedCart[] = [
                            'id' => (int) $item['product_id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    } else if (isset($item['id'])) {
                        $transformedCart[] = [
                            'id' => (int) $item['id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    }
                }
            }

            $this->responseJson([
                'success' => true,
                'cart' => $transformedCart
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'No cart cookie found'
            ]);
        }
    }
}
