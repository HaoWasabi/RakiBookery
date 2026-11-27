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

    private function requireLogin()
    {
        if (empty($_SESSION['UserID']) || empty($_SESSION['Role'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để tiếp tục'
            ], 401);
        }
    }

    private function isAdmin()
    {
        return ($_SESSION['Role'] ?? '') === 'admin';
    }

    public function getUserInfo($userId = null)
    {
        $this->requireLogin();

        $userId = $userId ?? $_SESSION['UserID'];

        if (!$this->isAdmin() && $_SESSION['UserID'] != $userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập thông tin này'
            ], 403);
        }

        $userInfo = $this->userModel->getUserById($userId);

        if (!$userInfo) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
        }

        if (!$this->isAdmin()) {
            unset($userInfo['Status']);
        }

        $this->responseJson([
            'success' => true,
            'data' => $userInfo
        ]);
    }

    public function updateUserInfo()
    {
        $this->requireLogin();
        $this->requirePost();

        $data = $this->getRequestData();

        $userId = $data['UserID'] ?? $_SESSION['UserID'];

        if (!$this->isAdmin() && $_SESSION['UserID'] != $userId) {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật thông tin này'
            ], 403);
        }

        $currentUser = $this->userModel->getUserById($userId);

        if (!$currentUser) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
        }

        $allowedFields = [
            'Name',
            'Phone',
            'Address',
            'City',
            'District',
            'Ward'
        ];

        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if (!is_string($data[$field])) {
                    $this->responseJson([
                        'success' => false,
                        'message' => 'Dữ liệu không hợp lệ'
                    ], 400);
                }

                $updateData[$field] = trim($data[$field]);
            }
        }

        if (isset($updateData['Name']) && $updateData['Name'] === '') {
            $this->responseJson([
                'success' => false,
                'message' => 'Họ và tên không được để trống'
            ], 400);
        }

        if (
            !empty($updateData['Phone']) &&
            $updateData['Phone'] !== $currentUser['Phone']
        ) {
            if (!preg_match('/^(?:\+84|0)[0-9]{9}$/', $updateData['Phone'])) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Số điện thoại không hợp lệ'
                ], 400);
            }

            if ($this->userModel->phoneExists(
                $updateData['Phone'],
                $userId
            )) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Số điện thoại này đã được sử dụng'
                ]);
            }
        }

        // Giữ lại các trường địa chỉ hiện tại khi chỉ cập nhật một phần.
        $addressFields = ['Address', 'City', 'District', 'Ward'];

        if (array_intersect($addressFields, array_keys($updateData))) {
            foreach ($addressFields as $field) {
                if (!array_key_exists($field, $updateData)) {
                    $updateData[$field] = $currentUser[$field] ?? null;
                }
            }
        }

        $result = $this->userModel->updateUser($userId, $updateData);

        if (!$result) {
            $this->responseJson([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật thông tin'
            ], 500);
        }

        if (
            $_SESSION['UserID'] == $userId &&
            isset($updateData['Name'])
        ) {
            $_SESSION['Name'] = $updateData['Name'];
        }

        $updatedUser = $this->userModel->getUserById($userId);

        if (!$this->isAdmin() && is_array($updatedUser)) {
            unset($updatedUser['Status']);
        }

        $this->responseJson([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
            'data' => $updatedUser
        ]);
    }

    public function lockUser()
    {
        $this->requireAdmin();
        $this->requirePost();

        $data = $this->getRequestData();
        $userId = $data['UserID'] ?? null;

        if (!filter_var($userId, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ], 400);
        }

        if ($userId == $_SESSION['UserID']) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể khóa tài khoản của chính bạn'
            ]);
        }

        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
        }

        if ($user['Role'] === 'admin') {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể khóa tài khoản của admin khác'
            ]);
        }

        $result = $this->userModel->lockUser($userId);

        $this->responseJson([
            'success' => (bool) $result,
            'message' => $result
                ? 'Đã khóa tài khoản thành công'
                : 'Có lỗi xảy ra khi khóa tài khoản'
        ]);
    }

    public function unlockUser()
    {
        $this->requireAdmin();
        $this->requirePost();

        $data = $this->getRequestData();
        $userId = $data['UserID'] ?? null;

        if (!filter_var($userId, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ], 400);
        }

        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng'
            ], 404);
        }

        $result = $this->userModel->unlockUser($userId);

        $this->responseJson([
            'success' => (bool) $result,
            'message' => $result
                ? 'Đã mở khóa tài khoản thành công'
                : 'Có lỗi xảy ra khi mở khóa tài khoản'
        ]);
    }

    public function getUsers()
    {
        $this->requireAdmin();

        $filters = [
            'role' => $_GET['role'] ?? '',
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $users = $this->userModel->getUsers($filters);

        $this->responseJson([
            'success' => true,
            'data' => $users
        ]);
    }

    public function toggleUserStatus()
    {
        $this->requireAdmin();
        $this->requirePost();

        $data = $this->getRequestData();
        $userId = $data['UserID'] ?? null;

        if (!filter_var($userId, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ], 400);
        }

        if ($userId == $_SESSION['UserID']) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể thay đổi trạng thái tài khoản của chính bạn'
            ]);
        }

        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        if ($user['Role'] === 'admin') {
            $this->responseJson([
                'success' => false,
                'message' => 'Không thể thay đổi trạng thái tài khoản admin khác'
            ]);
        }

        $result = $this->userModel->toggleUserStatus($userId);

        $this->responseJson([
            'success' => (bool) $result,
            'message' => $result
                ? 'Cập nhật trạng thái người dùng thành công'
                : 'Cập nhật trạng thái người dùng thất bại'
        ]);
    }

    public function addUser()
    {
        $this->requireAdmin();
        $this->requirePost();
        $userData = $this->getRequestData();

        $requiredFields = ['Name', 'Email', 'Password'];

        foreach ($requiredFields as $field) {
            if (
                !isset($userData[$field]) ||
                !is_string($userData[$field]) ||
                trim($userData[$field]) === ''
            ) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc'
                ], 400);
            }
        }

        $userData['Name'] = trim($userData['Name']);
        $userData['Email'] = trim($userData['Email']);

        if (!filter_var($userData['Email'], FILTER_VALIDATE_EMAIL)) {
            $this->responseJson([
                'success' => false,
                'message' => 'Email không hợp lệ'
            ], 400);
        }

        if (strlen($userData['Password']) < 6) {
            $this->responseJson([
                'success' => false,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
            ], 400);
        }

        $userData['Role'] = $userData['Role'] ?? 'user';
        $userData['Status'] = 1;

        if (!in_array($userData['Role'], ['user', 'admin'], true)) {
            $this->responseJson([
                'success' => false,
                'message' => 'Vai trò người dùng không hợp lệ'
            ], 400);
        }

        if (!empty($userData['Phone'])) {
            if (
                !is_string($userData['Phone']) ||
                !preg_match('/^(?:\+84|0)[0-9]{9}$/', $userData['Phone'])
            ) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Số điện thoại không hợp lệ'
                ], 400);
            }

            if ($this->userModel->phoneExists($userData['Phone'])) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Số điện thoại đã tồn tại trong hệ thống',
                    'error' => 'phone_exists'
                ]);
            }
        }

        if ($this->userModel->emailExists($userData['Email'])) {
            $this->responseJson([
                'success' => false,
                'message' => 'Email đã tồn tại trong hệ thống',
                'error' => 'email_exists'
            ]);
        }

        foreach (['Address', 'City', 'District', 'Ward'] as $field) {
            if (isset($userData[$field])) {
                if (!is_string($userData[$field])) {
                    $this->responseJson([
                        'success' => false,
                        'message' => 'Thông tin địa chỉ không hợp lệ'
                    ], 400);
                }

                $userData[$field] = trim($userData[$field]);
            }
        }

        $result = $this->userModel->addUser($userData);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Thêm người dùng thành công',
                'redirect' => '/admin/users'
            ]);
        }

        $this->responseJson([
            'success' => false,
            'message' => 'Thêm người dùng thất bại. Vui lòng thử lại sau.'
        ], 500);
    }

    public function updateUser()
    {
        $this->requireAdmin();
        $this->requirePost();

        $userData = $this->getRequestData();
        $userId = $userData['id'] ?? null;

        if (!filter_var($userId, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ])) {
            $this->responseJson([
                'success' => false,
                'message' => 'ID người dùng không hợp lệ'
            ], 400);
        }

        $currentUser = $this->userModel->getUserById($userId);

        if (!$currentUser) {
            $this->responseJson([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        // Admin không được phép cập nhật thông tin của admin khác
        if ($currentUser['Role'] === 'admin' && $currentUser['UserID'] != $_SESSION['UserID']) {
            $this->responseJson([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật thông tin của admin khác',
                'error' => 'forbidden_admin'
            ], 403);
        }

        $allowedFields = [
            'Name',
            'Email',
            'Phone',
            'Password',
            'Address',
            'City',
            'District',
            'Ward'
        ];

        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($userData[$field])) {
                if (!is_string($userData[$field])) {
                    $this->responseJson([
                        'success' => false,
                        'message' => 'Dữ liệu không hợp lệ'
                    ], 400);
                }

                $updateData[$field] = $field === 'Password'
                    ? $userData[$field]
                    : trim($userData[$field]);
            }
        }

        if (isset($updateData['Name']) && $updateData['Name'] === '') {
            $this->responseJson([
                'success' => false,
                'message' => 'Họ và tên không được để trống'
            ], 400);
        }

        if (isset($updateData['Email'])) {
            if (!filter_var($updateData['Email'], FILTER_VALIDATE_EMAIL)) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Email không hợp lệ'
                ], 400);
            }

            if ($this->userModel->emailExistsForOtherUser(
                $updateData['Email'],
                $userId
            )) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Email đã tồn tại trong hệ thống',
                    'error' => 'email_exists'
                ]);
            }
        }

        if (!empty($updateData['Phone'])) {
            if (!preg_match('/^(?:\+84|0)[0-9]{9}$/', $updateData['Phone'])) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Số điện thoại không hợp lệ'
                ], 400);
            }

            if ($this->userModel->phoneExistsForOtherUser(
                $updateData['Phone'],
                $userId
            )) {
                $this->responseJson([
                    'success' => false,
                    'message' => 'Số điện thoại đã tồn tại trong hệ thống',
                    'error' => 'phone_exists'
                ]);
            }
        }

        if (
            !empty($updateData['Password']) &&
            strlen($updateData['Password']) < 6
        ) {
            $this->responseJson([
                'success' => false,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự'
            ], 400);
        }

        $addressFields = ['Address', 'City', 'District', 'Ward'];

        if (array_intersect($addressFields, array_keys($updateData))) {
            foreach ($addressFields as $field) {
                if (!array_key_exists($field, $updateData)) {
                    $updateData[$field] = $currentUser[$field] ?? null;
                }
            }
        }

        $result = $this->userModel->updateUser($userId, $updateData);

        if ($result) {
            if (
                $_SESSION['UserID'] == $userId &&
                isset($updateData['Name'])
            ) {
                $_SESSION['Name'] = $updateData['Name'];
            }

            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật người dùng thành công',
                'redirect' => '/admin/users'
            ]);
        }

        $this->responseJson([
            'success' => false,
            'message' => 'Cập nhật người dùng thất bại. Vui lòng thử lại sau.'
        ], 500);
    }
}