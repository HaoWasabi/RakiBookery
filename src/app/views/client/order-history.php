<!-- Đơn hàng - MeepBookery -->
<section class="order-history-section">
    <div class="container">
        <div class="col-lg-12">
            <h1 class="order-title text-center mb-4">Lịch sử đơn hàng</h1>
        </div>

        <!-- Filter Error Alert -->
        <div id="filterErrorAlert" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
            <span id="filterErrorMessage"></span>
            <button type="button" class="btn-close" id="closeFilterAlert" aria-label="Close"></button>
        </div>

        <!-- Search & Filter -->
        <div class="order-filter-container mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <button class="btn btn-link text-decoration-none text-danger" type="button"
                            data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true"
                            aria-controls="filterCollapse">
                            <i class="fas fa-filter me-2"></i> Tìm kiếm & Lọc đơn hàng
                        </button>
                    </h5>
                </div>
                <div id="filterCollapse" class="collapse show">
                    <div class="card-body">
                        <form id="orderFilterForm" action="/my-account/order-history" method="GET" class="row g-3">
                            <!-- Order ID -->
                            <div class="col-md-6 col-lg-3">
                                <label for="orderId" class="form-label">Mã đơn hàng</label>
                                <input type="text" class="form-control" id="orderId" name="orderID"
                                    placeholder="Nhập mã đơn hàng"
                                    value="<?= isset($_GET['orderID']) ? htmlspecialchars($_GET['orderID']) : '' ?>">
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-6 col-lg-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="Status">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="pending" <?= (isset($_GET['Status']) && $_GET['Status'] === 'pending') ? 'selected' : '' ?>>Chờ xác nhận</option>
                                    <option value="confirmed" <?= (isset($_GET['Status']) && $_GET['Status'] === 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
                                    <option value="delivered_success" <?= (isset($_GET['Status']) && $_GET['Status'] === 'delivered_success') ? 'selected' : '' ?>>Đã giao hàng
                                    </option>
                                    <option value="canceled" <?= (isset($_GET['Status']) && $_GET['Status'] === 'canceled') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div class="col-md-6 col-lg-3">
                                <label for="startDate" class="form-label">Từ ngày</label>
                                <input type="date" class="form-control" id="startDate" name="startDate"
                                    value="<?= isset($_GET['startDate']) ? htmlspecialchars($_GET['startDate']) : '' ?>">
                                <!-- , strtotime('-30 days')) -->
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label for="endDate" class="form-label">Đến ngày</label>
                                <input type="date" class="form-control" id="endDate" name="endDate"
                                    value="<?= isset($_GET['endDate']) ? htmlspecialchars($_GET['endDate']) : '' ?>">
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 text-end">
                                <a href="/my-account/order-history" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-redo-alt me-1"></i> Đặt lại
                                </a>
                                <button type="submit" class="btn btn-danger" id="filterSubmitBtn">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <?php
                // No orders found
                if (empty($orders)) {
                    echo '<div class="empty-orders">
                        <img src="../../img/empty-order.jpg" alt="Không tìm thấy đơn hàng" class="img-fluid">
                        <h4>Không tìm thấy đơn hàng nào</h4>
                        <p class="text-muted">Hãy thử sử dụng tiêu chí tìm kiếm khác hoặc tiếp tục mua sắm.</p>
                        <a href="/shop" class="btn btn-danger mt-3">
                            <i class="fas fa-shopping-cart me-2"></i> Mua sắm ngay
                        </a>
                    </div>';
                } else {
                    ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng tiền</th>
                                    <th>Địa chỉ giao hàng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="orderTableBody">
                                <!-- Orders will be populated by PaginationJS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination container -->
                    <div id="orderPagination" class="d-flex justify-content-center mt-4"></div>

                    <?php
                    // Prepare orders data for JavaScript
                    $ordersJson = json_encode($orders);
                    ?>

                    <script>
                        // Order data from PHP
                        const orderData = <?= $ordersJson ?>;

                        // Render a single order row
                        function renderOrderRow(order) {
                            // Determine status display
                            let statusClass = '';
                            let statusText = '';

                            switch (order.Status) {
                                case 'pending':
                                    statusClass = 'warning';
                                    statusText = 'Chờ xác nhận';
                                    break;
                                case 'confirmed':
                                    statusClass = 'info';
                                    statusText = 'Đã xác nhận';
                                    break;
                                case 'delivered_success':
                                    statusClass = 'success';
                                    statusText = 'Đã giao hàng';
                                    break;
                                case 'canceled':
                                    statusClass = 'danger';
                                    statusText = 'Đã hủy';
                                    break;
                                default:
                                    statusClass = 'secondary';
                                    statusText = 'Không xác định';
                            }

                            // Format date
                            const orderDate = new Date(order.OrderDate);
                            const formattedDate = orderDate.toLocaleDateString('vi-VN', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });

                            // Format currency
                            const formattedAmount = new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                minimumFractionDigits: 0
                            }).format(order.TotalAmount);

                            // Combine address parts
                            const addressParts = [];
                            if (order.Address) addressParts.push(order.Address);
                            if (order.Ward) addressParts.push(order.Ward);
                            if (order.District) addressParts.push(order.District);
                            if (order.City) addressParts.push(order.City);
                            const fullAddress = addressParts.length > 0 ? addressParts.join(', ') : 'Không có';

                            return `
                                <tr>
                                    <td>${order.OrderID}</td>
                                    <td>${formattedDate}</td>
                                    <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                                    <td>${formattedAmount}</td>
                                    <td class="text-truncate" style="max-width: 250px;" title="${fullAddress}">
                                        ${fullAddress}
                                    </td>
                                    <td>
                                        <a href="/my-account/order-history/order-detail?id=${encodeURIComponent(order.OrderID)}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                    </td>
                                </tr>
                            `;
                        }

                        // Initialize pagination when DOM is loaded
                        document.addEventListener('DOMContentLoaded', function () {
                            // Initialize pagination
                            $('#orderPagination').pagination({
                                dataSource: orderData,
                                pageSize: 5,
                                callback: function (data, pagination) {
                                    // Render order rows
                                    const html = data.map(renderOrderRow).join('');
                                    $('#orderTableBody').html(html);
                                },
                                // Pagination configuration
                                pageRange: 2,
                                prevText: '<i class="fas fa-angle-left"></i>',
                                nextText: '<i class="fas fa-angle-right"></i>',
                                autoHidePrevious: true,
                                autoHideNext: true,
                                hideOnlyOnePage: true,
                                className: 'paginationjs-theme-red'
                            });
                        });
                    </script>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- Help Section -->
