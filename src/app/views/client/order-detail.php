<!-- Chi tiết đơn hàng - MeepBookery -->

<!-- Order Detail Banner -->
<section class="order-detail-banner bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="order-detail-banner-content text-center">
                    <h1 class="order-detail-title mb-2 ">Chi tiết đơn hàng
                        <!-- < ?php echo $order['OrderID']; ?> -->
                    </h1>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Order Detail Section -->
<section class="order-detail-section py-3">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Status Timeline -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Trạng thái đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $statusClass = '';
                        $statusText = '';

                        switch ($order['Status']) {
                            case 'pending':
                                $statusClass = 'warning';
                                $statusText = 'Chờ xác nhận';
                                $statusStage = 1;
                                break;
                            case 'confirmed':
                                $statusClass = 'info';
                                $statusText = 'Đã xác nhận';
                                $statusStage = 2;
                                break;
                            /* case 'shipping':
                                $statusClass = 'primary';
                                $statusText = 'Đang giao hàng';
                                $statusStage = 3;
                                break; */
                            case 'delivered_success':
                                $statusClass = 'success';
                                $statusText = 'Đã giao';
                                $statusStage = 4;
                                break;
                            case 'canceled':
                                $statusClass = 'danger';
                                $statusText = 'Đã hủy';
                                $statusStage = 0;
                                break;
                            default:
                                $statusClass = 'secondary';
                                $statusText = 'Không xác định';
                                $statusStage = 0;
                        }
                        ?>

                        <?php if ($statusStage === 0): ?>
                            <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div>
                                    Đơn hàng của bạn đã bị hủy.
                                    <!-- Vui lòng liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi -->
                                    <!-- nào. -->
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="order-timeline">
                                <div class="progress mb-4" style="height: 6px;">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                        style="width: <?php echo min(($statusStage / 3) * 100, 100); ?>%"
                                        aria-valuenow="<?php echo min(($statusStage / 3) * 100, 100); ?>" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="timeline-step <?php echo $statusStage >= 1 ? 'active' : ''; ?>">
                                            <div class="timeline-icon">
                                                <i class="fas fa-file-invoice"></i>
                                            </div>
                                            <p class="timeline-text">Đặt hàng</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="timeline-step <?php echo $statusStage >= 2 ? 'active' : ''; ?>">
                                            <div class="timeline-icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <p class="timeline-text">Xác nhận</p>
                                        </div>
                                    </div>
                                    <!-- <div class="col-3">
                                        <div class="timeline-step < ?php echo $statusStage >= 3 ? 'active' : ''; ?>">
                                            <div class="timeline-icon">
                                                <i class="fas fa-shipping-fast"></i>
                                            </div>
                                            <p class="timeline-text">Đang giao</p>
                                        </div>
                                    </div> -->
                                    <div class="col-3">
                                        <div class="timeline-step <?php echo $statusStage >= 4 ? 'active' : ''; ?>">
                                            <div class="timeline-icon">
                                                <i class="fas fa-box-open"></i>
                                            </div>
                                            <p class="timeline-text">Đã giao</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="current-status text-center mt-4">
                                <span class="badge bg-<?php echo $statusClass; ?> p-2 px-3 fs-6">
                                    <i class="fas fa-circle me-1 small"></i>
                                    <?php echo $statusText; ?>
                                </span>
                                <!-- <p class="text-muted mt-2 mb-0 small">Cập nhật lần cuối:
                                    < ?php echo date('d/m/Y H:i', strtotime($order['updated_at'])); ?>
                                </p> -->
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sản phẩm đã đặt</h5>
                        <span class="text-muted"><?php echo count($order['OrderDetails']); ?> sản phẩm</span>
                    </div>
                    <div class="card-body p-0">
                        <?php
                        $totalItems = count($order['OrderDetails']);
                        $initialDisplay = 4; // Số sản phẩm hiển thị ban đầu khi dùng chế độ đóng/mở
                        
                        // Nếu có 10 sản phẩm trở xuống, hiển thị kiểu đóng/mở (nếu có >= 5 sản phẩm)
                        if ($totalItems <= 10) {
                            $useCollapseMode = $totalItems >= 5;
                            ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($order['OrderDetails'] as $index => $item):
                                    $displayClass = ($useCollapseMode && $index >= $initialDisplay) ? 'item-hidden d-none' : '';
                                    ?>
                                    <div class="list-group-item product-item <?php echo $displayClass; ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="item-number"><?php echo $index + 1; ?></div>
                                            <div class="flex-shrink-0 ms-2">
                                                <img src="<?php echo $item['ImageURL']; ?>"
                                                    alt="<?php echo $item['ProductName']; ?>" class="img-fluid rounded"
                                                    style="width: 80px; height: 100px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <h6 class="mb-1"><?php echo $item['ProductName']; ?></h6>
                                                        <div class="text-muted small">
                                                            <div>Tác giả: <?php echo $item['Author']; ?></div>
                                                            <div>Thể loại: <?php echo $item['Category']; ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-md-center mt-2 mt-md-0">
                                                        <div class="text-muted small">Số lượng</div>
                                                        <div class="fw-bold"><?php echo $item['Quantity']; ?></div>
                                                    </div>
                                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                                        <div class="text-muted small">Giá</div>
                                                        <div class="fw-bold">
                                                            <?= number_format((float) $item['Price'], 0, ',', '.') ?> đ
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <?php if ($useCollapseMode): ?>
                                    <div class="text-center py-3" id="showMoreContainer">
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="showMoreBtn">
                                            <i class="bi bi-chevron-down me-1"></i> Xem thêm
                                            <?php echo $totalItems - $initialDisplay; ?> sản phẩm
                                        </button>
                                    </div>
                                    <div class="text-center py-3 d-none" id="showLessContainer">
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="showLessBtn">
                                            <i class="bi bi-chevron-up me-1"></i> Thu gọn
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php
                            // Nếu có nhiều hơn 10 sản phẩm, hiển thị kiểu cuộn
                        } else {
                            ?>
                            <div class="list-group list-group-flush scrollable-items">
                                <?php foreach ($order['details'] as $index => $item): ?>
                                    <div class="list-group-item product-item">
                                        <div class="d-flex align-items-center">
                                            <div class="item-number"><?php echo $index + 1; ?></div>
                                            <div class="flex-shrink-0 ms-2">
                                                <img src="<?php echo $item['ImageURL']; ?>"
                                                    alt="<?php echo $item['ProductName']; ?>" class="img-fluid rounded"
                                                    style="width: 80px; height: 100px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <h6 class="mb-1"><?php echo $item['ProductName']; ?></h6>
                                                        <div class="text-muted small">
                                                            <div>Tác giả: <?php echo $item['Author']; ?></div>
                                                            <div>Thể loại: <?php echo $item['Category']; ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-md-center mt-2 mt-md-0">
                                                        <div class="text-muted small">Số lượng</div>
                                                        <div class="fw-bold"><?php echo $item['Quantity']; ?></div>
                                                    </div>
                                                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                                        <div class="text-muted small">Giá</div>
                                                        <div class="fw-bold">
                                                            <?= number_format((float) $item['Price'], 0, ',', '.') ?> đ
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Mã đơn hàng</span>
                                <span class="fw-bold"><?php echo $order['OrderID']; ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Ngày đặt hàng</span>
                                <span><?php echo date('H:i:s d/m/Y ', strtotime($order['OrderDate'])); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>Phương thức thanh toán</span>
                                <span><?php echo $order['PaymentMethod']; ?></span>
                            </li>
                        </ul>

                        <div class="order-summary">
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính</span>
                                <span><?= number_format((float) $order['TotalAmount'], 0, ',', '.') ?>đ</span>
                            </div>
                            <!-- <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển</span>
                                <span>0đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá</span>
                                <span>0đ</span>
                            </div> -->
                            <hr>
                            <div class="d-flex justify-content-between mb-0">
                                <span class="fw-bold">Tổng cộng</span>
                                <span
                                    class="fw-bold text-danger"><?= number_format((float) $order['TotalAmount'], 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-1 text-muted small">Người nhận</p>
                            <p class="mb-0 fw-medium"><?php echo $order['UserName']; ?></p>
                        </div>
                        <div class="mb-3">
                            <p class="mb-1 text-muted small">Số điện thoại</p>
                            <p class="mb-0 fw-medium"><?php echo $order['Phone']; ?></p>
                        </div>
                        <div class="mb-0">
                            <p class="mb-1 text-muted small">Địa chỉ giao hàng</p>
                            <p class="mb-0 fw-medium">
                                <?php echo $order['Address'] . ', ' . $order['Ward'] . ', ' . $order['District'] . ', ' . $order['City']; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <a href="/my-account/order-history" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách đơn hàng
                        </a>
                        <?php if ($order['Status'] == 'pending'): ?>
                            <button type="button" class="btn btn-danger w-100 mb-2" id="cancelOrderBtn">
                                <i class="fas fa-times-circle me-2"></i> Hủy đơn hàng
                            </button>
                        <?php endif; ?>
                        <a href="#" class="btn btn-outline-primary w-100">
                            <i class="fas fa-headset me-2"></i> Liên hệ hỗ trợ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
    .order-detail-banner {
        background-color: #f8f9fa;
    }

    .timeline-step {
        position: relative;
    }

    .timeline-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin: 0 auto 0.75rem;
        transition: all 0.3s ease;
    }

    .timeline-step.active .timeline-icon {
        background-color: #dc3545;
        color: white;
    }

    .timeline-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .timeline-step.active .timeline-text {
        color: #212529;
        font-weight: 500;
    }

    /* Scrollable items container */
    .scrollable-items {
        max-height: 350px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #dc3545 #f1f1f1;
    }

    /* Custom scrollbar styling */
    .scrollable-items::-webkit-scrollbar {
        width: 6px;
    }

    .scrollable-items::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .scrollable-items::-webkit-scrollbar-thumb {
        background: #dc3545;
        border-radius: 10px;
    }

    .scrollable-items::-webkit-scrollbar-thumb:hover {
        background: #b30000;
    }

    /* Product item hover effect */
    .product-item {
        position: relative;
        transition: all 0.2s ease;
    }

    .product-item:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }

    /* Item number styling */
    .item-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: bold;
    }
