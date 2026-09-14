<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang - MeepBookery Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon">

    <style>
        .error-container {
            text-align: center;
            background-color: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 1rem;
            line-height: 1;
        }

        .error-message {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #2c3e50;
        }

        .error-description {
            color: #7f8c8d;
            margin-bottom: 2rem;
        }

        .error-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }

        .btn-primary:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .btn-outline-secondary {
            color: #2c3e50;
            border-color: #2c3e50;
        }

        .btn-outline-secondary:hover {
            background-color: #2c3e50;
            border-color: #2c3e50;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-message">Không tìm thấy trang</h1>
        <p class="error-description">Trang bạn đang tìm kiếm không tồn tại.</p>

        <div class="d-grid gap-2 d-md-flex justify-content-center">
            <!-- <a href="/admin/dashboard" class="btn btn-primary px-4">
                <i class="fas fa-tachometer-alt me-2"></i>Về Dashboard
            </a> -->
            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>
</body>

</html>