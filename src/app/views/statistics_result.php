<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thống kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2 class="text-center mb-4">Top 5 khách hàng có mức mua cao nhất</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Tên khách hàng</th>
                <th>Tổng mua (VNĐ)</th>
                <th>Chi tiết đơn hàng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topCustomers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['Name']); ?></td>
                    <td><?php echo number_format($customer['TotalSpent'], 2); ?></td>
                    <td>
                        <a href="/statistic/viewOrders?userId=<?php echo $customer['UserID']; ?>&start=<?php echo $startDate; ?>&end=<?php echo $endDate; ?>"
                            class="btn btn-info btn-sm">
                            Xem đơn hàng
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="/statistic" class="btn btn-secondary mt-3">Quay lại</a>
</body>

</html>