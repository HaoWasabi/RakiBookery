<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2 class="text-center mb-4">Danh sách đơn hàng</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Mã đơn hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền (VNĐ)</th>
                <th>Trạng thái</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['OrderID']; ?></td>
                    <td><?php echo $order['OrderDate']; ?></td>
                    <td><?php echo number_format($order['TotalAmount'], 2); ?></td>
                    <td><?php echo $order['Status']; ?></td>
                    <td>
                        <a href="/satictic/orderdetail?orderId=<?php echo $order['OrderID']; ?>"
                            class="btn btn-warning btn-sm">
                            Xem chi tiết
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/statistic"
        class="btn btn-secondary mt-3">Quay lại</a>
</body>

</html>