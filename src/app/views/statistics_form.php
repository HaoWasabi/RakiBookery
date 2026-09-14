<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê khách hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2 class="text-center mb-4">Thống kê khách hàng có mức mua cao nhất</h2>
    <div class="card p-4 shadow">
        <form action="/statistic/result" method="POST">
            <div class="mb-3">
                <label for="start_date" class="form-label">Từ ngày:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">Đến ngày:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Thống kê</button>
        </form>
    </div>
</body>

</html>