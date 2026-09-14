<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Meep Bookery</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Sidebar (giữ nguyên) */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #333;
            color: #fff;
            padding: 20px;
        }

        .sidebar .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 20px;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }

        .sidebar ul li a:hover {
            color: #e74c3c;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .main-content h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .table td a {
            color: #e74c3c;
            text-decoration: none;
            margin-right: 10px;
        }

        .table td a:hover {
            text-decoration: underline;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <!-- Sidebar -->
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Meep Bookery Admin</div>
        <ul>
            <li><a href="/orders">Dashboard</a></li>
            <li><a href="admin-products.html">Manage Products</a></li>
            <li><a href="admin-orders.html">Manage Orders</a></li>
            <li><a href="admin-users.html">Manage Users</a></li>
            <li><a href="admin-reports.html">Manage Reports</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="index.html">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Manage Orders</h1>
        <!-- Form Lọc Đơn Hàng -->
        <form class="row g-3 mb-4" method="GET" action="/orders/filter">
            <div class="col-md-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tất cả</option>
                    <option value="pending">Chờ xác nhận</option>
                    <option value="confirmed">Đã xác nhận</option>
                    <option value="delivered_success">Đã giao hàng</option>
                    <option value="canceled">Đã hủy</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="startDate" class="form-label">Từ ngày</label>
                <input type="date" class="form-control" id="startDate" name="startDate">
            </div>
            <div class="col-md-3">
                <label for="endDate" class="form-label">Đến ngày</label>
                <input type="date" class="form-control" id="endDate" name="endDate">
            </div>
            <div class="col-md-3">
                <label for="city" class="form-label">Thành phố</label>
                <input type="text" class="form-control" id="city" name="city" placeholder="Nhập thành phố">
            </div>
            <div class="col-md-3">
                <label for="district" class="form-label">Quận/Huyện</label>
                <input type="text" class="form-control" id="district" name="district" placeholder="Nhập quận/huyện">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Lọc</button>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['OrderID'] ?></td>
                        <td><?= $order['Name'] ?></td>
                        <td><?= $order['TotalAmount'] ?></td>
                        <td><?= $order['OrderDate'] ?></td>
                        <td><?= $order['Address'] ?>,<?= $order['Ward'] ?>,<?= $order['District'] ?>, <?= $order['City'] ?>
                        </td>
                        <td><?= $order['Status'] ?></td>
                        <td>
                            <?php if ($order['Status'] === 'pending'): ?>
                                <button class="btn btn-success btn-sm"
                                    onclick="changeStatus(<?= $order['OrderID'] ?>, 'confirmed')">
                                    <i class="fas fa-check"></i> Xác nhận
                                </button>
                            <?php elseif ($order['Status'] === 'confirmed'): ?>
                                <button class="btn btn-primary btn-sm"
                                    onclick="changeStatus(<?= $order['OrderID'] ?>, 'delivered_success')">
                                    <i class="fas fa-truck"></i> Giao hàng
                                </button>
                            <?php endif; ?>

                            <?php if ($order['Status'] !== 'delivered_success' && $order['Status'] !== 'canceled'): ?>
                                <button class="btn btn-danger btn-sm"
                                    onclick="changeStatus(<?= $order['OrderID'] ?>, 'canceled')">
                                    <i class="fas fa-times"></i> Hủy
                                </button>
                            <?php endif; ?>

                            <?php if ($order['Status'] === 'delivered_success' || $order['Status'] === 'canceled'): ?>
                                <span class="badge bg-secondary">Không thể chỉnh sửa</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- <tr>
                    <td>#1001</td>
                    <td>John Doe</td>
                    <td>$78.98</td>
                    <td>Pending</td>
                    <td>2025-03-20</td>
                    <td><a href="admin-order-detail.html">View</a> <a href="#">Update</a></td>
                </tr>
                <tr>
                    <td>#1002</td>
                    <td>Jane Smith</td>
                    <td>$39.00</td>
                    <td>Completed</td>
                    <td>2025-03-19</td>
                    <td><a href="#">View</a></td>
                </tr> -->
            </tbody>
        </table>
    </div>
    <script>
        function changeStatus(orderId, newStatus) {
            if (confirm("Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng?")) {
                fetch('http://localhost:8000/orders/update-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'orderId=' + orderId + '&status=' + newStatus
                })
                    .then(response => response.json()) // ✅ Đọc JSON thay vì text
                    .then(data => {
                        alert(data.message);
                        location.reload();
                    })
                    .catch(error => {
                        console.error("Lỗi:", error);
                        alert("Có lỗi xảy ra, vui lòng thử lại!");
                    });
            }
        }
    </script>
</body>

</html>