<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail - Meep Bookery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

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

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .main-content h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 20px;
        }

        .order-detail {
            max-width: 800px;
        }

        .order-detail h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        .order-detail p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .order-detail .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-detail .table th,
        .order-detail .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .order-detail .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .order-detail .table td img {
            width: 50px;
            height: auto;
        }

        .order-detail .actions {
            margin-top: 20px;
        }

        .order-detail .actions select,
        .order-detail .actions button {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .order-detail .actions button {
            background-color: #e74c3c;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['checkout_success']) || isset($_SESSION['checkout_error'])): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div id="liveToast" class="toast align-items-center text-white <?= isset($_SESSION['checkout_success']) ? 'bg-success' : 'bg-danger' ?> border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= htmlspecialchars($_SESSION['checkout_success'] ?? $_SESSION['checkout_error']) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['checkout_success'], $_SESSION['checkout_error']); ?>
    <?php endif; ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Meep Bookery Admin</div>
        <ul>
            <li><a href="admin.html">Dashboard</a></li>
            <li><a href="admin-products.html">Manage Products</a></li>
            <li><a href="admin-orders.html">Manage Orders</a></li>
            <li><a href="admin-users.html">Manage Users</a></li>
            <li><a href="admin-reports.html">Manage Reports</a></li>
            <li><a href="admin-statistics.html">Manage Statistics</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="index.html">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Order #<?php echo $orderData['OrderID']; ?></h1>
        <div class="order-detail">
            <h2>Order Information</h2>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($orderData['UserName']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($orderData['Email']); ?></p>
            <p><strong>PaymentMethod:</strong> <?php echo htmlspecialchars($orderData['PaymentMethod']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($orderData['OrderDate']); ?></p>
            <p><strong>Address:</strong>
                <?php
                echo htmlspecialchars($orderData['Address']) . ', ' .
                    htmlspecialchars($orderData['Ward']) . ', ' .
                    htmlspecialchars($orderData['District']) . ', ' .
                    htmlspecialchars($orderData['City']);
                ?>
            </p>

            <p><strong>Status:</strong> <?php echo htmlspecialchars($orderData['Status']); ?></p>

            <h2>Items</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderData['OrderDetails'] as $item): ?>
                        <tr>
                            <td><img src="https://via.placeholder.com/50" alt="Product"></td>
                            <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                            <td>$<?php echo number_format($item['Price'], 2); ?></td>
                            <td><?php echo $item['Quantity']; ?></td>
                            <td>$<?php echo number_format($item['Price'] * $item['Quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Total:</strong> $<?php echo number_format($orderData['TotalAmount'], 2); ?></p>
        </div>
    </div>
</body>

</html>