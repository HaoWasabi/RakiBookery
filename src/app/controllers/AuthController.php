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

        $data     = $this->getRequestData();
        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';

        $user = $this->authModel->login($email, $password);

        // Kiểm tra nếu là lỗi
        if (isset($user['error'])) {
            $message = match ($user['error']) {
                'invalid_credentials' => 'Email hoặc mật khẩu không chính xác',
                'account_locked'      => 'Tài khoản của bạn đã bị khóa',
                default               => 'Đã xảy ra lỗi, vui lòng thử lại'
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
                return;
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

        // *** SỬA: đổi true → false ***
        // Lý do giữ false: session_regenerate_id(true) xóa file session cũ ngay
        // lập tức. Nếu browser gửi request song song với session ID cũ, PHP sẽ
        // tạo session mới rỗng vì file cũ đã bị xóa → mất UserID → bị văng login.
        session_regenerate_id(false);

        // *** SỬA: gọi session_write_close() trước khi trả response ***
        // Đảm bảo PHP ghi session xuống file /tmp/php_sessions NGAY LẬP TỨC
        // trước khi script kết thúc. Nếu không, có trường hợp PHP chưa flush
        // session xuống disk mà browser đã redirect và gửi request mới → đọc
        // session trống.
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Name']   = $user['Name'];
        $_SESSION['Email']  = $user['Email'];
        $_SESSION['Role']   = $user['Role'];

        // Phải set cart trước khi write_close vì session.php khởi tạo cart=[]
        // cho session mới, nhưng session mới này chưa có cart nên không cần set lại.

        session_write_close(); // ← flush session xuống disk ngay

        // Check for cart cookie and set a flag for client-side to sync
        if ($user['Role'] == 'user') {
            $hasCartCookie  = false;
            $cartCookieName = 'user_cart_' . $user['UserID'];
            if (isset($_COOKIE[$cartCookieName])) {
                // Không thể set session sau write_close, dùng flag tạm trong response
                $hasCartCookie = true;
            }

            $this->responseJson([
                'success'         => true,
                'message'         => 'Đăng nhập thành công',
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
        $this->requirePost();

        $data     = $this->getRequestData();
        $fullName = $data['fullName'] ?? '';
        $email    = $data['email']    ?? '';
        $phone    = $data['phone']    ?? '';
        $password = $data['password'] ?? '';

        if ($this->authModel->emailExists($email)) {
            $this->responseJson([
                'success' => false,
                'message' => 'Email này đã được sử dụng, vui lòng nhập email khác'
            ]);
            return;
        }

        if ($phone && $this->authModel->phoneExists($phone)) {
            $this->responseJson([
                'success' => false,
                'message' => 'Số điện thoại này đã được sử dụng, vui lòng nhập số khác'
            ]);
            return;
        }

        $result = $this->authModel->register($fullName, $email, $password, $phone);

        if ($result) {
            $user = $this->authModel->login($email, $password);

            if ($user) {
                session_regenerate_id(false); // *** SỬA: đồng bộ với login() ***

                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['Name']   = $user['Name'];
                $_SESSION['Role']   = $user['Role'];

                session_write_close(); // *** SỬA: flush session ngay ***
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
        $userId      = $_SESSION['UserID'] ?? null;

        if (isset($_SESSION['Role']) && $_SESSION['Role'] == 'user' && $userId) {
            $cartCookieName = 'user_cart_' . $userId;
            if (isset($_SESSION['cart'])) {
                $cartItems = $_SESSION['cart'];

                $transformedCart = [];
                foreach ($cartItems as $item) {
                    if (isset($item['product_id']) && isset($item['quantity'])) {
                        $transformedCart[] = [
                            'id'       => (int) $item['product_id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    } else if (isset($item['id']) && isset($item['quantity'])) {
                        $transformedCart[] = [
                            'id'       => (int) $item['id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    }
                }

                setcookie($cartCookieName, json_encode($transformedCart), time() + (86400 * 30), '/');
            }
        }

        $_SESSION = array();

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

        session_destroy();

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

        $userId         = $_SESSION['UserID'];
        $cartCookieName = 'user_cart_' . $userId;

        if (isset($_COOKIE[$cartCookieName])) {
            $cartData = $_COOKIE[$cartCookieName];

            setcookie($cartCookieName, '', time() - 3600, '/');

            $cartItems = json_decode($cartData, true);

            $transformedCart = [];
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    if (isset($item['product_id'])) {
                        $transformedCart[] = [
                            'id'       => (int) $item['product_id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    } else if (isset($item['id'])) {
                        $transformedCart[] = [
                            'id'       => (int) $item['id'],
                            'quantity' => (int) $item['quantity']
                        ];
                    }
                }
            }

            $this->responseJson([
                'success' => true,
                'cart'    => $transformedCart
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'No cart cookie found'
            ]);
        }
    }
}
