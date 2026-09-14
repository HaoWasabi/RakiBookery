<!-- Order Detail View -->
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết đơn hàng <?= $order['OrderID'] ?></h1>
        <div class="d-flex">
            <!-- <button type="button" class="btn btn-outline-secondary me-2" id="printOrder">
                <i class="fas fa-print me-2"></i>In đơn hàng
            </button> -->
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Information and Actions -->
        <div class="col-lg-8 mb-4">
            <div class="admin-card">
                <div class="admin-card-header d-flex justify-content-between align-items-center">
                    <h5 class="admin-card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Thông tin đơn hàng
                    </h5>
                    <div class="order-status">
                        <?php
                        $status_class = '';
                        $status_text = '';

                        switch ($order['Status']) {
                            case 'pending':
                                $status_class = 'status-warning';
                                $status_text = 'Chờ xác nhận';
                                break;
                            case 'confirmed':
                                $status_class = 'status-info';
                                $status_text = 'Đã xác nhận';
                                break;
                            case 'delivered_success':
                                $status_class = 'status-success';
                                $status_text = 'Đã giao hàng';
                                break;
                            case 'canceled':
                                $status_class = 'status-danger';
                                $status_text = 'Đã hủy';
                                break;
                        }
                        ?>
                        <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <!-- Order Timeline -->
                    <div class="order-timeline mb-4">
                        <div class="progress" style="height: 5px;">
                            <?php
                            $progress = 0;
                            switch ($order['Status']) {
                                case 'pending':
                                    $progress = 25;
                                    break;
                                case 'confirmed':
                                    $progress = 50;
                                    break;
                                case 'delivered_success':
                                    $progress = 100;
                                    break;
                                case 'canceled':
                                    $progress = 0;
                                    break;
                            }
                            ?>
                            <?php if ($order['Status'] != 'canceled'): ?>
                                <div class="progress-bar bg-gradient" role="progressbar" style="width: <?= $progress ?>%"
                                    aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            <?php else: ?>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"
                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            <?php endif; ?>
                        </div>
                        <div class="timeline-steps">
                            <div class="timeline-step <?= $order['Status'] != 'canceled' ? 'active' : '' ?>">
                                <div class="timeline-step-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="timeline-step-label">Đặt hàng</div>
                                <div class="timeline-step-date">
                                    <?= date('d/m/Y H:i:s', strtotime($order['OrderDate'])) ?>
                                </div>
                            </div>
                            <div
                                class="timeline-step <?= in_array($order['Status'], ['confirmed', 'delivered_success']) ? 'active' : '' ?>">
                                <div class="timeline-step-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="timeline-step-label">Xác nhận</div>
                                <div class="timeline-step-date">
                                    <!-- < ?= in_array($order['Status'], ['confirmed', 'delivered_success'])
                                        ? date('d/m/Y H:i', strtotime($order['confirmation_date'] ?? $order['OrderDate']))
                                        : '-' ?> -->
                                </div>
                            </div>
                            <div class="timeline-step <?= $order['Status'] == 'delivered_success' ? 'active' : '' ?>">
                                <div class="timeline-step-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <div class="timeline-step-label">Đã giao</div>
                                <div class="timeline-step-date">
                                    <!-- < ?= $order['Status'] == 'delivered_success'
                                        ? date('d/m/Y H:i', strtotime($order['delivery_date'] ?? $order['OrderDate']))
                                        : '-' ?> -->
                                </div>
                            </div>
                            <?php if ($order['Status'] == 'canceled'): ?>
                                <div class="timeline-step active canceled">
                                    <div class="timeline-step-icon">
                                        <i class="fas fa-ban"></i>
                                    </div>
                                    <div class="timeline-step-label">Đã hủy</div>
                                    <!-- <div class="timeline-step-date">
                                        < ?= date('d/m/Y H:i', strtotime($order['cancellation_date'] ?? $order['OrderDate'])) ?>
                                    </div> -->
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Details Grid -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="order-detail-card">
                                <h6 class="order-detail-title">
                                    <i class="fas fa-user me-2"></i>Thông tin khách hàng
                                </h6>
                                <div class="order-detail-content">
                                    <p><strong>Tên:</strong> <?= $order['UserName'] ?></p>
                                    <p><strong>Email:</strong> <?= $order['Email'] ?? 'Không có' ?></p>
                                    <p><strong>Số điện thoại:</strong> <?= $order['Phone'] ?></p>
                                    <!-- <p><strong>Loại khách hàng:</strong>
                                        <span class="badge bg-info">
                                            < ?= $order['Role'] ?? 'Thường' ?>
                                        </span>
                                    </p> -->
                                    <a href="/admin/users/user-info?id=<?= $order['UserID'] ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Xem hồ sơ
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="order-detail-card">
                                <h6 class="order-detail-title">
                                    <i class="fas fa-map-marker-alt me-2"></i>Địa chỉ giao hàng
                                </h6>
                                <div class="order-detail-content">
                                    <p><strong>Địa chỉ:</strong> <?= $order['Address'] ?></p>
                                    <p><strong>Phường/Xã:</strong> <?= $order['Ward'] ?></p>
                                    <p><strong>Quận/Huyện:</strong> <?= $order['District'] ?></p>
                                    <p><strong>Tỉnh/Thành phố:</strong> <?= $order['City'] ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="order-detail-card">
                                <h6 class="order-detail-title">
                                    <i class="fas fa-money-bill-wave me-2"></i>Thông tin thanh toán
                                </h6>
                                <div class="order-detail-content">
                                    <p><strong>Phương thức thanh toán:</strong> <?= $order['PaymentMethod'] ?></p>
                                    <!-- <p>
                                        <strong>Trạng thái:</strong>
                                        < ?php if (isset($order['PaymentStatus']) && $order['PaymentStatus'] === 'paid'): ?>
                                            <span class="status-badge status-success">Đã thanh toán</span>
                                        < ?php else: ?>
                                            <span class="status-badge status-pending">Chưa thanh toán</span>
                                        < ?php endif; ?>
                                    </p> -->
                                    <!-- <p><strong>Mã giao dịch:</strong> < ?= $order['TransactionID'] ?? 'Không có' ?></p> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="order-detail-card">
                                <h6 class="order-detail-title">
                                    <i class="fas fa-tags me-2"></i>Thông tin đơn hàng
                                </h6>
                                <div class="order-detail-content">
                                    <p><strong>Mã đơn hàng:</strong> <?= $order['OrderID'] ?></p>
                                    <p><strong>Ngày đặt:</strong>
                                        <?= date('d/m/Y H:i', strtotime($order['OrderDate'])) ?>
                                    </p>
                                    <p><strong>Tổng tiền:</strong> <span
                                            class="text-danger fw-bold"><?= number_format($order['TotalAmount']) ?>
                                            đ</span>
                                    </p>
                                    <!-- <p><strong>Ghi chú:</strong> < ?= $order['Note'] ?? 'Không có' ?></p> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <!--                     < ?php if ($order['Status'] != 'delivered_success' && $order['Status'] != 'canceled'): ?>
                        <div class="order-actions mt-4">
                            <h6 class="mb-3">Cập nhật trạng thái</h6>
                            <form id="updateOrderStatusForm" action="/api/users/toggle-status" method="POST"
                                class="row g-3">
                                <input type="hidden" name="orderId" value="< ?= $order['OrderID'] ?>">

                                <div class="col-md-4">
                                    <select class="form-select" name="status" required>
                                        <option value="pending" < ?= $order['Status'] == 'pending' ? 'selected' : '' ?>>Chờ xác
                                            nhận</option>
                                        <option value="confirmed" < ?= $order['Status'] == 'confirmed' ? 'selected' : '' ?>>Đã
                                            xác nhận</option>
                                        <option value="delivered_success" < ?= $order['Status'] == 'delivered_success' ? 'selected' : '' ?>>Đã giao</option>
                                        <option value="canceled" < ?= $order['Status'] == 'canceled' ? 'selected' : '' ?>>Đã hủy
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="note" placeholder="Ghi chú (nếu có)">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Cập nhật</button>
                                </div>
                            </form>
                        </div>
                    < ?php endif; ?> -->
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4 mb-4">
            <div class="admin-card mb-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>Tóm tắt đơn hàng
                    </h5>
                </div>
                <div class="admin-card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Tổng tiền hàng</span>
                            <span class="fw-medium"><?= number_format($order['Subtotal'] ?? $order['TotalAmount']) ?>
                                đ</span>
                        </li>
                        <!-- < ?php if (isset($order['Discount']) && $order['Discount'] > 0): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center text-success">
                                <span>Giảm giá</span>
                                <span class="fw-medium">-<?= number_format($order['Discount']) ?> đ</span>
                            </li>
                        < ?php endif; ?> -->
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                            <span>Tổng thanh toán</span>
                            <span class="text-danger"><?= number_format($order['TotalAmount']) ?> đ</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Customer Order History -->
            <!-- <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="admin-card-title mb-0">
                        <i class="fas fa-history me-2"></i>Lịch sử khách hàng
                    </h5>
                </div>
                <div class="admin-card-body">
                    <?php if (!empty($customerOrders)): ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tổng đơn hàng:</span>
                            <span class="fw-bold"><?= count($customerOrders) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tổng chi tiêu:</span>
                            <span class="fw-bold text-danger"><?= number_format($customerTotalSpent) ?> đ</span>
                        </div>
                        <h6 class="mt-4 mb-3">Các đơn hàng gần đây</h6>
                        <div class="customer-orders-list">
                            <?php foreach (array_slice($customerOrders, 0, 5) as $customerOrder): ?>
                                <div class="customer-order-item">
                                    <div class="d-flex justify-content-between">
                                        <a href="/admin/orders/order-detail?id=<?= $customerOrder['OrderID'] ?>"
                                            class="text-decoration-none">
                                            <?= $customerOrder['OrderID'] ?>
                                        </a>
                                        <span class="fw-medium"><?= number_format($customerOrder['TotalAmount']) ?> đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span
                                            class="small text-muted"><?= date('H:i:s d/m/Y', strtotime($customerOrder['OrderDate'])) ?></span>
                                        <?php
                                        $orderStatusClass = '';
                                        $orderStatusText = '';
                                        switch ($customerOrder['Status']) {
                                            case 'pending':
                                                $orderStatusClass = 'status-pending';
                                                $orderStatusText = 'Chờ xác nhận';
                                                break;
                                            case 'confirmed':
                                                $orderStatusClass = 'status-info';
                                                $orderStatusText = 'Đã xác nhận';
                                                break;
                                            case 'delivered_success':
                                                $orderStatusClass = 'status-success';
                                                $orderStatusText = 'Đã giao';
                                                break;
                                            case 'canceled':
                                                $orderStatusClass = 'status-error';
                                                $orderStatusText = 'Đã hủy';
                                                break;
                                        }
                                        ?>
                                        <span class="status-badge small <?= $orderStatusClass ?>"><?= $orderStatusText ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            Khách hàng chưa có đơn hàng nào khác.
                        </div>
                    <?php endif; ?>
                </div>
            </div> -->
        </div>
    </div>

    <!-- Order Items -->
    <div class="admin-card mb-4">
        <div class="admin-card-header d-flex justify-content-between align-items-center">
            <h5 class="admin-card-title mb-0">
                Sản phẩm trong đơn
            </h5>
            <span class="badge bg-primary rounded-pill"><?= count($orderDetails) ?> sản phẩm</span>
        </div>
        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="15%">Hình ảnh</th>
                            <th width="25%">Sản phẩm</th>
                            <th width="10%" class="text-center">Số lượng</th>
                            <th width="15%" class="text-end">Đơn giá</th>
                            <th width="15%" class="text-end">Thành tiền</th>
                            <th width="15%" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1; ?>
                        <?php foreach ($orderDetails as $item): ?>
                            <tr>
                                <td><?= $index++ ?></td>
                                <td>
                                    <img src="<?= $item['ImageURL'] ?>" alt="<?= $item['ProductName'] ?>"
                                        class="img-thumbnail" style="width: 80px; height: 100px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-medium"><?= $item['ProductName'] ?></div>
                                    <div class="text-muted small">
                                        <div>Tác giả: <?= $item['Author'] ?></div>
                                        <div>Thể loại: <?= $item['Category'] ?></div>
                                        <!-- <div>Mã sản phẩm: <?= $item['ProductID'] ?></div> -->
                                    </div>
                                </td>
                                <td class="text-center"><?= $item['Quantity'] ?></td>
                                <td class="text-end"><?= number_format($item['Price'], 0, ',', '.') ?> đ</td>
                                <td class="text-end fw-bold">
                                    <?= number_format($item['Price'] * $item['Quantity'], 0, ',', '.') ?> đ
                                </td>
                                <td class="text-center">
                                    <a href="/admin/products/product-detail?id=<?= $item['ProductID'] ?>"
                                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
                                        title="Xem sản phẩm">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Order Timeline Styles */
    .order-timeline {
        padding: 20px 0;
        position: relative;
    }

    .timeline-steps {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    .timeline-step {
        text-align: center;
        position: relative;
        width: 33%;
        opacity: 0.5;
        transition: all 0.3s ease;
    }

    .timeline-step.active {
        opacity: 1;
    }

    .timeline-step.canceled {
        color: var(--bs-danger);
    }

    .timeline-step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #f8f9fa;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #6c757d;
        position: relative;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .timeline-step.active .timeline-step-icon {
        background: linear-gradient(45deg, #ff6b6b, #cc2b5e);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 10px rgba(204, 43, 94, 0.3);
    }

    .timeline-step.canceled .timeline-step-icon {
        background-color: #f8d7da;
        color: #dc3545;
    }

    .timeline-step-label {
        margin-top: 8px;
        font-weight: 500;
    }

    .timeline-step-date {
        font-size: 0.75rem;
        color: #6c757d;
    }

    /* Order Detail Styles */
    .order-detail-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 15px;
        height: 100%;
        transition: all 0.3s ease;
        background-color: #fff;
    }

    .order-detail-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border-color: #dee2e6;
        transform: translateY(-3px);
    }

    .order-detail-title {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
        color: #495057;
        font-weight: 600;
    }

    .order-detail-content p {
        margin-bottom: 8px;
    }

    /* Customer Order History Styles */
    .customer-orders-list {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .customer-orders-list::-webkit-scrollbar {
        width: 5px;
    }

    .customer-orders-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .customer-orders-list::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .customer-orders-list::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    .customer-order-item {
        padding: 10px;
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .customer-order-item:hover {
        background-color: #f8f9fa;
    }

    .customer-order-item:last-child {
        border-bottom: none;
    }

    /* Progress Bar Gradient */
    .progress {
        height: 5px;
        overflow: hidden;
        border-radius: 10px;
    }

    .bg-gradient {
        background: linear-gradient(to right, #ff6b6b, #cc2b5e);
        box-shadow: 0 2px 5px rgba(204, 43, 94, 0.2);
    }

    /* Order Actions */
    .order-actions {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    /* Table styles */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        font-weight: 600;
        color: #495057;
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }

    .item-number {
        width: 30px;
        height: 30px;
        background-color: #f8f9fa;
        color: #6c757d;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
    }

    /* Status badges */
    .status-badge {
        padding: 0.4em 0.8em;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .status-badge.small {
        font-size: 0.7rem;
        padding: 0.2em 0.6em;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-success {
        background-color: #d4edda;
        color: #155724;
    }

    .status-error {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Handle print order
        /* document.getElementById('printOrder').addEventListener('click', function () {
            Swal.fire({
                title: 'In đơn hàng',
                text: 'Đang chuẩn bị in đơn hàng < ?= $order['OrderID'] ?>',
        icon: 'info',
            showCancelButton: false,
                confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
    });
        }); */

        // Handle order status update
        const updateOrderStatusForm = document.getElementById('updateOrderStatusForm');
        if (updateOrderStatusForm) {
            updateOrderStatusForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng.',
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                    });
            });
        }
    });
</script>