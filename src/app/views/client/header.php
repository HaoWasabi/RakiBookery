<!-- Top bar -->
<div class="top-bar py-1">
    <div class="container">
        <div class="row align-items-center">
            <!-- Cột icon MXH -->
            <div class="col-md-3 d-flex">
                <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                <a href="#" class="me-2"><i class="fab fa-youtube"></i></a>
            </div>

            <!-- Cột chào mừng -->
            <div class="col-md-6 text-center fw-bold">
                <span>Chào mừng bạn đến với MeepBookery</span>
            </div>

            <!-- Cột liên hệ -->
            <div class="col-md-3 text-end d-flex flex-column align-items-end">
                <a href="tel:+84123456789"
                    class="d-inline-flex align-items-center mb-1 text-white text-decoration-none">
                    <i class="fas fa-phone-alt me-1"></i>
                    <span>(+84) 0123456789</span>
                </a>
                <a href="mailto:mbkery@gmail.com"
                    class="d-inline-flex align-items-center text-white text-decoration-none">
                    <i class="fas fa-envelope me-1"></i>
                    <span>mbkery@gmail.com</span>
                </a>
            </div>
        </div>
    </div>
</div>


<!-- Main Header -->
<header class="main-header">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Logo -->
            <div class="logo me-3">
                <a href="/">
                    <img src="../../img/logo.JPG" alt="MeepBookery Logo" class="img-fluid">
                </a>
            </div>

            <!-- Search Bar -->
            <div class="search-wrapper flex-grow-1">
                <form action="/shop" method="GET" id="searchForm">
                    <div class="input-group">
                        <div class="dropdown">
                            <button class="category-toggle dropdown-toggle" type="button" id="categoryDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Danh mục
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                                <li><a class="dropdown-item" href="/shop" data-category-id="">Tất cả thể
                                        loại</a></li>
                                <?php if (isset($categories) && is_array($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <li><a class="dropdown-item" href="/shop?category=<?= urlencode($category['Name']); ?>"
                                                data-category-id="<?= $category['CategoryID']; ?>"><?= $category['Name']; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <input type="text" name="search" class="form-control" placeholder="Nhập tên sách..."
                            aria-label="Search">
                        <button class="btn search-btn" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <!-- User Account & Cart -->
            <div class="header-actions d-flex ms-3">
                <div class="account-hover-area me-2">
                    <?php

                    // Check if user is logged in
                    if (isset($_SESSION['UserID'])) {
                        // User is logged in - show user info and dropdown
                        $userName = 'User';
                        if (isset($_SESSION['Name'])) {
                            $parts = explode(' ', $_SESSION['Name']);
                            $userName = end($parts);
                        }

                        ?>
                        <a href="#" class="btn btn-danger account-btn logged-in-btn">
                            <div class="btn-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="btn-text">
                                <span>Chào, <?= $userName ?></span>
                            </div>
                        </a>
                        <div class="account-popup account-menu-dropdown">
                            <div class="account-popup-buttons">
                                <a href="/my-account" class="account-popup-link my-account">
                                    <i class="fas fa-user"></i>
                                    Tài khoản của tôi
                                </a>
                                <a href="/cart" class="account-popup-link my-cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    Giỏ hàng của tôi
                                </a>
                                <a href="/my-account/order-history" class="account-popup-link my-orders">
                                    <i class="fas fa-clipboard-list"></i>
                                    Đơn hàng của tôi
                                </a>
                                <a href="/logout" class="account-popup-link logout" id="logoutBtn">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Đăng xuất
                                </a>
                            </div>
                        </div>
                        <?php
                    } else {
                        // User is not logged in - show login/register buttons
                        ?>
                        <a href="#" class="btn btn-danger account-btn" data-bs-toggle="modal"
                        data-bs-target="#authModal" data-auth-action="login">
                            <div class="btn-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="btn-text">
                                <span>Đăng nhập</span>
                            </div>
                        </a>
                        <div class="account-popup">
                            <div class="account-popup-buttons">
                                <a href="#" class="account-popup-link login" data-bs-toggle="modal"
                                    data-bs-target="#authModal" data-auth-action="login">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Đăng nhập
                                </a>
                                <a href="#" class="account-popup-link register" data-bs-toggle="modal"
                                    data-bs-target="#authModal" data-auth-action="register">
                                    <i class="fas fa-user-plus"></i>
                                    Đăng ký
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                // Check if we are currently on the cart page or checkout page
                $is_cart_page = ($_SERVER['REQUEST_URI'] === '/cart' || $_SERVER['REQUEST_URI'] === '/cart/checkout');
                ?>
                <a href="<?php echo $is_cart_page ? 'javascript:void(0)' : '/cart'; ?>"
                    class="btn btn-danger cart-btn position-relative <?php echo $is_cart_page ? 'disabled opacity-75' : ''; ?>"
                    <?php echo $is_cart_page ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                    <div class="btn-icon">
                        <div class="cart-icon-wrapper">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-white text-danger cart-count">0</span>
                        </div>
                    </div>
                    <div class="btn-text">
                        <span>Giỏ hàng</span>
                    </div>
                </a>
            </div>
        </div>

    </div>
</header>

<!-- Navigation Menu -->
<?php if ($show_nav): ?>
    <nav class="main-navigation">
        <div class="container">
            <ul class="nav-menu">
                <?php
                // Xác định trang hiện tại từ REQUEST_URI
                $current_page = $_SERVER['REQUEST_URI'];

                // Tạo các class active dựa trên URI hiện tại
                $home_active = (strpos($current_page, 'index') !== false || $current_page === '/' || $current_page === '') ? 'active' : '';
                $products_active = (strpos($current_page, 'shop') !== false) ? 'active' : '';
                $about_active = (strpos($current_page, 'about-us') !== false) ? 'active' : '';
                $contact_active = (strpos($current_page, 'contact-us') !== false) ? 'active' : '';
                ?>
                <li class="nav-item <?php echo $home_active; ?>">
                    <a href="/" class="nav-link">
                        <div class="nav-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="nav-text">
                            Trang chủ
                        </div>
                    </a>
                </li>
                <li class="nav-item <?php echo $products_active; ?>">
                    <a href="/shop" class="nav-link">
                        <div class="nav-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="nav-text">
                            Sách
                        </div>
                    </a>
                </li>
                <li class="nav-item <?php echo $about_active; ?>">
                    <a href="/about-us" class="nav-link">
                        <div class="nav-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="nav-text">
                            Giới thiệu
                        </div>
                    </a>
                </li>
                <li class="nav-item <?php echo $contact_active; ?>">
                    <a href="/contact-us" class="nav-link">
                        <div class="nav-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="nav-text">
                            Liên hệ
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
<?php endif; ?>

<?php if (!isset($_SESSION['UserID'])): ?>
    <?php require_once 'auth-modal.php'; ?>
<?php endif; ?>