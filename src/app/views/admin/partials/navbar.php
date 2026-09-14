<nav class="navbar navbar-expand-lg px-4 py-3 shadow-sm">
    <div class="container-fluid">
        <button class="btn sidebar-toggle-btn" type="button" id="sidebarCollapseBtn" aria-label="Toggle sidebar">
            <i class="fas fa-angle-double-left text-primary"></i>
        </button>

        <div class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <?php if (isset($active_menu) && $active_menu !== ''): ?>
                    <li class="breadcrumb-item"><a href="/admin/dashboard"
                            class="text-decoration-none text-primary fw-medium"><i
                                class="fas fa-home me-1"></i>Dashboard</a></li>
                    <?php
                    $breadcrumbTitle = match ($active_menu) {
                        'dashboard' => 'Tổng quan',
                        'products' => 'Quản lý sản phẩm',
                        'categories' => 'Quản lý thể loại',
                        'orders' => 'Quản lý đơn hàng',
                        'users' => 'Quản lý người dùng',
                        'top-customers' => 'Top khách hàng',
                        default => ''
                    };
                    ?>
                    <?php if ($breadcrumbTitle): ?>
                        <li class="breadcrumb-item active"><span class="fw-medium"><?= $breadcrumbTitle ?></span></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ol>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <!-- Notification dropdown -->
            <!-- <div class="dropdown me-3">
                <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg text-muted"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-0 overflow-hidden"
                    style="width: 300px" aria-labelledby="notificationDropdown">
                    <div class="p-3 bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Thông báo</h6>
                    </div>
                    <div class="notification-list p-0">
                        <a href="#" class="dropdown-item px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-primary-light">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-gray-500">27/11/2023</div>
                                    <span class="fw-medium">Đơn hàng mới #12345</span>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-success-light">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-gray-500">26/11/2023</div>
                                    <span class="fw-medium">Người dùng mới đã đăng ký</span>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item px-3 py-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="icon-circle bg-warning-light">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="small text-gray-500">25/11/2023</div>
                                    <span class="fw-medium">Cảnh báo: hàng sắp hết</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <a href="#" class="dropdown-item text-center p-2 bg-light fw-medium text-primary">
                        Xem tất cả thông báo
                    </a>
                </div>
            </div> -->

            <!-- User dropdown -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="me-2 d-none d-lg-inline fw-medium"><?= $_SESSION['Name'] ?? 'Admin' ?></span>
                    <div class="avatar-circle">
                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="userDropdown">
                    <li>
                        <div class="dropdown-item-text p-3 bg-light border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">
                                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-medium"><?= $_SESSION['Name'] ?? 'Admin' ?></div>
                                    <div class="small text-muted"><?= $_SESSION['Email'] ?? 'admin@example.com' ?></div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li><a class="dropdown-item py-2" href="/admin/profile"><i
                                class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Hồ sơ</a></li>
                    <!--                     <li><a class="dropdown-item py-2" href="/admin/settings"><i
                                class="fas fa-cog fa-sm fa-fw me-2 text-gray-400"></i> Cài đặt</a></li> -->
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item py-2 logout-link" id="navbar-logoutBtn" href="/admin/logout"><i
                                class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i> Đăng xuất</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Update breadcrumb item animation
        const breadcrumbItems = document.querySelectorAll('.breadcrumb-item');
        breadcrumbItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>