<?php
require_once ROOT_PATH . '/app/models/Category.php';
require_once ROOT_PATH . '/app/models/Book.php';
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/PaymentMethod.php';
class ClientController extends BaseController
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

        $this->data['bestSellerBooks'] = $this->bookModel->getTopBestSellingBooks(4);
    }

    public function index()
    {
        $this->data['bestSellerBooks'] = $this->bookModel->getTopBestSellingBooks(4);
        $this->renderView('home', 'MeepBookery');
    }

    public function shop()
    {
        $this->renderView('shop', 'Cửa hàng', true, true, [
            ['title' => 'Cửa hàng', 'url' => '/shop']
        ]);
    }

    public function productDetail()
    {
        $this->data['book'] = $this->bookModel->getAvailableBookById($_GET['id']);
        if (!$this->data['book']) {
            $this->notFound();
            return;
        }
        $this->renderView('product-detail', 'Sách ' . $this->data['book']['Name'], true, false, [
            ['title' => 'Cửa hàng', 'url' => '/shop'],
            // ['title' => 'Chi tiết sản phẩm', 'url' => '#']
            ['title' => 'Sách ' . $this->data['book']['Name'], 'url' => '#']
        ]);
    }

    public function cart()
    {
        $this->renderView('cart', 'Giỏ hàng', true, false, [
            ['title' => 'Giỏ hàng', 'url' => '/cart']
        ]);
    }

    public function checkout()
    {
        $this->checkLogin();

        $this->data['user'] = $this->userModel->getUserById($_SESSION['UserID']);

        // Get payment methods
        require_once ROOT_PATH . '/app/models/PaymentMethod.php';

        $this->data['payment_methods'] = $this->paymentMethodModel->getAllPaymentMethods();

        $this->renderView('checkout', 'Thanh toán', true, false, [
            ['title' => 'Giỏ hàng', 'url' => '/cart'],
            ['title' => 'Thanh toán', 'url' => '/cart/checkout']
        ]);
    }

    public function orderHistory()
    {
        $this->checkLogin();

        $this->data['orders'] = $this->orderModel->getFilteredOrdersOfCustomer($_SESSION['UserID']);

        $this->renderView('order-history', 'Lịch sử đơn hàng', true, false, [
            ['title' => 'Tài khoản', 'url' => '/my-account'],
            ['title' => 'Lịch sử đơn hàng', 'url' => '/my-account/order-history']
        ]);
    }

    public function orderDetail()
    {
        $this->checkLogin();

        $this->data['order'] = $this->orderModel->getOrderById($_GET['id'], $_SESSION['UserID']);

        if (!$this->data['order']) {
            $this->notFound();
            return;
        }

        $this->renderView('order-detail', 'Chi tiết đơn hàng', true, false, [
            ['title' => 'Tài khoản', 'url' => '/my-account'],
            ['title' => 'Lịch sử đơn hàng', 'url' => '/my-account/order-history'],
            [
                'title' => 'Chi tiết đơn hàng '
                // . $this->data['order']['OrderID']
                ,
                'url' => '#'
            ]
        ]);
    }

    public function myAccount()
    {
        $this->checkLogin();

        $this->data['user'] = $this->userModel->getUserById($_SESSION['UserID']);

        $this->renderView('my-account', 'Tài khoản của tôi', true, false, [
            ['title' => 'Tài khoản', 'url' => '/my-account']
        ]);
    }

    public function aboutUs()
    {
        $this->renderView('about-us', 'Giới thiệu', true, true, [
            ['title' => 'Giới thiệu', 'url' => '/about-us']
        ]);
    }

    public function contactUs()
    {
        $this->renderView('contact-us', 'Liên hệ', true, true, [
            ['title' => 'Liên hệ', 'url' => '/contact-us']
        ]);
    }

    public function notFound()
    {
        http_response_code(404);
        $this->renderView('../error/404', '404 - Trang không tìm thấy', false, false, [
            ['title' => 'Lỗi 404', 'url' => '#']
        ]);
    }

    private function checkLogin(): void
    {
        if (!isset($_SESSION['UserID'])) {
            $this->redirect('/');
        }
    }
    public function syncCart()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->responseJson(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Lấy dữ liệu từ request
        $data = $this->getRequestData();

        if (!isset($data['cart']) || !is_array($data['cart'])) {
            $this->responseJson(['success' => false, 'message' => 'Invalid cart data']);
            return;
        }

        // Lưu giỏ hàng vào session
        $_SESSION['cart'] = $data['cart'];

        // Trả về thành công
        $this->responseJson(['success' => true, 'message' => 'Cart synced to session']);
    }
    private function renderView($viewName, $page_title, $show_breadcrumb = false, $show_nav = true, $breadcrumbs = [])
    {

        if (isset($_SESSION['UserID']) && $_SESSION['Role'] == 'admin') {
            header('Location: /logout');
            exit();
        }

        $this->data['categories'] = $this->categoryModel->getAll();
        $this->data['books'] = $this->bookModel->getAllAvailableBooks();

        extract($this->data);

        // Bắt đầu bộ nhớ đệm và include view con
        ob_start();
        include(ROOT_PATH . "/app/views/client/{$viewName}.php");
        $content = ob_get_clean();

        // Sau đó include layout chính
        include(ROOT_PATH . '/app/views/client/index.php');
    }

}
