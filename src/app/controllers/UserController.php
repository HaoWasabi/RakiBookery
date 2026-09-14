<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/BaseController.php';

class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }
    public function getUserInfo($userId = null)
    {
        // Mặc định lấy thông tin người dùng hiện tại
        if (!$userId && isset($_SESSION['UserID'])) {
            $userId = $_SESSION['UserID'];
        }

        // Kiểm tra quyền truy cập
        if ($_SESSION['Role'] !== 'admin' && $_SESSION['UserID'] != $userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập thông tin này'
            ], 403);
            return;
        }

        $userInfo = $this->userModel->getUserById($userId);

        if (!$userInfo) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
            return;
        }

        // Loại bỏ các thông tin nhạy cảm nếu không phải admin
        if ($_SESSION['Role'] !== 'admin') {
            unset($userInfo['status']);
        }

        $this->responseJson([
            'success' => true,
            'data' => $userInfo
        ]);
    }
    public function updateUserInfo()
    {
        $this->requirePost();

        $data = $this->getRequestData();
        $userId = $data['UserID'] ?? $_SESSION['UserID'];

        // Kiểm tra quyền truy cập
        if ($_SESSION['Role'] !== 'admin' && $_SESSION['UserID'] != $userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật thông tin này'
            ], 403);
            return;
        }

        // Lấy thông tin người dùng hiện tại
        $currentUser = $this->userModel->getUserById($userId);
        if (!$currentUser) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
            return;
        }

        // Validate số điện thoại nếu có thay đổi
        if (isset($data['Phone']) && $data['Phone'] !== $currentUser['Phone']) {
            if ($this->userModel->phoneExists($data['Phone'], $userId)) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Số điện thoại này đã được sử dụng, vui lòng chọn số khác'
                ]);
                return;
            }
        }

        // Chuẩn bị dữ liệu cập nhật
        $updateData = [];

        // Chỉ cập nhật các trường được cho phép
        $allowedFields = ['Name', 'Phone', 'Address', 'City', 'District', 'Ward'];

        // Thêm các trường được phép vào dữ liệu cập nhật
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        // Thực hiện cập nhật
        $result = $this->userModel->updateUser($userId, $updateData);

        if ($result) {
            // Cập nhật session nếu người dùng đang cập nhật thông tin của chính mình
            if ($_SESSION['UserID'] == $userId && isset($updateData['Name'])) {
                $_SESSION['Name'] = $updateData['Name'];
            }

            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $this->userModel->getUserById($userId)
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thông tin'
            ]);
        }
    }
    public function lockUser()
    {
        $this->requirePost();

        // Chỉ admin mới có quyền khóa tài khoản người dùng
        if ($_SESSION['Role'] !== 'admin') {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành độ  ng này'
            ], 403);
            return;
        }

        $data = $this->getRequestData();
        $userId = $data['UserID'] ?? null;

        if (!$userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ], 400);
            return;
        }

        // Không thể khóa tài khoản của chính mình
        if ($userId == $_SESSION['UserID']) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể khóa tài khoản của chính bạn'
            ]);
            return;
        }

        // Lấy thông tin người dùng để kiểm tra
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
            return;
        }

        // Không thể khóa tài khoản admin khác
        if ($user['role'] === 'admin' && $_SESSION['UserID'] != $userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể khóa tài khoản của admin khác'
            ]);
            return;
        }

        // Thực hiện khóa tài khoản
        $result = $this->userModel->lockUser($userId);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Đã khóa tài khoản thành công'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi khóa tài khoản'
            ]);
        }
    }

    public function unlockUser()
    {
        $this->requirePost();

        // Chỉ admin mới có quyền mở khóa tài khoản người dùng
        if ($_SESSION['Role'] !== 'admin') {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này'
            ], 403);
            return;
        }

        $data = $this->getRequestData();
        $userId = $data['UserID'] ?? null;

        if (!$userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ], 400);
            return;
        }

        // Lấy thông tin người dùng để kiểm tra
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
            return;
        }

        // Thực hiện mở khóa tài khoản
        $result = $this->userModel->unlockUser($userId);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Đã mở khóa tài khoản thành công'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi mở khóa tài khoản'
            ]);
        }
    }
    public function getUsers()
    {
        // Chỉ admin mới có quyền xem danh sách người dùng
        if ($_SESSION['Role'] !== 'admin') {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập trang này'
            ], 403);
            return;
        }

        // Lấy tham số lọc từ query string
        $filters = [
            'role' => $_GET['role'] ?? '',
            'status' => isset($_GET['status']) ? $_GET['status'] : '',
            'search' => $_GET['search'] ?? ''
        ];

        // Lấy danh sách người dùng theo bộ lọc
        $users = $this->userModel->getUsers($filters);

        $this->responseJson([
            'success' => true,
            'data' => $users
        ]);
    }

    public function toggleUserStatus()
    {
        $this->requirePost();

        $data = $this->getRequestData();
        error_log(json_encode($data));
        $userId = $data['UserID'] ?? null;

        if (!$userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
            return;
        }

        if ($userId == $_SESSION['UserID']) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể khóa tài khoản của chính bạn'
            ]);
            return;
        }

        // Check if this is the only admin
        $user = $this->userModel->getUserById($userId);

        if ($user['Role'] === 'admin' && $this->userModel->countAdmin() <= 1) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể khóa tài khoản admin duy nhất'
            ]);
            return;
        }

        if ($user['Role'] === 'admin') {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể khóa tài khoản của admin khác'
            ]);
            return;
        }


        $result = $this->userModel->toggleUserStatus($userId);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật trạng thái người dùng thành công'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Cập nhật trạng thái người dùng thất bại'
            ]);
        }
    }

    public function addUser()
    {
        $this->requirePost();

        $userData = $this->getRequestData();
        // Check if phone already exists
        if (isset($userData['Phone']) && $userData['Phone'] && $this->userModel->phoneExists($userData['Phone'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Số điện thoại đã tồn tại trong hệ thống',
                'error' => 'phone_exists'
            ]);
            return;
        }

        // Check if email already exists
        if ($this->userModel->emailExists($userData['Email'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Email đã tồn tại trong hệ thống',
                'error' => 'email_exists'
            ]);
            return;
        }



        $result = $this->userModel->addUser($userData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Thêm người dùng thành công',
                'redirect' => '/admin/users'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Thêm người dùng thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }

    public function updateUser()
    {
        $this->requirePost();

        $userData = $this->getRequestData();

        // Check if email already exists for other users
        if ($this->userModel->emailExistsForOtherUser($userData['Email'], $userData['id'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Email đã tồn tại trong hệ thống',
                'error' => 'email_exists'
            ]);
            return;
        }

        // Check if phone already exists for other users
        if (isset($userData['Phone']) && $userData['Phone'] && $this->userModel->phoneExistsForOtherUser($userData['Phone'], $userData['id'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Số điện thoại đã tồn tại trong hệ thống',
                'error' => 'phone_exists'
            ]);
            return;
        }

        $result = $this->userModel->updateUser($userData['id'], $userData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật người dùng thành công',
                'redirect' => '/admin/users'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Cập nhật người dùng thất bại. Vui lòng thử lại sau.'
            ]);
        }
    }
}