<section class="help-section py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <div class="help-icon me-3">
                        <i class="fas fa-truck text-danger"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Theo dõi đơn hàng</h5>
                        <p class="mb-0 small">Kiểm tra trạng thái đơn hàng của bạn</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="d-flex align-items-center">
                    <div class="help-icon me-3">
                        <i class="fas fa-exchange-alt text-danger"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Chính sách đổi trả</h5>
                        <p class="mb-0 small">Đổi trả sản phẩm trong vòng 7 ngày</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div class="help-icon me-3">
                        <i class="fas fa-headset text-danger"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Hỗ trợ 24/7</h5>
                        <p class="mb-0 small">Liên hệ: <a href="tel:+84123456789">(+84) 0123456789</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .help-icon {
        font-size: 1.5rem;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        font-weight: 500;
    }

    .empty-orders {
        padding: 2rem 0;
        text-align: center;
    }

    .empty-orders img {
        max-width: 200px;
        margin-bottom: 1.5rem;
    }

    /* PaginationJS customization */
    .paginationjs-theme-red .paginationjs-pages li.active>a {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .paginationjs-theme-red .paginationjs-pages li>a {
        color: #dc3545;
    }

    .paginationjs-theme-red .paginationjs-pages li>a:hover {
        background-color: #f8d7da;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const orderIdInput = document.getElementById('orderId');
        const filterForm = document.getElementById('orderFilterForm');
        const filterErrorAlert = document.getElementById('filterErrorAlert');
        const filterErrorMessage = document.getElementById('filterErrorMessage');

        // Form submission validation
        filterForm.addEventListener('submit', function (e) {
            // Reset any previous error
            hideAlert();

            // Trim orderID input
            orderIdInput.value = orderIdInput.value.trim();

            // Validate date range
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDate > endDate) {
                e.preventDefault();
                showAlert('Ngày bắt đầu không thể sau ngày kết thúc. Vui lòng chọn lại.');
                return false;
            }

            return true;
        });

        // Function to show alert message
        function showAlert(message) {
            filterErrorMessage.textContent = message;
            filterErrorAlert.classList.remove('d-none');

            // Scroll to error message
            filterErrorAlert.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Function to hide alert message
        function hideAlert() {
            document.getElementById('closeFilterAlert').addEventListener('click', function () {
                filterErrorAlert.classList.add('d-none');
            });
        }

        // Initialize date inputs with defaults if they're empty
        /* if (!startDateInput.value) {
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            startDateInput.valueAsDate = thirtyDaysAgo;
        }

        if (!endDateInput.value) {
            endDateInput.valueAsDate = new Date();
        } */
    });
</script>