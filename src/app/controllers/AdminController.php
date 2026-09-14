<?php
require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Book.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Order.php';
require_once ROOT_PATH . '/app/models/PaymentMethod.php';
require_once ROOT_PATH . '/app/controllers/BaseController.php';

class AdminController extends BaseController
{
    private $data = [];
    private $categoryModel;
    private $bookModel;
    private $userModel;
    private $orderModel;
    private $paymentMethodModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->bookModel = new Book();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->paymentMethodModel = new PaymentMethod();

        $this->data['paymentMethods'] = $this->paymentMethodModel->getAllPaymentMethods();
    }
    private function isAdminLoggedIn()
    {
        return isset($_SESSION['UserID']) && isset($_SESSION['Role']) && $_SESSION['Role'] === 'admin';
    }
    public function login()
    {
        if ($this->isAdminLoggedIn()) {
            $this->redirect('/admin/dashboard');
        }

        include('../app/views/admin/login.php');
    }
    private function checkAdminAuth()
    {
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('/admin/login');
        }
    }
    public function dashboard()
    {
        $this->checkAdminAuth();

        // Fetch data for dashboard
        $this->data['stats']['totalOrders'] = $this->orderModel->getTotalOrders();
        $this->data['stats']['totalRevenue'] = $this->orderModel->getTotalRevenue();
        $this->data['stats']['totalUsers'] = $this->userModel->getTotalUsers();
        $this->data['stats']['totalProducts'] = $this->bookModel->getTotalBooks();
        $this->data['ordersByCategory'] = $this->orderModel->getOrdersByCategory("5");
        $this->data['revenueByMonth'] = $this->orderModel->getRevenueByMonth();

        $this->renderView('dashboard', 'Dashboard', 'dashboard');
    }
    public function products()
    {
        $this->checkAdminAuth();

        // Get all books without pagination
        $this->data['books'] = $this->bookModel->getAllBooks();

        // Get categories for the filter dropdown
        $this->data['categories'] = $this->categoryModel->getAll();

        $this->renderView('products', 'Quản lý sản phẩm', 'products');
    }

    public function addProduct()
    {
        $this->checkAdminAuth();

        $this->data['categories'] = $this->categoryModel->getAll();

        $this->renderView('add-product', 'Thêm sản phẩm mới', 'products');
    }

    public function viewProduct()
    {
        $this->checkAdminAuth();

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            $this->redirect('/admin/products');
        }

        $this->data['book'] = $this->bookModel->getBookById($id);

        if (!$this->data['book']) {
            $this->redirect('/admin/products');
        }

        $this->data['categories'] = $this->categoryModel->getAll();

        $this->renderView('edit-product', 'Chỉnh sửa sản phẩm', 'products');
    }

    public function users()
    {
        $this->checkAdminAuth();

        // Get all users with pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 1000; // Increased to match other admin tables using DataTables
        $offset = ($page - 1) * $limit;

        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $role = isset($_GET['Role']) ? $_GET['Role'] : '';
        $status = isset($_GET['Status']) ? $_GET['Status'] : '';

        $this->data['users'] = $this->userModel->getFilteredUsers($offset, $limit, $search, $role, $status);
        $this->data['totalUsers'] = $this->userModel->countFilteredUsers($search, $role, $status);
        $this->data['totalPages'] = ceil($this->data['totalUsers'] / $limit);
        $this->data['currentPage'] = $page;
        $this->data['adminCount'] = $this->userModel->countAdmin();

        $this->renderView('users', 'Quản lý người dùng', 'users');
    }

    public function addUser()
    {
        $this->checkAdminAuth();
        $this->renderView('add-user', 'Thêm người dùng mới', 'users');
    }

    public function viewUser()
    {
        $this->checkAdminAuth();

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            $this->redirect('/admin/users');
        }

        $this->data['user'] = $this->userModel->getUserById($id);

        if (!$this->data['user']) {
            $this->redirect('/admin/users');
        }

        $this->renderView('edit-user', 'Chỉnh sửa người dùng', 'users');
    }

    public function orders()
    {
        $this->checkAdminAuth();

        // Get all orders with pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 1000;
        $offset = ($page - 1) * $limit;

        // Search and filter parameters
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $city = isset($_GET['city']) ? $_GET['city'] : '';
        $district = isset($_GET['district']) ? $_GET['district'] : '';
        $paymentmethodID = isset($_GET['paymentmethodID']) ? $_GET['paymentmethodID'] : '';

        $this->data['orders'] = $this->orderModel->getFilteredOrders($offset, $limit, $paymentmethodID, $status, $startDate, $endDate, $city, $district);
        $this->data['totalOrders'] = $this->orderModel->countFilteredOrders($status, $startDate, $endDate, $city, $district);
        $this->data['totalPages'] = ceil($this->data['totalOrders'] / $limit);
        $this->data['currentPage'] = $page;

        // Get payment methods for the filter dropdown
        $this->data['paymentMethods'] = $this->paymentMethodModel->getAllPaymentMethods();

        // Get counts for each status
        $this->data['statusCounts'] = [
            'pending' => $this->orderModel->countOrdersByStatus('pending'),
            'confirmed' => $this->orderModel->countOrdersByStatus('confirmed'),
            'delivered_success' => $this->orderModel->countOrdersByStatus('delivered_success'),
            'canceled' => $this->orderModel->countOrdersByStatus('canceled')
        ];

        $this->renderView('orders', 'Quản lý đơn hàng', 'orders');
    }

    public function viewOrder()
    {
        $this->checkAdminAuth();

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$id) {
            $this->redirect('/admin/orders');
        }

        $this->data['order'] = $this->orderModel->getOrderById($id);

        if (!$this->data['order']) {
            $this->redirect('/admin/orders');
        }

        // Get order details (items in the order)
        $this->data['orderDetails'] = $this->orderModel->getOrderDetailsByOrderId($id);

        // Get customer's other orders for order history
        $customerId = $this->data['order']['UserID'];
        $this->data['customerOrders'] = $this->orderModel->getAllOrderOfCustomer($customerId);

        // Calculate customer's total spent
        $this->data['customerTotalSpent'] = array_reduce(
            $this->data['customerOrders'],
            function ($total, $order) {
                return $total + $order['TotalAmount'];
            },
            0
        );

        $this->renderView('view-order', 'Chi tiết đơn hàng', 'orders');
    }

    public function categories()
    {
        $this->checkAdminAuth();

        // Get all categories
        $this->data['categories'] = $this->categoryModel->getAll();

        $this->renderView('categories', 'Quản lý thể loại', 'categories');
    }

    public function addCategory()
    {
        $this->checkAdminAuth();
        $this->renderView('add-category', 'Thêm thể loại mới', 'categories');
    }

    public function viewCategory()
    {
        $this->checkAdminAuth();

        $requestData = $this->getRequestData();
        $id = isset($requestData['id']) ? intval($requestData['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

        if (!$id) {
            $this->redirect('/admin/categories');
        }

        $this->data['category'] = $this->categoryModel->getCategoryById($id);

        if (!$this->data['category']) {
            $this->redirect('/admin/categories');
        }

        $this->renderView('edit-category', 'Chỉnh sửa thể loại', 'categories');
    }

    public function topCustomers()
    {
        $this->checkAdminAuth();

        $this->data['topCustomers'] = $this->orderModel->getTopCustomersWithOrders('', '', 5);

        $this->renderView('top-customers', 'Top khách hàng', 'top-customers');
    }
    public function notFound()
    {
        // $this->renderView('/error/404', '404 - Trang không tìm thấy', '');
        include(ROOT_PATH . '/app/views/admin/error/404.php');
    }

    private function renderView($viewName, $pageTitle, $activeMenu)
    {
        $this->data['page_title'] = $pageTitle;
        $this->data['active_menu'] = $activeMenu;

        extract($this->data);

        ob_start();
        include(ROOT_PATH . "/app/views/admin/{$viewName}.php");
        $content = ob_get_clean();

        include(ROOT_PATH . '/app/views/admin/index.php');
    }
}
