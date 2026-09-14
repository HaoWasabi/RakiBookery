<?php
require_once __DIR__ . '/../../config/database.php';


class Order
{
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = Database::getInstance()->getConnection();
        } catch (PDOException $e) {
            error_log("Lỗi kết nối DB: " . $e->getMessage());
            die("Không thể kết nối đến cơ sở dữ liệu.");
        }
    }

    // 1. Hàm tạo đơn hàng
    public function createOrder($userId, $totalAmount, $addressId, $paymentMethodId)
    {
        try {
            $stmt = $this->conn->prepare("
            INSERT INTO `Order` (UserID, TotalAmount, AddressID, PaymentMethodID) 
            VALUES (?, ?, ?, ?)
        ");
            $stmt->execute([$userId, $totalAmount, $addressId, $paymentMethodId]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Lỗi tạo đơn hàng: " . $e->getMessage());
            return false;
        }
    }

    // 2. Hàm cập nhật trạng thái đơn hàng
    public function updateOrderStatus($orderId, $newStatus, $userId = null)
    {
        try {
            // Kiểm tra đơn hàng hiện tại (có điều kiện user nếu có)
            $query = "SELECT Status FROM `Order` WHERE OrderID = ?";
            $params = [$orderId];

            if ($userId !== null) {
                $query .= " AND UserID = ?";
                $params[] = $userId;
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order)
                return false;

            if ($order['Status'] === 'delivered_success' && $newStatus === 'canceled')
                return false;

            // Cập nhật trạng thái đơn hàng
            $stmt = $this->conn->prepare("UPDATE `Order` SET Status = ? WHERE OrderID = ?" . ($userId !== null ? " AND UserID = ?" : ""));
            $params = [$newStatus, $orderId];
            if ($userId !== null)
                $params[] = $userId;

            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật trạng thái đơn hàng: " . $e->getMessage());
            return false;
        }
    }


    // 3. Hàm lấy tất cả đơn hàng
    public function getAllOrders()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT o.*, a.Address, a.City, a.District, a.Ward ,u.Name
            FROM `Order` o
            JOIN Address a ON o.AddressID = a.AddressID
            JOIN User u on u.AddressID=O.AddressId
            ORDER BY o.OrderDate DESC
        ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách đơn hàng: " . $e->getMessage());
            return [];
        }
    }
    public function getAllOrderOfCustomer($userId)
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT o.*, a.Address, a.City, a.District, a.Ward ,u.Name,o.Status
            FROM `Order` o
            JOIN Address a ON o.AddressID = a.AddressID
            JOIN User u on u.AddressID=O.AddressId
            Where o.UserID = ?
            ORDER BY o.OrderDate DESC
        ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách đơn hàng: " . $e->getMessage());
            return [];
        }
    }

    // 4. Hàm lọc đơn hàng theo tiêu chí
    public function filterOrders($status = null, $startDate = null, $endDate = null, $city = null, $district = null)
    {
        try {
            $query = "
            SELECT o.*, a.Address, a.City, a.District, a.Ward ,u.Name
            FROM `Order` o
            JOIN Address a ON o.AddressID = a.AddressID
            JOIN User u on u.AddressID=O.AddressId
            WHERE 1=1
        ";

            $params = [];

            if ($status) {
                $query .= " AND o.Status = ?";
                $params[] = $status;
            }
            if ($startDate && $endDate) {
                $query .= " AND DATE(o.OrderDate) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            }
            if ($city) {
                $query .= " AND a.City LIKE ?";
                $params[] = "%{$city}%"; // Thêm ký tự % để tìm kiếm chứa chuỗi
            }
            if ($district) {
                $query .= " AND a.District LIKE ?";
                $params[] = "%{$district}%";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lọc đơn hàng: " . $e->getMessage());
            return [];
        }
    }


    // Get top customers by order value
    public function getTopCustomers($startDate, $endDate, $limit = 5)
    {
        try {
            $query = "
                SELECT 
                    u.UserID, u.Name, u.Email, u.Phone,
                    COUNT(DISTINCT o.OrderID) AS OrderCount,
                    SUM(o.TotalAmount) AS TotalAmount
                FROM user u
                JOIN `Order` o ON u.UserID = o.UserID
                WHERE o.Status = 'delivered_success'
            ";

            $params = [];

            if ($startDate) {
                $query .= " AND o.OrderDate >= ?";
                $params[] = $startDate;
            }

            if ($endDate) {
                $query .= " AND o.OrderDate <= ?";
                $params[] = $endDate;
            }

            $query .= "
                GROUP BY u.UserID, u.Name, u.Email, u.Phone
                ORDER BY TotalAmount DESC
                LIMIT ?
            ";

            $params[] = (int) $limit;

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy khách hàng hàng đầu: " . $e->getMessage());
            return [];
        }
    }

    public function getOrdersOfCustomerBetweenStartAndEnd($userId, $startDate, $endDate)
    {
        try {
            $query = "
                SELECT o.OrderID, o.OrderDate, o.TotalAmount, o.Status
                FROM `Order` o
                WHERE o.UserID = ? AND o.Status = 'delivered_success'
            ";

            $params = [$userId];

            if ($startDate) {
                $query .= " AND o.OrderDate >= ?";
                $params[] = $startDate;
            }

            if ($endDate) {
                $query .= " AND o.OrderDate <= ?";
                $params[] = $endDate;
            }

            $query .= " ORDER BY o.OrderDate DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy đơn hàng của khách hàng: " . $e->getMessage());
            return [];
        }
    }

        public function getTopCustomersWithOrders($startDate, $endDate, $limit = 5)
        {
            try {
                $query = "
                SELECT 
                    u.UserID, u.Name, u.Email, u.Phone,
                    COUNT(DISTINCT o.OrderID) AS OrderCount,
                    SUM(o.TotalAmount) AS TotalAmount
                FROM user u
                JOIN `Order` o ON u.UserID = o.UserID
                WHERE o.Status = 'delivered_success'
                ";
        
                $params = [];
        
                if (!empty($startDate)) {
                    $query .= " AND o.OrderDate >= ?";
                    $params[] = $startDate;
                }
        
                if (!empty($endDate)) {
                    $query .= " AND o.OrderDate <= ?";
                    $params[] = $endDate;
                }
        
                $query .= "
                GROUP BY u.UserID, u.Name, u.Email, u.Phone
                ORDER BY TotalAmount DESC
                LIMIT $limit";  // Sửa LIMIT để nối trực tiếp giá trị của $limit vào câu truy vấn
        
                $stmt = $this->conn->prepare($query);
                $stmt->execute($params);
                $topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                // Query đơn hàng từng khách
                foreach ($topCustomers as &$customer) {
                    $orderQuery = "
                    SELECT o.OrderID, o.OrderDate, o.TotalAmount, o.Status
                    FROM `Order` o
                    WHERE o.UserID = ? AND o.Status = 'delivered_success'
                ";
        
                    $orderParams = [$customer['UserID']];
        
                    if (!empty($startDate)) {
                        $orderQuery .= " AND o.OrderDate >= ?";
                        $orderParams[] = $startDate;
                    }
        
                    if (!empty($endDate)) {
                        $orderQuery .= " AND o.OrderDate <= ?";
                        $orderParams[] = $endDate;
                    }
        
                    $orderQuery .= " ORDER BY o.OrderDate DESC";
        
                    $orderStmt = $this->conn->prepare($orderQuery);
                    $orderStmt->execute($orderParams);
                    $customer['Orders'] = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
                }
        
                return $topCustomers;
            } catch (PDOException $e) {
                error_log("Lỗi lấy top khách hàng kèm đơn hàng: " . $e->getMessage());
                return [];
            }
        }    


    public function getOrderById($orderId, $userId = null)
    {
        try {
            // Lấy thông tin đơn hàng + User
            $sql = "
                    SELECT 
                        o.OrderID, o.OrderDate, o.Status, o.TotalAmount,
                        u.UserID, u.Name AS UserName, u.Email, u.Phone,
                        a.Address, a.City, a.District, a.Ward,
                        pm.Name AS PaymentMethod
                    FROM `Order` o
                    JOIN User u ON o.UserID = u.UserID
                    JOIN Address a ON o.AddressID = a.AddressID
                    JOIN PaymentMethod pm ON o.PaymentMethodID = pm.PaymentMethodID
                    WHERE o.OrderID = ?
                    ";

            $params = [$orderId];

            // Nếu có userId thì thêm điều kiện
            if ($userId) {
                $sql .= " AND o.UserID = ?";
                $params[] = $userId;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return null; // Không tìm thấy đơn hàng
            }

            // Lấy danh sách sản phẩm trong đơn hàng
            $stmt = $this->conn->prepare("
                SELECT 
                    od.ProductID, b.Name AS ProductName, b.Author, b.ImageURL,
                    od.Quantity, od.Price , c.Name AS Category
                FROM OrderDetail od
                JOIN Book b ON od.ProductID = b.BookID
                JOIN Category c ON b.CategoryID = c.CategoryID
                WHERE od.OrderID = ?
            ");
            $stmt->execute([$orderId]);
            $orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Gộp thông tin đơn hàng và chi tiết đơn hàng
            $order['OrderDetails'] = $orderDetails;

            return $order;
        } catch (PDOException $e) {
            error_log("Lỗi lấy đơn hàng theo ID: " . $e->getMessage());
            return null;
        }
    }

    public function getFilteredOrdersOfCustomer($userId)
    {
        // Set up filters
        $filters = [
            'orderID' => $_GET['orderID'] ?? '',
            'Status' => $_GET['Status'] ?? '',
            'startDate' => $_GET['startDate'] ?? '',
            'endDate' => $_GET['endDate'] ?? '',
        ];

        try {
            $query = "SELECT o.*, a.Address, a.City, a.District, a.Ward, u.Name
                      FROM `Order` o
                      JOIN Address a ON o.AddressID = a.AddressID
                      JOIN User u ON u.UserID = o.UserID
                      WHERE o.UserID = ?";

            $params = [$userId];

            // Filter by order ID if provided
            if (!empty($filters['orderID'])) {
                $query .= " AND o.OrderID LIKE ?";
                $params[] = "%" . $filters['orderID'] . "%";
            }

            // Filter by status if provided
            if (!empty($filters['Status'])) {
                $query .= " AND o.Status = ?";
                $params[] = $filters['Status'];
            }

            // Filter by date range if provided
            if (!empty($filters['startDate'])) {
                $query .= " AND DATE(o.OrderDate) >= ?";
                $params[] = $filters['startDate'];
            }

            if (!empty($filters['endDate'])) {
                $query .= " AND DATE(o.OrderDate) <= ?";
                $params[] = $filters['endDate'];
            }

            // Order by most recent
            $query .= " ORDER BY o.OrderDate DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lọc đơn hàng: " . $e->getMessage());
            return [];
        }
    }

    // Get total number of orders
    public function getTotalOrders()
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM `Order`");
            $stmt->execute();
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi đếm tổng số đơn hàng: " . $e->getMessage());
            return 0;
        }
    }

    // Get total revenue from successful orders
    public function getTotalRevenue()
    {
        try {
            $stmt = $this->conn->prepare("SELECT SUM(TotalAmount) FROM `Order` WHERE Status = 'delivered_success'");
            $stmt->execute();
            return (float) $stmt->fetchColumn() ?: 0;
        } catch (PDOException $e) {
            error_log("Lỗi tính tổng doanh thu: " . $e->getMessage());
            return 0;
        }
    }

    // Get orders by category for dashboard chart
    public function getOrdersByCategory($limit = 5)
    {
        try {
            $query = "
            SELECT 
                c.CategoryID,
                c.Name AS Category,
                COUNT(DISTINCT o.OrderID) AS OrderCount,
                SUM(od.Quantity) AS ItemCount,
                SUM(od.Quantity * IFNULL(od.Price, 0)) AS TotalAmount
            FROM OrderDetail od
            JOIN `Order` o ON od.OrderID = o.OrderID 
                AND o.Status = 'delivered_success'
            JOIN Book b ON od.ProductID = b.BookID
            JOIN Category c ON b.CategoryID = c.CategoryID
            GROUP BY c.CategoryID
            ORDER BY TotalAmount DESC
            LIMIT :limit
        ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy đơn hàng theo thể loại: " . $e->getMessage());
            return [];
        }
    }

    // Get orders by category with time period filter for dashboard pie chart
    public function getOrdersByCategoryByPeriod($timePeriod = 'all')
    {
        try {
            $query = "
            SELECT 
                c.CategoryID,
                c.Name AS Category,
                COUNT(DISTINCT o.OrderID) AS OrderCount,
                SUM(od.Quantity) AS ItemCount,
                SUM(od.Quantity * IFNULL(od.Price, 0)) AS TotalAmount
            FROM OrderDetail od
            JOIN `Order` o ON od.OrderID = o.OrderID 
                AND o.Status = 'delivered_success'
            ";

            switch ($timePeriod) {
                case '1month':
                    $query .= " AND o.OrderDate >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                    break;
                case '6months':
                    $query .= " AND o.OrderDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
                    break;
                case '1year':
                    $query .= " AND o.OrderDate >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                    break;
                case 'all':
                default:
                    break;
            }

            $query .= "
            JOIN Book b ON od.ProductID = b.BookID
            JOIN Category c ON b.CategoryID = c.CategoryID
            GROUP BY c.CategoryID
            ORDER BY TotalAmount DESC
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy đơn hàng theo thể loại và thời gian: " . $e->getMessage());
            return [];
        }
    }

    // Get monthly revenue for dashboard chart
    public function getRevenueByMonth()
    {
        try {
            $query = "
                SELECT 
                    DATE_FORMAT(OrderDate, '%Y-%m') AS Month,
                    COUNT(OrderID) AS OrderCount,
                    SUM(TotalAmount) AS TotalAmount
                FROM `Order`
                WHERE Status = 'delivered_success'
                AND OrderDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(OrderDate, '%Y-%m')
                ORDER BY Month
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy doanh thu theo tháng: " . $e->getMessage());
            return [];
        }
    }

    // Get monthly revenue for a specific year
    public function getRevenueByMonthForYear($year)
    {
        try {
            $query = "
                SELECT 
                    DATE_FORMAT(OrderDate, '%m') AS Month,
                    COUNT(OrderID) AS OrderCount,
                    SUM(TotalAmount) AS TotalAmount
                FROM `Order`
                WHERE Status = 'delivered_success'
                AND YEAR(OrderDate) = :year
                GROUP BY DATE_FORMAT(OrderDate, '%m')
                ORDER BY Month
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Đảm bảo có đủ 12 tháng trong kết quả
            $fullYearData = [];
            for ($i = 1; $i <= 12; $i++) {
                $month = str_pad($i, 2, '0', STR_PAD_LEFT);
                $found = false;

                foreach ($result as $item) {
                    if ($item['Month'] === $month) {
                        $fullYearData[] = $item;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $fullYearData[] = [
                        'Month' => $month,
                        'OrderCount' => 0,
                        'TotalAmount' => 0
                    ];
                }
            }

            return $fullYearData;
        } catch (PDOException $e) {
            error_log("Lỗi lấy doanh thu theo tháng cho năm: " . $e->getMessage());
            return [];
        }
    }

    // Get filtered orders with pagination for admin panel
    public function getFilteredOrders($offset = '', $limit = '', $paymentmethodID = '', $status = '', $startDate = '', $endDate = '', $city = '', $district = '')
    {
        try {
            $query = "
            SELECT o.*, a.Address, a.City, a.District, a.Ward, u.Name, u.Email, u.Phone, pm.Name AS PaymentMethod
            FROM `Order` o
            JOIN Address a ON o.AddressID = a.AddressID
            JOIN User u ON o.UserID = u.UserID
            JOIN PaymentMethod pm ON o.PaymentMethodID = pm.PaymentMethodID
            WHERE 1=1
        ";

            $params = [];

            if (!empty($paymentmethodID)) {
                $query .= " AND pm.PaymentMethodID = ?";
                $params[] = $paymentmethodID;
            }

            if (!empty($status)) {
                $query .= " AND o.Status = ?";
                $params[] = $status;
            }

            if (!empty($startDate) && !empty($endDate)) {
                $query .= " AND DATE(o.OrderDate) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            } else if (!empty($startDate)) {
                $query .= " AND DATE(o.OrderDate) >= ?";
                $params[] = $startDate;
            } else if (!empty($endDate)) {
                $query .= " AND DATE(o.OrderDate) <= ?";
                $params[] = $endDate;
            }

            if (!empty($city)) {
                $query .= " AND a.City LIKE ?";
                $params[] = "%{$city}%";
            }

            if (!empty($district)) {
                $query .= " AND a.District LIKE ?";
                $params[] = "%{$district}%";
            }

            $query .= " ORDER BY o.OrderDate DESC";

            if (!empty($offset) && !empty($limit)) {
                $query .= " LIMIT ?, ?";
                $params[] = (int) $offset;
                $params[] = (int) $limit;
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách đơn hàng: " . $e->getMessage());
            return [];
        }
    }

    // Count filtered orders for pagination
    public function countFilteredOrders($status = '', $startDate = '', $endDate = '', $city = '', $district = '', $ward = '')
    {
        try {
            $query = "
                SELECT COUNT(*) 
                FROM `Order` o
                JOIN Address a ON o.AddressID = a.AddressID
                WHERE 1=1
            ";

            $params = [];

            if (!empty($status)) {
                $query .= " AND o.Status = ?";
                $params[] = $status;
            }

            if (!empty($startDate) && !empty($endDate)) {
                $query .= " AND DATE(o.OrderDate) BETWEEN ? AND ?";
                $params[] = $startDate;
                $params[] = $endDate;
            } else if (!empty($startDate)) {
                $query .= " AND DATE(o.OrderDate) >= ?";
                $params[] = $startDate;
            } else if (!empty($endDate)) {
                $query .= " AND DATE(o.OrderDate) <= ?";
                $params[] = $endDate;
            }

            if (!empty($city)) {
                $query .= " AND a.City LIKE ?";
                $params[] = "%{$city}%";
            }

            if (!empty($district)) {
                $query .= " AND a.District LIKE ?";
                $params[] = "%{$district}%";
            }

            if (!empty($ward)) {
                $query .= " AND a.Ward LIKE ?";
                $params[] = "%{$ward}%";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi đếm đơn hàng: " . $e->getMessage());
            return 0;
        }
    }

    // Get order details for a specific order
    public function getOrderDetailsByOrderId($orderId)
    {
        try {
            $query = "
                SELECT 
                    od.ProductID, 
                    b.Name AS ProductName, 
                    b.Author, 
                    b.ImageURL,
                    od.Quantity, 
                    od.Price,
                    (od.Quantity * od.Price) AS Subtotal,
                    c.Name AS Category
                FROM OrderDetail od
                JOIN Book b ON od.ProductID = b.BookID
                JOIN Category c ON b.CategoryID = c.CategoryID
                WHERE od.OrderID = ?
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy chi tiết đơn hàng: " . $e->getMessage());
            return [];
        }
    }

    // Count orders by status
    public function countOrdersByStatus($status)
    {
        try {
            $query = "SELECT COUNT(*) FROM `Order` WHERE Status = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$status]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi đếm đơn hàng theo trạng thái: " . $e->getMessage());
            return 0;
        }
    }

    // Get recent orders for dashboard with limit
    public function getRecentOrders($limit = 5)
    {
        try {
            $query = "
                SELECT 
                    o.OrderID, 
                    o.OrderDate, 
                    o.Status, 
                    o.TotalAmount,
                    u.Name AS CustomerName
                FROM `Order` o
                JOIN User u ON o.UserID = u.UserID
                ORDER BY o.OrderDate DESC
                LIMIT :limit
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy đơn hàng gần đây: " . $e->getMessage());
            return [];
        }
    }
}