</style>

<!-- Custom JavaScript -->
<script type="module">
    import {
        showSweetAlert
    } from '/assets/client/js/util.js';

    document.addEventListener('DOMContentLoaded', function () {
        const cancelOrderBtn = document.getElementById('cancelOrderBtn');
        if (cancelOrderBtn) {
            cancelOrderBtn.addEventListener('click', function () {
                showSweetAlert('Bạn có chắc chắn muốn hủy đơn hàng <?php echo $order['OrderID']; ?>?', {
                    icon: 'warning',
                    title: 'Xác nhận hủy đơn hàng',
                    text: 'Lưu ý: Hành động này không thể hoàn tác.',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận hủy',
                    cancelButtonText: 'Đóng',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                }).then((result) => {
                    if (result.isConfirmed) {
                        cancelOrder(<?php echo $order['OrderID']; ?>);
                    }
                });
            });
        }

        function cancelOrder(orderId) {
            const formData = new FormData();
            formData.append('orderId', orderId);
            formData.append('status', 'canceled');

            <?php if (isset($_SESSION['UserID'])): ?>
                formData.append('userId', <?php echo $_SESSION['UserID']; ?>);
            <?php endif; ?>

            fetch('/api/orders/update-status', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message && data.message.includes("thành công")) {
                        showSweetAlert('Đơn hàng đã được hủy thành công!', {
                            icon: 'success',
                            title: 'Hủy đơn hàng thành công',
                            confirmButtonText: 'Quay lại danh sách đơn hàng',
                            confirmButtonColor: '#dc3545'
                        }).then(() => {
                            window.location.href = '/my-account/order-history';
                        });
                    } else {
                        showSweetAlert(data.message || 'Đã xảy ra lỗi khi hủy đơn hàng. Vui lòng thử lại sau.', {
                            icon: 'error',
                            title: 'Lỗi hủy đơn hàng',
                            confirmButtonText: 'Đã hiểu',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSweetAlert('Đã xảy ra lỗi khi kết nối đến máy chủ. Vui lòng thử lại sau.', {
                        icon: 'error',
                        title: 'Lỗi kết nối',
                        confirmButtonText: 'Đã hiểu',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }

        // Xử lý nút Xem thêm/Thu gọn
        const showMoreBtn = document.getElementById('showMoreBtn');
        const showLessBtn = document.getElementById('showLessBtn');

        if (showMoreBtn) {
            showMoreBtn.addEventListener('click', function () {
                document.querySelectorAll('.item-hidden').forEach(item => {
                    item.classList.remove('d-none');
                });

                document.getElementById('showMoreContainer').classList.add('d-none');
                document.getElementById('showLessContainer').classList.remove('d-none');
            });
        }

        if (showLessBtn) {
            showLessBtn.addEventListener('click', function () {
                document.querySelectorAll('.item-hidden').forEach(item => {
                    item.classList.add('d-none');
                });

                document.getElementById('showLessContainer').classList.add('d-none');
                document.getElementById('showMoreContainer').classList.remove('d-none');

                // Cuộn lên đầu danh sách sản phẩm
                const listGroup = document.querySelector('.list-group');
                if (listGroup) {
                    listGroup.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        }
    });
</script>