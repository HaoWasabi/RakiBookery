<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Address.php';
require_once __DIR__ . '/../models/PaymentMethod.php';
require_once __DIR__ . '/../models/OrderDetail.php';
require_once __DIR__ . '/BaseController.php';

class OrderController extends BaseController
{
    private $orderModel;
    private $addressModel;
    private $paymentMethodModel;
    private $orderDetailModel;
    public function __construct()
    {
        $this->orderModel = new Order();
        $this->addressModel = new Address();
        $this->paymentMethodModel = new PaymentMethod();
        $this->orderDetailModel = new OrderDetail();
    }

    // Hiển thị danh sách đơn hàng
    public function index()
    {
        $orders = $this->orderModel->getAllOrders();
        // require_once __DIR__ . '/../views/admin-orders.php';  // ✅ Đúng
    }
    // Cập nhật trạng thái đơn hàng
    public function updateStatus()
    {
        $this->requirePost();

        $data = $this->getRequestData();
        $orderId = $data['orderId'] ?? '';
        $status = $data['status'] ?? '';

        if (!$orderId || !$status) {
            $this->responseJson([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
            return;
        }

        $result = $this->orderModel->updateOrderStatus($orderId, $status);

        if ($result) {
            $this->responseJson([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công'
            ]);
        } else {
            $this->responseJson([
                'success' => false,
                'message' => 'Cập nhật trạng thái đơn hàng thất bại'
            ]);
        }
    }


    // Lọc đơn hàng theo tiêu chí
    public function filterOrders()
    {
        $status = $_GET['status'] ?? null;
        $startDate = $_GET['startDate'] ?? null;
        $endDate = $_GET['endDate'] ?? null;
        $city = $_GET['city'] ?? null;
        $district = $_GET['district'] ?? null;
        // echo ($startDate . $endDate);
        $orders = $this->orderModel->filterOrders($status, $startDate, $endDate, $city, $district);
        // require_once __DIR__ . '/../views/admin-orders.php';  // ✅ Sửa đường dẫn
        return $orders;
    }
    public function getOrderDetailById()
    {
        $orderId = intval($_GET['orderId']);
        $orderData = $this->orderModel->getOrderById($orderId);
        // require_once __DIR__ . '/../views/orderdetail.php';
        return $orderData;
    }
    /*  public function createSession()
     {
         session_start();
         unset($_SESSION['cart']);
         unset($_SESSION['UserID']);
         // Tạo session giả nếu chưa tồn tại
         if (!isset($_SESSION['UserID'])) {
             $_SESSION['UserID'] = 5; // Giả sử UserID là 1
         }

         // Tạo giỏ hàng giả nếu chưa có
         if (!isset($_SESSION['cart'])) {
             $_SESSION['cart'] = [
                 [
                     'product_id' => 3,
                     'quantity' => 2,
                     'price' => 150000
                 ],
                 [
                     'product_id' => 4,
                     'quantity' => 1,
                     'price' => 200000
                 ]
             ];
         }
     } */
    public function processCheckout()
    {
        // Kiểm tra đăng nhập và giỏ hàng
        if (!isset($_SESSION['UserID']) || empty($_SESSION['cart'])) {
            if (empty($_SESSION['cart'])) {
                $this->responseJson(['success' => false, 'message' => 'Vui lòng thêm sản phẩm vào giỏ hàng']);
                return;
            }
            $this->responseJson(['success' => false, 'message' => 'Vui lòng đăng nhập và thêm sản phẩm vào giỏ hàng']);
            return;
        }


        $userId = $_SESSION['UserID'];
        $addressId = 0;
        $paymentMethodId = $_POST['payment_method_id'];

        // Nếu người dùng nhập địa chỉ mới, lưu vào DB và lấy ID mới
        if (!empty($_POST['new_address'])) {
            $Address = $_POST['new_address'];
            $Ward = $_POST['new_ward'];
            $District = $_POST['new_district'];
            $City = $_POST['new_city'];
            // Lưu địa chỉ mới vào database
            $addressId = $this->addressModel->create($Address, $City, $District, $Ward);
        } else {
            $addressId = $_POST['address_id']; // Dùng địa chỉ hiện có
        }

        // Tính tổng tiền đơn hàng
        $totalAmount = array_reduce($_SESSION['cart'], function ($sum, $item) {
            return $sum + ($item['quantity'] * $item['price']);
        }, 0);

        // Lưu đơn hàng vào database
        $orderId = $this->orderModel->createOrder($userId, $totalAmount, $addressId, $paymentMethodId);
        if (!$orderId) {
            $this->responseJson(['success' => false, 'message' => 'Lỗi khi tạo đơn hàng']);
            return;
        }

        // Lưu chi tiết đơn hàng
        foreach ($_SESSION['cart'] as $item) {
            $this->orderDetailModel->createOrderDetail($orderId, $item['product_id'], $item['quantity'], $item['price']);
        }

        // Xóa giỏ hàng sau khi đặt hàng thành công
        unset($_SESSION['cart']);

        // Trả về thông tin đơn hàng
        $this->responseJson([
            'success' => true,
            'message' => 'Đặt hàng thành công',
            'orderId' => $orderId
        ]);
    }

    public function getOrdersByCustomerId()
    {
        // Lấy tham số từ URL
        $userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
        if ($userId == 0) {
            die("Thiếu tham số đầu vào.");
        }

        // Lấy danh sách đơn hàng của khách hàng
        $orders = $this->orderModel->getAllOrderOfCustomer($userId);
        // Gọi view để hiển thị dữ liệu
        // require_once __DIR__ . '/../views/orderlist.php';
        return $orders;
    }

    public function getFilteredOrders()
    {
        // Get filter parameters from query string
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
        $city = isset($_GET['city']) ? $_GET['city'] : '';
        $district = isset($_GET['district']) ? $_GET['district'] : '';
        $paymentmethodID = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';

        // Load orders based on filters
        $orders = $this->orderModel->getFilteredOrders('', '', $paymentmethodID, $status, $startDate, $endDate, $city, $district);

        // Get payment methods for the filter dropdown
        $paymentMethods = $this->paymentMethodModel->getAllPaymentMethods();

        $this->responseJson([
            'success' => true,
            'data' => [
                'orders' => $orders,
                'paymentMethods' => $paymentMethods
            ]
        ]);
    }

    public function getRecentOrders()
    {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
        $recentOrders = $this->orderModel->getRecentOrders($limit);

        $this->responseJson([
            'success' => true,
            'data' => $recentOrders
        ]);
    }
}
