<?php
require_once __DIR__ . '/../models/StatisticsModel.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../controllers/BaseController.php';
class StatisticsController extends BaseController
{
    private $statisticsModel;
    private $orderModel;
    public function __construct()
    {
        $this->statisticsModel = new StatisticModel();
        $this->orderModel = new Order();
    }

    public function index()
    {
        // require_once __DIR__ . '/../views/statistics_form.php';  // ✅ Đúng
    }

    public function showStatistics()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];

            $topCustomers = $this->statisticsModel->getTopCustomers($startDate, $endDate);
            // require_once __DIR__ . '/../views/statistics_result.php';  // ✅ Đúng
            return $topCustomers;
        }
    }
    public function viewOrder()
    {
        // Lấy tham số từ URL
        $userId = isset($_GET['userId']) ? intval($_GET['userId']) : 0;
        $startDate = isset($_GET['start']) ? $_GET['start'] : null;
        $endDate = isset($_GET['end']) ? $_GET['end'] : null;

        if ($userId == 0 || !$startDate || !$endDate) {
            die("Thiếu tham số đầu vào.");
        }

        // Lấy danh sách đơn hàng của khách hàng
        $orders = $this->orderModel->getOrdersOfCustomerBetweenStartAndEnd($userId, $startDate, $endDate);

        // Gọi view để hiển thị dữ liệu
        //    require_once __DIR__ . '/../views/orderlist.php';
        return $orders;
    }

    public function getRevenueByYear()
    {
        $year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');
        $revenueData = $this->orderModel->getRevenueByMonthForYear($year);

        $this->responseJson([
            'success' => true,
            'data' => $revenueData
        ]);
    }

    public function getTopCustomers()
    {
        $startDate = isset($_GET['start_date']) && $_GET['start_date'] !== ''
            ? $_GET['start_date'] . ' 00:00:00'
            : null;

        $endDate = isset($_GET['end_date']) && $_GET['end_date'] !== ''
            ? $_GET['end_date'] . ' 23:59:59'
            : null;

        $topCustomers = $this->orderModel->getTopCustomersWithOrders($startDate, $endDate, 5);

        $this->responseJson([
            'success' => true,
            'data' => $topCustomers
        ]);
    }

    // Thống kê sản phẩm được bán theo loại
    public function getOrdersByCategory()
    {
        $timePeriod = isset($_GET['period']) ? $_GET['period'] : 'all';
        $ordersByCategory = $this->orderModel->getOrdersByCategoryByPeriod($timePeriod);

        $this->responseJson([
            'success' => true,
            'data' => $ordersByCategory
        ]);
    }
}
?>