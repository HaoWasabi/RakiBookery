<!-- Page Header -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tổng quan</h1>
        <a href="/admin/top-customers" class="btn btn-primary">
            <i class="fa-duotone fa-crown me-2"></i> Top khách hàng
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card dashboard-card-orders h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-uppercase mb-1 fw-bold text-primary">
                                Tổng đơn hàng
                            </div>
                            <div class="h3 mb-0 fw-bold text-gray-800">
                                <?= number_format($stats['totalOrders']) ?>
                            </div>
                            <!--  <div class="mt-2 small text-success">
                                <i class="fa-duotone fa-arrow-trend-up me-1"></i>
                                <span>12% so với tháng trước</span>
                            </div> -->
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-primary-light">
                                <i class="fa-regular fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card dashboard-card-revenue h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-uppercase mb-1 fw-bold text-success">
                                Doanh thu
                            </div>
                            <div class="h3 mb-0 fw-bold text-gray-800">
                                <?= number_format($stats['totalRevenue']) ?> ₫
                            </div>
                            <!-- <div class="mt-2 small text-success">
                                <i class="fa-duotone fa-arrow-trend-up me-1"></i>
                                <span>8.3% so với tháng trước</span>
                            </div> -->
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-success-light">
                                <i class="fa-duotone fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card dashboard-card-users h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-uppercase mb-1 fw-bold text-info">
                                Người dùng
                            </div>
                            <div class="h3 mb-0 fw-bold text-gray-800">
                                <?= number_format($stats['totalUsers']) ?>
                            </div>
                            <!-- <div class="mt-2 small text-success">
                                <i class="fa-duotone fa-arrow-trend-up me-1"></i>
                                <span>5.2% so với tháng trước</span>
                            </div> -->
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-info-light">
                                <i class="fa-duotone fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dashboard-card dashboard-card-products h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs text-uppercase mb-1 fw-bold text-warning">
                                Sản phẩm
                            </div>
                            <div class="h3 mb-0 fw-bold text-gray-800">
                                <?= number_format($stats['totalProducts']) ?>
                            </div>
                            <!-- <div class="mt-2 small text-success">
                                <i class="fa-duotone fa-arrow-trend-up me-1"></i>
                                <span>3.7% so với tháng trước</span>
                            </div> -->
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-warning-light">
                                <i class="fa-duotone fa-books"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- Orders by Category Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary" id="category-chart-title">Đơn hàng theo thể loại</h6>
                    <div class="dropdown no-arrow">
                        <select id="category-time-period" class="form-select form-select-sm">
                            <option value="1month">1 tháng qua</option>
                            <option value="6months">6 tháng qua</option>
                            <option value="1year">1 năm qua</option>
                            <option value="all" selected>Toàn thời gian</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie position-relative">
                        <div id="chart-content">
                            <canvas id="categoryPieChart" height="300"></canvas>
                        </div>
                        <div id="category-chart-loading"
                            class="position-absolute top-50 start-50 translate-middle text-center"
                            style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <div class="mt-2 text-primary fw-bold">Đang tải dữ liệu...</div>
                        </div>
                    </div>
                    <div class="mt-4 text-center small d-flex flex-wrap justify-content-center gap-3"
                        id="category-legend">
                        <!-- Category legends will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Month Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary" id="revenue-chart-title">Doanh thu theo tháng</h6>
                    <div class="dropdown no-arrow">
                        <select id="revenue-year-selector" class="form-select form-select-sm">
                            <?php
                            $currentYear = (int) date('Y');
                            $startYear = 2024;
                            for ($year = $currentYear; $year >= $startYear; $year--) {
                                $selected = ($year === $currentYear) ? 'selected' : '';
                                echo "<option value=\"$year\" $selected>Năm $year</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-bar position-relative">
                        <div id="revenue-chart-content">
                            <canvas id="revenueBarChart" height="300"></canvas>
                        </div>
                        <div id="revenue-chart-loading"
                            class="position-absolute top-50 start-50 translate-middle text-center"
                            style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <div class="mt-2 text-primary fw-bold">Đang tải dữ liệu...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">5 đơn hàng gần đây</h6>
                    <a href="/admin/orders" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng tiền</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="recent-orders-table-body">
                                <!-- Đổ dữ liệu doanh thu tại đây -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
// Helper functions for the view
function getStatusClass($status)
{
    return match ($status) {
        'pending' => 'bg-warning',
        'confirmed' => 'bg-info',
        'delivered_success' => 'bg-success',
        'canceled' => 'bg-danger',
        default => 'bg-secondary'
    };
}

function getStatusLabel($status)
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

<script>
    // Helper function to get the Bootstrap background class based on status
    function getStatusClass(status) {
        const statusClasses = {
            'pending': 'bg-warning',
            'confirmed': 'bg-info',
            'delivered_success': 'bg-success',
            'canceled': 'bg-danger'
        };
        return statusClasses[status] || 'bg-secondary';
    }

    function getStatusLabel(status) {
        const statusLabels = {
            'pending': 'Chờ xác nhận',
            'confirmed': 'Đã xác nhận',
            'delivered_success': 'Đã giao hàng',
            'canceled': 'Đã hủy'
        };
        return statusLabels[status] || 'Không xác định';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Định nghĩa biến toàn cục cho biểu đồ
        let categoryPieChart = null;
        let revenueBarChart = null;

        // Hiển thị loading indicator cho biểu đồ pie chart
        function showCategoryLoading() {
            document.getElementById('category-chart-loading').style.display = 'block';
            document.getElementById('chart-content').style.visibility = 'hidden';
            document.getElementById('category-legend').style.visibility = 'hidden';
        }

        // Ẩn loading indicator cho biểu đồ pie chart
        function hideCategoryLoading() {
            document.getElementById('category-chart-loading').style.display = 'none';
            document.getElementById('chart-content').style.visibility = 'visible';
            document.getElementById('category-legend').style.visibility = 'visible';
        }

        // Hiển thị loading indicator cho biểu đồ doanh thu
        function showRevenueLoading() {
            document.getElementById('revenue-chart-loading').style.display = 'block';
            document.getElementById('revenue-chart-content').style.visibility = 'hidden';
        }

        // Ẩn loading indicator cho biểu đồ doanh thu
        function hideRevenueLoading() {
            document.getElementById('revenue-chart-loading').style.display = 'none';
            document.getElementById('revenue-chart-content').style.visibility = 'visible';
        }

        // Hàm tạo màu ngẫu nhiên cho biểu đồ
        function getChartColor(index) {
            // Use HSL to generate pleasant colors by varying the hue
            const hue = (index * 137.508) % 360; // Golden angle for even hue distribution
            const saturation = 60; // 60% saturation for vibrant but not overpowering colors
            const lightness = 65; // 65% lightness for a balanced, not-too-dark-or-bright tone

            // Convert HSL to RGB
            const s = saturation / 100;
            const l = lightness / 100;
            const c = (1 - Math.abs(2 * l - 1)) * s;
            const x = c * (1 - Math.abs(((hue / 60) % 2) - 1));
            const m = l - c / 2;

            let r, g, b;
            if (hue < 60) {
                r = c;
                g = x;
                b = 0;
            } else if (hue < 120) {
                r = x;
                g = c;
                b = 0;
            } else if (hue < 180) {
                r = 0;
                g = c;
                b = x;
            } else if (hue < 240) {
                r = 0;
                g = x;
                b = c;
            } else if (hue < 300) {
                r = x;
                g = 0;
                b = c;
            } else {
                r = c;
                g = 0;
                b = x;
            }

            // Convert RGB to hex
            r = Math.round((r + m) * 255);
            g = Math.round((g + m) * 255);
            b = Math.round((b + m) * 255);

            return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
        }

        // Hàm tải dữ liệu đơn hàng theo danh mục
        async function loadCategoryData(period = 'all') {
            try {
                // Hiển thị loading
                showCategoryLoading();

                // Cập nhật tiêu đề theo khoảng thời gian đã chọn trước khi tải dữ liệu
                const periodText = document.getElementById('category-time-period').options[
                    document.getElementById('category-time-period').selectedIndex
                ].text;
                document.getElementById('category-chart-title').textContent = `Đơn hàng theo thể loại (${periodText})`;

                const response = await fetch(`/api/statistics/orders-by-category?period=${period}`);
                const result = await response.json();

                if (result.success) {
                    // Cập nhật biểu đồ với dữ liệu mới
                    updateCategoryChart(result.data);
                } else {
                    console.error('Error:', result.message || 'Unknown error');
                }
            } catch (error) {
                console.error('Error loading category data:', error);
            } finally {
                hideCategoryLoading();
            }
        }

        // Hàm cập nhật biểu đồ danh mục
        function updateCategoryChart(categoryData) {
            const categoryLabels = categoryData.map(item => item.Category);
            const categoryValues = categoryData.map(item => parseFloat(item.TotalAmount));
            const orderCounts = categoryData.map(item => parseInt(item.OrderCount));
            const chartColors = [];

            // Tạo mảng màu cho từng danh mục
            categoryLabels.forEach((_, index) => {
                chartColors.push(getChartColor(index));
            });

            // Cập nhật phần tử chú thích
            const legendContainer = document.getElementById('category-legend');
            legendContainer.innerHTML = '';

            categoryData.forEach((item, index) => {
                const span = document.createElement('span');
                span.classList.add('me-2', 'mb-2');
                span.innerHTML = `
                    <i class="fas fa-circle" style="color: ${chartColors[index]}"></i> 
                    ${item.Category}
                `;
                legendContainer.appendChild(span);
            });

            // Cập nhật hoặc tạo mới biểu đồ
            const ctx = document.getElementById("categoryPieChart");

            if (categoryPieChart) {
                categoryPieChart.data.labels = categoryLabels;
                categoryPieChart.data.datasets[0].data = categoryValues;
                categoryPieChart.data.datasets[0].backgroundColor = chartColors;
                categoryPieChart.data.datasets[0].hoverBackgroundColor = chartColors.map(color => adjustColor(color, -15));
                categoryPieChart.data.datasets[0].orderCounts = orderCounts;
                categoryPieChart.update();
            } else {
                categoryPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryValues,
                            backgroundColor: chartColors,
                            hoverBackgroundColor: chartColors.map(color => adjustColor(color, -15)),
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                            orderCounts: orderCounts
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                                position: 'bottom'
                            },
                            tooltip: {
                                backgroundColor: "#2c3e50",
                                bodyColor: "#ffffff",
                                titleColor: "#f1c40f",
                                borderColor: "#34495e",
                                borderWidth: 1,
                                padding: 15,
                                displayColors: false,
                                caretPadding: 10,
                                callbacks: {
                                    label: function (tooltipItem) {
                                        const value = tooltipItem.raw;
                                        const index = tooltipItem.dataIndex;
                                        const orderCount = tooltipItem.dataset.orderCounts[index];

                                        return [
                                            `Số đơn: ${orderCount} đơn hàng`,
                                            `Doanh thu: ${new Intl.NumberFormat('vi-VN').format(value)} ₫`,
                                        ];
                                    }
                                }
                            }
                        },
                    },
                });
            }
        }

        const currentYear = new Date().getFullYear();

        // Chuyển đổi dữ liệu tháng từ số sang tên tháng
        const monthNames = [
            'Tháng 1',
            'Tháng 2',
            'Tháng 3',
            'Tháng 4',
            'Tháng 5',
            'Tháng 6',
            'Tháng 7',
            'Tháng 8',
            'Tháng 9',
            'Tháng 10',
            'Tháng 11',
            'Tháng 12'
        ];

        // Tải dữ liệu doanh thu theo tháng cho năm được chọn
        async function loadRevenueData(year = currentYear) {
            try {
                // Hiển thị loading
                showRevenueLoading();

                // Cập nhật tiêu đề biểu đồ
                document.getElementById('revenue-chart-title').textContent = `Doanh thu theo tháng (Năm ${year})`;

                const response = await fetch(`/api/statistics/revenue-by-year?year=${year}`);
                const result = await response.json();

                if (result.success) {
                    result.data.forEach(item => {
                        item.MonthName = monthNames[item.Month - 1];
                    });

                    updateRevenueChart(result.data);
                } else {
                    console.error('Error:', result.message || 'Unknown error');
                }
            } catch (error) {
                console.error('Error loading revenue data:', error);
            } finally {
                // Ẩn loading sau khi hoàn thành
                hideRevenueLoading();
            }
        }

        // Hàm cập nhật biểu đồ doanh thu
        function updateRevenueChart(revenueData) {
            const monthNames = revenueData.map(item => item.MonthName);
            const revenueValues = revenueData.map(item => parseFloat(item.TotalAmount));
            const orderCounts = revenueData.map(item => parseInt(item.OrderCount));

            if (revenueBarChart) {
                revenueBarChart.data.labels = monthNames;
                revenueBarChart.data.datasets[0].data = revenueValues;
                revenueBarChart.data.datasets[0].orderCounts = orderCounts;
                revenueBarChart.update();
            } else {
                const ctx = document.getElementById("revenueBarChart");
                revenueBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: monthNames,
                        datasets: [{
                            label: "Doanh thu",
                            backgroundColor: "#e74c3c",
                            hoverBackgroundColor: "#c0392b",
                            borderColor: "#e74c3c",
                            data: revenueValues,
                            barThickness: 'flex',
                            maxBarThickness: 25,
                            borderRadius: 5,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return new Intl.NumberFormat('vi-VN').format(value) + ' ₫';
                                    }
                                },
                                grid: {
                                    color: "rgb(234, 236, 244)",
                                    zeroLineColor: "rgb(234, 236, 244)",
                                    drawBorder: false,
                                    borderDash: [2],
                                    zeroLineBorderDash: [2]
                                }
                            },
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: "#2c3e50",
                                bodyColor: "#ffffff",
                                titleColor: "#f1c40f",
                                borderColor: "#34495e",
                                borderWidth: 1,
                                padding: 15,
                                displayColors: false,
                                callbacks: {
                                    label: function (context) {
                                        const index = context.dataIndex;
                                        const revenue = context.raw;
                                        const orders = revenueData[index]?.OrderCount ?? 0;
                                        return [
                                            'Số đơn: ' + orders,
                                            'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(revenue) + ' ₫',
                                        ];
                                    }
                                }
                            },
                        },
                    }
                });
            }
        }

        // Xử lý sự kiện thay đổi khoảng thời gian
        document.getElementById('category-time-period').addEventListener('change', function () {
            loadCategoryData(this.value);
        });

        // Xử lý sự kiện thay đổi năm cho biểu đồ doanh thu
        document.getElementById('revenue-year-selector').addEventListener('change', function () {
            loadRevenueData(this.value);
        });

        // Helper function to adjust colors
        function adjustColor(color, amount) {
            return '#' + color.replace(/^#/, '').replace(/../g, color => ('0' + Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2));
        }

        // Hàm để tải dữ liệu đơn hàng gần đây
        async function loadRecentOrders() {
            try {
                // Hiển thị trạng thái đang tải
                const tableBody = document.getElementById('recent-orders-table-body');
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <span>Đang tải dữ liệu...</span>
                        </td>
                    </tr>
                `;

                const response = await fetch('/api/orders/recentOrders');
                const result = await response.json();

                if (result.success) {
                    updateRecentOrdersTable(result.data);
                } else {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-3">
                                <div class="alert alert-warning mb-0">Không thể tải dữ liệu đơn hàng</div>
                            </td>
                        </tr>
                    `;
                    console.error('Lỗi:', result.message || 'Lỗi không xác định');
                }
            } catch (error) {
                console.error('Lỗi tải đơn hàng gần đây:', error);
                const tableBody = document.getElementById('recent-orders-table-body');
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-3">
                            <div class="alert alert-danger mb-0">Đã xảy ra lỗi khi tải dữ liệu: ${error.message}</div>
                        </td>
                    </tr>
                `;
            }
        }

        // Cập nhật bảng đơn hàng gần đây với dữ liệu mới
        function updateRecentOrdersTable(orders) {
            const tableBody = document.getElementById('recent-orders-table-body');

            if (!orders || orders.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-3">Không có đơn hàng nào</td>
                    </tr>
                `;
                return;
            }

            let html = '';

            orders.forEach(order => {
                const statusClass = getStatusClass(order.Status);
                const statusLabel = getStatusLabel(order.Status);

                html += `
                    <tr>
                        <td>${order.OrderID}</td>
                        <td>${order.CustomerName}</td>
                        <td>${formatDate(order.OrderDate)}</td>
                        <td><span class="badge ${statusClass}">${statusLabel}</span></td>
                        <td>${formatCurrency(order.TotalAmount)} ₫</td>
                        <td>
                            <a href="/admin/orders/order-detail?id=${order.OrderID}" 
                               class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
        }

        // Hàm định dạng ngày tháng
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });
        }

        // Hàm định dạng tiền tệ
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        // Khởi tạo khi trang tải
        loadCategoryData('all');
        loadRevenueData();
        loadRecentOrders();
    });
</script>