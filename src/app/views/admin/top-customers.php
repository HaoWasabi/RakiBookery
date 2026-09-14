<?php
function getOrderStatusBadgeClass($status)
{
    return match ($status) {
        'pending' => 'bg-warning',
        'confirmed' => 'bg-info',
        'delivered_success' => 'bg-success',
        'canceled' => 'bg-danger',
        default => 'bg-secondary'
    };
}
function getOrderStatusLabel($status)
{
    return match ($status) {
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'delivered_success' => 'Đã giao hàng',
        'canceled' => 'Đã hủy',
        default => 'Không xác định'
    };
}

?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Top khách hàng</h1>
        <a href="/admin/dashboard" class="btn btn-secondary">
            <i class="fa-duotone fa-arrow-left me-2"></i> Quay lại tổng quan
        </a>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-primary">Thống kê khách hàng mua sắm nhiều nhất</h6>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="button" id="fetch-data-btn" class="btn btn-primary flex-grow-1">
                            <i class="fa fa-search mr-2"></i> Thống kê
                        </button>
                        <button type="button" id="reset-date-btn" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Đặt lại
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading indicator -->
            <div id="loading-indicator" class="text-center my-5 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tải dữ liệu...</p>
            </div>

            <!-- Alert message for no data or errors -->
            <div id="alert-container" class="mb-4 d-none"></div>

            <!-- Data container -->
            <div id="data-container">
                <?php if (isset($topCustomers) && !empty($topCustomers)): ?>
                    <!-- Visualization Chart -->
                    <div class="mb-4">
                        <canvas id="topCustomersChart" height="250"></canvas>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover datatable">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">Hạng</th>
                                    <th width="20%">Khách hàng</th>
                                    <th width="15%">Tổng mua</th>
                                    <th width="60%">Đơn hàng</th>
                                </tr>
                            </thead>
                            <tbody id="customers-table-body">
                                <?php foreach ($topCustomers as $index => $customer): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?= $index + 1 ?>
                                        </td>
                                        <td>
                                            <a href="/admin/users/user-info?id=<?= $customer['UserID'] ?>"
                                                class="text-primary fw-bold">
                                                <?= $customer['Name'] ?>
                                            </a>
                                            <div class="text-muted">
                                                <?= $customer['Email'] ?>
                                            </div>
                                            <div class="text-muted">
                                                <?= $customer['Phone'] ?? 'Chưa cập nhật' ?>
                                            </div>
                                        </td>
                                        <td class="fw-bold"><?= number_format($customer['TotalAmount'], 0, ',', '.') ?> ₫
                                        </td>
                                        <td>
                                            <?php if (!empty($customer['Orders'])): ?>
                                                <div class="orders-list" style="max-height: 200px; overflow-y: auto;">
                                                    <?php foreach ($customer['Orders'] as $order): ?>
                                                        <div class="order-item border-bottom py-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div class="order-details">
                                                                    <a href="/admin/orders/order-detail?id=<?= $order['OrderID'] ?>"
                                                                        class="text-decoration-none fw-bold text-primary">
                                                                        Đơn <?= $order['OrderID'] ?>
                                                                    </a>
                                                                    <div class="text-muted small mt-1">
                                                                        <?= date('H:i:s d/m/Y', strtotime($order['OrderDate'])) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="order-meta d-flex align-items-center gap-2">
                                                                    <span class="fw-bold">
                                                                        <?= number_format($order['TotalAmount'], 0, ',', '.') ?> ₫
                                                                    </span>
                                                                    <span
                                                                        class="badge <?= getOrderStatusBadgeClass($order['Status']) ?>">
                                                                        <?= getOrderStatusLabel($order['Status']) ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Không có đơn hàng</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php elseif (isset($_GET['start_date']) && isset($_GET['end_date'])): ?>
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="fa-duotone fa-triangle-exclamation me-2 fs-4"></i>
                        <div>Không có khách hàng nào mua sắm trong khoảng thời gian đã chọn.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .ribbon-wrapper {
        position: relative;
        display: inline-block;
        width: 32px;
        height: 32px;
    }

    .ribbon {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1rem;
    }

    .gold {
        background-color: #FFD700;
        color: #8B6914;
    }

    .silver {
        background-color: #C0C0C0;
        color: #5E5E5E;
    }

    .bronze {
        background-color: #CD7F32;
        color: #7E4B1C;
    }

    .datatable th,
    .datatable td {
        vertical-align: middle;
    }

    .orders-list::-webkit-scrollbar {
        width: 5px;
    }

    .orders-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .orders-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }

    .orders-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const fetchDataBtn = document.getElementById('fetch-data-btn');
        const resetDateBtn = document.getElementById('reset-date-btn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const alertContainer = document.getElementById('alert-container');
        const dataContainer = document.getElementById('data-container');

        let topCustomersChart = null;

        // Fetch data when page loads
        // fetchTopCustomersData();
        renderTopCustomersData(<?= json_encode($topCustomers) ?>);

        // Function to validate date range
        function validateDateRange() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);

            if (startDateInput.value && endDateInput.value && startDate > endDate) {
                showError('Lỗi ngày tháng', 'Ngày bắt đầu không thể sau ngày kết thúc');
                return false;
            }
            return true;
        }

        function fetchTopCustomersData() {
            if (!validateDateRange()) {
                return;
            }

            // Show loading indicator
            loadingIndicator.classList.remove('d-none');
            dataContainer.classList.add('d-none');
            alertContainer.classList.add('d-none');

            // Destroy existing chart if any
            if (topCustomersChart) {
                topCustomersChart.destroy();
                topCustomersChart = null;
            }

            // Prepare query parameters
            const params = new URLSearchParams();
            if (startDateInput.value) {
                params.append('start_date', startDateInput.value);
            }
            if (endDateInput.value) {
                params.append('end_date', endDateInput.value);
            }

            // Fetch data
            fetch(`/api/statistics/top-customers?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    setTimeout(() => {
                        // Hide loading indicator
                        loadingIndicator.classList.add('d-none');

                        if (data.success && data.data && data.data.length > 0) {
                            // Show data container
                            dataContainer.classList.remove('d-none');

                            // Render data
                            renderTopCustomersData(data.data);
                        } else {
                            // Show no data alert
                            alertContainer.innerHTML = `
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="fa-duotone fa-triangle-exclamation me-2 fs-4"></i>
                                <div>Không có khách hàng nào mua sắm trong khoảng thời gian đã chọn.</div>
                            </div>
                        `;
                            alertContainer.classList.remove('d-none');
                        }
                    }, 100);
                })
                .catch(error => {
                    loadingIndicator.classList.add('d-none');
                    console.error('Error fetching data:', error);

                    alertContainer.innerHTML = `
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fa-duotone fa-circle-exclamation me-2 fs-4"></i>
                            <div>Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại sau.</div>
                        </div>
                    `;
                    alertContainer.classList.remove('d-none');
                });
        }

        // Function to render table rows
        function renderTopCustomersData(topCustomers) {
            // Render chart
            renderChart(topCustomers);

            // Render table
            const tableBody = document.getElementById('customers-table-body');
            let htmlContent = '';

            topCustomers.forEach((customer, index) => {
                htmlContent += `
                    <tr>
                        <td class="text-center">
                            ${index + 1}
                        </td>
                        <td>
                            <a href="/admin/users/user-info?id=${customer.UserID}" class="text-decoration-none text-primary fw-bold">
                                ${customer.Name}
                            </a>
                            <div class="text-muted">
                                ${customer.Email}
                            </div>
                            <div class="text-muted">
                                ${customer.Phone || 'Chưa cập nhật'}
                            </div>
                        </td>
                        <td class="fw-bold">${new Intl.NumberFormat('vi-VN').format(customer.TotalAmount)} ₫
                        </td>
                        <td>
                `;

                if (customer.Orders && customer.Orders.length > 0) {
                    htmlContent += `<div class="orders-list" style="max-height: 200px; overflow-y: auto;">`;

                    customer.Orders.forEach(order => {
                        htmlContent += `
                            <div class="order-item border-bottom py-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="order-details">
                                        <a href="/admin/orders/order-detail?id=${order.OrderID}"
                                            class="text-decoration-none fw-bold text-primary">
                                            Đơn ${order.OrderID}
                                        </a>
                                        <div class="text-muted small mt-1">
                                            ${new Date(order.OrderDate).toLocaleString('vi-VN')}
                                        </div>
                                    </div>
                                    <div class="order-meta d-flex align-items-center gap-2">
                                        <span class="fw-bold">
                                            ${new Intl.NumberFormat('vi-VN').format(order.TotalAmount)} ₫
                                        </span>
                                        <span class="badge ${getOrderStatusBadgeClass(order.Status)}">
                                            ${getOrderStatusLabel(order.Status)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    htmlContent += `</div>`;
                } else {
                    htmlContent += `<span class="text-muted">Không có đơn hàng</span>`;
                }

                htmlContent += `
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = htmlContent;
        }

        // Function to render chart
        function renderChart(topCustomers) {
            const ctx = document.getElementById('topCustomersChart');

            // Prepare chart data
            const customerNames = topCustomers.map(customer => customer.Name);
            const totalAmounts = topCustomers.map(customer => customer.TotalAmount);
            const orderCounts = topCustomers.map(customer => customer.OrderCount);

            // Define gradient colors for bars
            const colors = [
                'rgba(231, 76, 60, 0.8)', // Red - First place
                'rgba(52, 152, 219, 0.8)', // Blue - Second place
                'rgba(46, 204, 113, 0.8)', // Green - Third place
                'rgba(155, 89, 182, 0.8)', // Purple - Fourth place
                'rgba(243, 156, 18, 0.8)' // Orange - Fifth place
            ];

            topCustomersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: customerNames,
                    datasets: [{
                        label: 'Tổng giá trị mua hàng (VNĐ)',
                        data: totalAmounts,
                        backgroundColor: colors,
                        borderColor: colors.map(color => color.replace('0.8', '1')),
                        borderWidth: 1,
                        borderRadius: 5,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const index = context.dataIndex;
                                    const revenue = context.raw;

                                    return [
                                        'Số đơn: ' + orderCounts[index] + ' đơn',
                                        'Tổng mua: ' + new Intl.NumberFormat('vi-VN').format(revenue) + ' ₫',
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Function to get order status badge class
        function getOrderStatusBadgeClass(status) {
            const statusMap = {
                'pending': 'bg-warning',
                'confirmed': 'bg-info',
                'delivered_success': 'bg-success',
                'canceled': 'bg-danger'
            };
            return statusMap[status] || 'bg-secondary';
        }

        // Function to get order status label
        function getOrderStatusLabel(status) {
            const labelMap = {
                'pending': 'Chờ xác nhận',
                'confirmed': 'Đã xác nhận',
                'delivered_success': 'Đã giao hàng',
                'canceled': 'Đã hủy'
            };
            return labelMap[status] || 'Không xác định';
        }

        // Fetch data button click event
        fetchDataBtn.addEventListener('click', fetchTopCustomersData);

        // Reset date button click event
        resetDateBtn.addEventListener('click', function () {
            startDateInput.value = '';
            endDateInput.value = '';
            fetchTopCustomersData();
        });

        // Initial load if there are date parameters in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('start_date') || urlParams.has('end_date')) {
            startDateInput.value = urlParams.get('start_date') || '';
            endDateInput.value = urlParams.get('end_date') || '';
            // Small delay to ensure the page is fully loaded
            setTimeout(fetchTopCustomersData, 100);
        }
    });
</script>