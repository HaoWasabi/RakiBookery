<?php

require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Book.php';
require_once ROOT_PATH . '/app/models/MockBookData.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Order.php';
require_once ROOT_PATH . '/app/models/PaymentMethod.php';
require_once ROOT_PATH . '/app/models/Promo.php';
require_once ROOT_PATH . '/app/controllers/BaseController.php';
require_once ROOT_PATH . '/app/controllers/BookController.php';

class AdminController extends BaseController
{
    private $data = [];

    private $categoryModel;
    private $bookModel;
    private $bookController;
    private $userModel;
    private $orderModel;
    private $paymentMethodModel;
    private $promoModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->bookModel = new Book();
        $this->bookController = new BookController();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->paymentMethodModel = new PaymentMethod();
        $this->promoModel = new Promo();

        $this->data['paymentMethods'] =
            $this->paymentMethodModel->getAllPaymentMethods();
    }

    private function isAdminLoggedIn()
    {
        return !empty($_SESSION['UserID']) &&
            ($_SESSION['Role'] ?? '') === 'admin';
    }

    private function checkAdminAuth()
    {
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
        }
    }

    public function login()
    {
        if ($this->isAdminLoggedIn()) {
            $this->redirect('/admin/dashboard');
        }

        include ROOT_PATH . '/app/views/admin/login.php';
    }

    public function dashboard()
    {
        $this->checkAdminAuth();

        $availableBooks = $this->bookController->getAllAvailableBooks();

        $this->data['stats'] = [
            'totalOrders' =>
                $this->orderModel->getTotalOrders(),

            'totalRevenue' =>
                $this->orderModel->getTotalRevenue(),

            'totalUsers' =>
                $this->userModel->getTotalUsers(),

            'totalProducts' =>
                is_countable($availableBooks)
                    ? count($availableBooks)
                    : 0
        ];

        $this->data['ordersByCategory'] =
            $this->orderModel->getOrdersByCategory(5);

        $this->data['revenueByMonth'] =
            $this->orderModel->getRevenueByMonth();

        $this->renderView(
            'dashboard',
            'Dashboard',
            'dashboard'
        );
    }

    public function products()
    {
        $this->checkAdminAuth();

        $useMock = env('BOOK_DATA_SOURCE', 'live') === 'mock';

        $this->data['books'] =
            $this->bookController->getAllBooks();

        $this->data['categories'] = $useMock
            ? MockBookData::getCategories()
            : $this->categoryModel->getAll();

        $this->data['useMock'] = $useMock;

        $this->renderView(
            'products',
            'Quản lý sản phẩm',
            'products'
        );
    }

    public function addProduct()
    {
        $this->checkAdminAuth();

        $this->data['categories'] =
            $this->categoryModel->getAll();

        $this->renderView(
            'add-product',
            'Thêm sản phẩm mới',
            'products'
        );
    }

    public function viewProduct()
    {
        $this->checkAdminAuth();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/admin/products');
        }

        $this->data['book'] =
            $this->bookModel->getBookById($id);

        if (!$this->data['book']) {
            $this->redirect('/admin/products');
        }

        $this->data['categories'] =
            $this->categoryModel->getAll();

        $this->renderView(
            'edit-product',
            'Chỉnh sửa sản phẩm',
            'products'
        );
    }

    public function users()
    {
        $this->checkAdminAuth();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 1000;

        $search = $_GET['search'] ?? '';
        $role = $_GET['Role'] ?? '';
        $status = $_GET['Status'] ?? '';

        $totalUsers = $this->userModel->countFilteredUsers(
            $search,
            $role,
            $status
        );

        $totalPages = (int) ceil($totalUsers / $limit);

        $page = min($page, max(1, $totalPages));
        $offset = ($page - 1) * $limit;

        $this->data['users'] =
            $this->userModel->getFilteredUsers(
                $offset,
                $limit,
                $search,
                $role,
                $status
            );

        $this->data['totalUsers'] = $totalUsers;
        $this->data['totalPages'] = $totalPages;
        $this->data['currentPage'] = $page;

        $this->data['adminCount'] =
            $this->userModel->countAdmin();

        $this->renderView(
            'users',
            'Quản lý người dùng',
            'users'
        );
    }

    public function addUser()
    {
        $this->checkAdminAuth();

        $this->renderView(
            'add-user',
            'Thêm người dùng mới',
            'users'
        );
    }

    public function viewUser()
    {
        $this->checkAdminAuth();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/admin/users');
        }

        $this->data['user'] =
            $this->userModel->getUserById($id);

        if (!$this->data['user']) {
            $this->redirect('/admin/users');
        }

        // Khóa chỉnh sửa nếu user đích là admin và không phải chính mình
        $this->data['isTargetAdmin'] = (
            isset($this->data['user']['Role']) &&
            $this->data['user']['Role'] === 'admin' &&
            $this->data['user']['UserID'] != $_SESSION['UserID']
        );

        // Đánh dấu khi admin đang xem tài khoản của chính mình
        $this->data['isSelf'] = ($this->data['user']['UserID'] == $_SESSION['UserID']);

        $this->renderView('edit-user', 'Chỉnh sửa người dùng', 'users');
    }

    public function orders()
    {
        $this->checkAdminAuth();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 1000;

        $status = $_GET['status'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        $city = $_GET['city'] ?? '';
        $district = $_GET['district'] ?? '';
        $paymentmethodID = $_GET['paymentmethodID'] ?? '';

        // Lấy danh sách theo đầy đủ các điều kiện lọc.
        $filteredOrders = $this->orderModel->getFilteredOrders(
            '',
            '',
            $paymentmethodID,
            $status,
            $startDate,
            $endDate,
            $city,
            $district
        );

        // Tổng số đơn hàng đồng nhất với danh sách đã lọc.
        $totalOrders = count($filteredOrders);
        $totalPages = (int) ceil($totalOrders / $limit);

        $page = min($page, max(1, $totalPages));
        $offset = ($page - 1) * $limit;

        $this->data['orders'] = array_slice(
            $filteredOrders,
            $offset,
            $limit
        );

        $this->data['totalOrders'] = $totalOrders;
        $this->data['totalPages'] = $totalPages;
        $this->data['currentPage'] = $page;

        $this->data['paymentMethods'] =
            $this->paymentMethodModel->getAllPaymentMethods();

        $this->data['statusCounts'] = [
            'pending' =>
                $this->orderModel->countOrdersByStatus('pending'),

            'confirmed' =>
                $this->orderModel->countOrdersByStatus('confirmed'),

            'delivered_success' =>
                $this->orderModel->countOrdersByStatus('delivered_success'),

            'canceled' =>
                $this->orderModel->countOrdersByStatus('canceled')
        ];

        $this->renderView(
            'orders',
            'Quản lý đơn hàng',
            'orders'
        );
    }

    public function viewOrder()
    {
        $this->checkAdminAuth();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/admin/orders');
        }

        $order = $this->orderModel->getOrderById($id);

        if (!$order) {
            $this->redirect('/admin/orders');
        }

        $this->data['order'] = $order;

        $this->data['orderDetails'] =
            $this->orderModel->getOrderDetailsByOrderId($id);

        $customerId = $order['UserID'];

        $this->data['customerOrders'] =
            $this->orderModel->getFilteredOrdersOfCustomer(
                $customerId
            );

        // Lấy các đơn hàng đã giao thành công để tính tổng chi tiêu.
        $completedOrders =
            $this->orderModel->getOrdersOfCustomerBetweenStartAndEnd(
                $customerId,
                '',
                ''
            );

        $this->data['customerTotalSpent'] = array_reduce(
            $completedOrders,
            function ($total, $customerOrder) {
                return $total +
                    (float) ($customerOrder['TotalAmount'] ?? 0);
            },
            0.0
        );

        $this->renderView(
            'view-order',
            'Chi tiết đơn hàng',
            'orders'
        );
    }

    public function categories()
    {
        $this->checkAdminAuth();

        $this->data['categories'] =
            $this->categoryModel->getAll();

        $this->renderView(
            'categories',
            'Quản lý thể loại',
            'categories'
        );
    }

    public function addCategory()
    {
        $this->checkAdminAuth();

        $this->renderView(
            'add-category',
            'Thêm thể loại mới',
            'categories'
        );
    }

    public function viewCategory()
    {
        $this->checkAdminAuth();

        $requestData = $this->getRequestData();

        $id = (int) (
            $requestData['id'] ??
            $_GET['id'] ??
            0
        );

        if ($id <= 0) {
            $this->redirect('/admin/categories');
        }

        $this->data['category'] =
            $this->categoryModel->getCategoryById($id);

        if (!$this->data['category']) {
            $this->redirect('/admin/categories');
        }

        $this->renderView(
            'edit-category',
            'Chỉnh sửa thể loại',
            'categories'
        );
    }

    public function topCustomers()
    {
        $this->checkAdminAuth();

        $this->data['topCustomers'] =
            $this->orderModel->getTopCustomersWithOrders(
                '',
                '',
                5
            );

        $this->renderView(
            'top-customers',
            'Top khách hàng',
            'top-customers'
        );
    }

    public function promos()
    {
        $this->checkAdminAuth();

        $this->data['promos'] =
            $this->promoModel->getAll();

        $this->renderView(
            'promos',
            'Quản lý khuyến mãi',
            'promos'
        );
    }

    public function addPromoPage()
    {
        $this->checkAdminAuth();

        $this->renderView(
            'add-promo',
            'Thêm khuyến mãi mới',
            'promos'
        );
    }

    public function viewPromo()
    {
        $this->checkAdminAuth();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/admin/promos');
        }

        $this->data['promo'] =
            $this->promoModel->getPromoById($id);

        if (!$this->data['promo']) {
            $this->redirect('/admin/promos');
        }

        $this->renderView(
            'edit-promo',
            'Chỉnh sửa khuyến mãi',
            'promos'
        );
    }

    public function notFound()
    {
        http_response_code(404);

        include ROOT_PATH . '/app/views/admin/error/404.php';
    }

    private function renderView(
        $viewName,
        $pageTitle,
        $activeMenu
    ) {
        $this->data['page_title'] = $pageTitle;
        $this->data['active_menu'] = $activeMenu;

        extract($this->data, EXTR_SKIP);

        ob_start();

        include ROOT_PATH . "/app/views/admin/{$viewName}.php";

        $content = ob_get_clean();

        include ROOT_PATH . '/app/views/admin/index.php';
    }
}