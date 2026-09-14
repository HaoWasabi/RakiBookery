<div class="col-md-3 col-lg-2 sidebar d-md-block">
    <div class="d-flex flex-column p-3 h-100">
        <!-- Sidebar Header with Decorative Element -->
        <div class="sidebar-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center logo-container">
                <div class="logo-wrapper">
                    <img src="/img/logo.jpg" alt="MeepBookery Logo" class="img-fluid rounded-circle shadow-sm logo-img">
                </div>
                <h5 class="ms-2 mb-0 text-white fw-bold logo-text">Meepbookery</h5>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="sidebar-section">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center mb-2">
                <span class="section-text"><i class="fas fa-chart-line me-2 opacity-50"></i>THỐNG KÊ</span>
                <span class="heading-line"></span>
            </h6>
            <ul class="nav nav-pills flex-column mb-4">
                <li class="nav-item mb-1">
                    <a href="/admin/dashboard"
                        class="nav-link d-flex align-items-center <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                        <span class="menu-icon-wrapper">
                            <i class="fa-duotone fa-gauge-high menu-icon"></i>
                        </span>
                        <span class="nav-text">Tổng quan</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/admin/top-customers"
                        class="nav-link d-flex align-items-center <?= $active_menu === 'top-customers' ? 'active' : '' ?>">
                        <span class="menu-icon-wrapper">
                            <i class="fa-duotone fa-crown menu-icon"></i>
                        </span>
                        <span class="nav-text">Top khách hàng</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Navigation Menu -->
        <div class="sidebar-section">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center mb-2">
                <span class="section-text"><i class="fas fa-th-large me-2 opacity-50"></i>QUẢN LÝ</span>
                <span class="heading-line"></span>
            </h6>
            <ul class="nav nav-pills flex-column mb-2">
                <li class="nav-item mb-1">
                    <a href="/admin/products"
                        class="nav-link d-flex align-items-center <?= $active_menu === 'products' ? 'active' : '' ?>">
                        <span class="menu-icon-wrapper">
                            <i class="fa-duotone fa-books menu-icon"></i>
                        </span>
                        <span class="nav-text">Quản lý sản phẩm</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/admin/categories"
                        class="nav-link d-flex align-items-center <?= $active_menu === 'categories' ? 'active' : '' ?>">
                        <span class="menu-icon-wrapper">
                            <i class="fa-solid fa-books-medical"></i>
                        </span>
                        <span class="nav-text">Quản lý thể loại</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/admin/orders"
                        class="nav-link d-flex align-items-center <?= $active_menu === 'orders' ? 'active' : '' ?>">
                        <span class="menu-icon-wrapper">
                            <i class="fa-regular fa-file-invoice-dollar"></i>
                        </span>
                        <span class="nav-text">Quản lý đơn hàng</span>
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a href="/admin/users"
                        class="nav-link d-flex align-items-center <?= $active_menu === 'users' ? 'active' : '' ?>">
                        <span class="menu-icon-wrapper">
                            <i class="fa-duotone fa-users menu-icon"></i>
                        </span>
                        <span class="nav-text">Quản lý người dùng</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="mt-auto">
            <hr class="border-light opacity-10">
            <a href="/admin/logout" id="logoutBtn" class="nav-link text-danger d-flex align-items-center">
                <span class="menu-icon-wrapper">
                    <i class="fa-duotone fa-right-from-bracket menu-icon"></i>
                </span>
                <span class="nav-text">Đăng xuất</span>
            </a>
        </div>
    </div>
</div>

<div class="sidebar-overlay"></div>