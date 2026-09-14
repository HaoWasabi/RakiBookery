<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="/img/logo.jpg" type="image/jpeg">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/admin/css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <div class="logo-container">
                    <img src="/img/logo.jpg" alt="MeepBookery Logo">
                </div>
                <h4 class="mb-0">MeepBookery Admin</h4>
            </div>
            <div class="card-body">
                <form id="login-form">
                    <div class="form-floating mb-4">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                            required oninvalid="this.setCustomValidity('Vui lòng nhập email hợp lệ')"
                            oninput="this.setCustomValidity('')">
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                    </div>
                    <div class="input-group mb-4">
                        <div class="form-floating flex-grow-1">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" required
                                oninvalid="this.setCustomValidity('Vui lòng nhập mật khẩu')"
                                oninput="this.setCustomValidity('')">
                            <label for="password"><i class="fas fa-lock me-2"></i>Mật khẩu</label>
                        </div>
                        <span class="input-group-text password-toggle">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="login-btn">
                            <span class="spinner-border spinner-border-sm me-2" id="login-spinner" role="status"
                                aria-hidden="true"></span>
                            <i class="fas fa-sign-in-alt me-2" id="login-icon"></i>Đăng nhập
                        </button>
                    </div>
                </form>

                <!--        <div class="back-to-website">
                    <a href="/"><i class="fas fa-arrow-left me-1"></i> Quay lại trang chủ</a>
                </div> -->
            </div>
        </div>

        <div class="login-footer text-white fw-bold">
            <p>MeepBookery Admin Panel &copy; <?= date('Y') ?>. All rights reserved.</p>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script type="module" src="/assets/admin/js/login.js"></script>
</body>

</html>