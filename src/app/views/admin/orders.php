<!-- Orders Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý đơn hàng</h1>
        <!-- <button type="button" class="btn btn-outline-secondary" id="exportOrders">
            <i class="fas fa-file-export me-2"></i>Xuất báo cáo
        </button> -->
    </div>
    <!-- Orders Stats Cards -->
    <!--     <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card bg-warning text-white h-100">
                <div class="stats-card-body">
                    <div class="stats-card-value">< ?= $statusCounts['pending'] ?></div>
                    <div class="stats-card-label">Chờ xác nhận</div>
                    <div class="stats-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card bg-info text-white h-100">
                <div class="stats-card-body">
                    <div class="stats-card-value">< ?= $statusCounts['confirmed'] ?></div>
                    <div class="stats-card-label">Đã xác nhận</div>
                    <div class="stats-card-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card bg-success text-white h-100">
                <div class="stats-card-body">
                    <div class="stats-card-value">< ?= $statusCounts['delivered_success'] ?></div>
                    <div class="stats-card-label">Đã giao</div>
                    <div class="stats-card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card bg-danger text-white h-100">
                <div class="stats-card-body">
                    <div class="stats-card-value">< ?= $statusCounts['canceled'] ?></div>
                    <div class="stats-card-label">Đã hủy</div>
                    <div class="stats-card-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Tìm kiếm & Lọc</h6>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="orderStatus" class="form-label">Trạng thái đơn hàng</label>
                        <select class="form-select" id="orderStatus">
                            <option value="">Tất cả</option>
                            <option value="pending">Chờ xác nhận</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <!-- <option value="shipping">Đang giao</option> -->
                            <option value="delivered_success">Đã giao</option>
                            <option value="canceled">Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="paymentMethod" class="form-label">Phương thức thanh toán</label>
                        <select class="form-select" id="paymentMethod">
                            <option value="">Tất cả</option>
                            <?php foreach ($paymentMethods as $paymentMethod): ?>
                                <option value="<?= $paymentMethod['PaymentMethodID'] ?>">
                                    <?= $paymentMethod['Name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="dateFrom" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="dateFrom" name="start_date">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="dateTo" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="dateTo" name="end_date">
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" id="resetFilterBtn" class="btn btn-secondary me-2">
                        <i class="fas fa-redo mr-2"></i> Đặt lại
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-2"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading-indicator" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2">Đang tải dữ liệu đơn hàng...</p>
    </div>

    <!-- Orders Table -->
    <div class="card mb-4 d-none" id="data-container">
        <div class="card-body">
            <div class="table-responsive"> 
                <table class="table table-bordered table-hover w-100" id="ordersTable">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">Mã đơn</th>
                            <th width="10%">Khách hàng</th>
                            <th width="10%">Ngày đặt</th>
                            <th width="12%">Tổng tiền</th>
                            <th width="15%">Địa chỉ giao hàng</th>
                            <th width="15%">Phương thức thanh toán</th>
                            <th width="10%">Trạng thái</th>
                            <th width="20%" class="no-sort">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="orders-table-body">
                        <!-- Orders data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const orderStatusSelect = document.getElementById('orderStatus');
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const dateFromInput = document.getElementById('dateFrom');
        const dateToInput = document.getElementById('dateTo');
        const searchBtn = document.getElementById('filterForm').querySelector('button[type="submit"]');
        const resetBtn = document.getElementById('resetFilterBtn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const dataContainer = document.getElementById('data-container');

        let ordersTable = null;

        // Initial data load
        loadOrdersData();

        // Search button click event
        document.getElementById('filterForm').addEventListener('submit', function (e) {
            e.preventDefault();
            // Validate date range before loading data
            if (validateDateRange()) {
                loadOrdersData();
            }
        });

        // Reset button click event
        resetBtn.addEventListener('click', function () {
            orderStatusSelect.value = '';
            paymentMethodSelect.value = '';
            dateFromInput.value = '';
            dateToInput.value = '';
            loadOrdersData();
        });

        // Function to validate date range
        function validateDateRange() {
            const startDate = dateFromInput.value ? new Date(dateFromInput.value) : null;
            const endDate = dateToInput.value ? new Date(dateToInput.value) : null;

            if (startDate && endDate && startDate > endDate) {
                Swal.fire({
                    title: 'Lỗi ngày tháng',
                    text: 'Ngày bắt đầu không thể sau ngày kết thúc',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            return true;
        }

        // Helper functions
        function getStatusClass(status) {
            switch (status) {
                case 'pending':
                    return 'bg-warning text-dark';
                case 'confirmed':
                    return 'bg-info text-white';
                case 'delivered_success':
                    return 'bg-success text-white';
                case 'canceled':
                    return 'bg-danger text-white';
                default:
                    return 'bg-secondary text-white';
            }
        }

        function getStatusLabel(status) {
            switch (status) {
                case 'pending':
                    return 'Chờ xác nhận';
                case 'confirmed':
                    return 'Đã xác nhận';
                case 'delivered_success':
                    return 'Đã giao hàng';
                case 'canceled':
                    return 'Đã hủy';
                default:
                    return 'Không xác định';
            }
        }

        // Function to load orders data
        function loadOrdersData() {
            // Destroy existing DataTable if it exists
            if (ordersTable !== null) {
                ordersTable.destroy();
                ordersTable = null;
            } else if ($.fn.DataTable.isDataTable('#ordersTable')) {
                $('#ordersTable').DataTable().destroy();
            }

            // Show loading indicator, hide data container
            loadingIndicator.classList.remove('d-none');
            dataContainer.classList.add('d-none');

            // Prepare query parameters
            const params = new URLSearchParams();
            if (orderStatusSelect.value) {
                params.append('status', orderStatusSelect.value);
            }
            if (paymentMethodSelect.value) {
                params.append('payment_method', paymentMethodSelect.value);
            }
            if (dateFromInput.value) {
                params.append('start_date', dateFromInput.value);
            }
            if (dateToInput.value) {
                params.append('end_date', dateToInput.value);
            }

            // Return a promise that resolves when data is loaded
            return new Promise((resolve, reject) => {
                // Fetch data
                fetch(`/api/orders/filtered?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        // Hide loading indicator
                        loadingIndicator.classList.add('d-none');

                        if (data.success) {
                            // Render orders table
                            renderOrdersTable(data.data.orders);

                            // Show data container
                            dataContainer.classList.remove('d-none');

                            // Initialize DataTable
                            initDataTable();

                            // Resolve the promise
                            resolve();
                        } else {
                            console.error('Error loading orders data');
                            reject('Error loading orders data');
                        }
                    })
                    .catch(error => {
                        // Hide loading indicator and log error
                        loadingIndicator.classList.add('d-none');
                        console.error('Error fetching data:', error);
                        reject(error);
                    });
            });
        }

        // Function to render orders table
        function renderOrdersTable(orders) {
            const tableBody = document.getElementById('orders-table-body');

            // Clear existing rows
            tableBody.innerHTML = '';

            if (orders && orders.length > 0) {
                // Add new rows
                orders.forEach(order => {
                    const row = document.createElement('tr');

                    // Order ID
                    let cell = document.createElement('td');
                    cell.textContent = order.OrderID;
                    row.appendChild(cell);

                    // Customer info
                    cell = document.createElement('td');
                    cell.innerHTML = `
                        <div>${order.Name}</div>
                        <div class="small text-muted">${order.Phone}</div>
                        <div class="small text-muted">${order.Email}</div>
                    `;
                    row.appendChild(cell);

                    // Order date
                    const orderDate = new Date(order.OrderDate);
                    cell = document.createElement('td');
                    cell.setAttribute('data-order', Math.floor(orderDate.getTime() / 1000));
                    cell.innerHTML = `
                        <div>${orderDate.toLocaleDateString('vi-VN')}</div>
                        <div class="small text-muted">${orderDate.toLocaleTimeString('vi-VN')}</div>
                    `;
                    row.appendChild(cell);

                    // Total amount
                    cell = document.createElement('td');
                    cell.textContent = new Intl.NumberFormat('vi-VN').format(order.TotalAmount) + ' ₫';
                    row.appendChild(cell);

                    // Shipping address
                    cell = document.createElement('td');
                    cell.innerHTML = `
                        <div>
                            ${order.Address}, ${order.Ward}, ${order.District}, ${order.City}
                        </div>
                    `;
                    row.appendChild(cell);

                    // Payment method
                    cell = document.createElement('td');
                    cell.innerHTML = `<div>${order.PaymentMethod}</div>`;
                    row.appendChild(cell);

                    // Status
                    cell = document.createElement('td');
                    const statusClass = getStatusClass(order.Status);
                    const statusText = getStatusLabel(order.Status);
                    cell.innerHTML = `<span class="badge ${statusClass}">${statusText}</span>`;
                    row.appendChild(cell);

                    // Actions
                    cell = document.createElement('td');
                    let actions = `
                        <div class="d-flex">
                            <a href="/admin/orders/order-detail?id=${order.OrderID}" class="btn btn-sm btn-primary me-2 fw-bold" 
                               style="background-color: #4e73df; border-color: #4e73df;">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                    `;

                    if (order.Status === 'pending') {
                        actions += `
                            <button type="button" class="btn btn-sm btn-success me-2 fw-bold update-status"
                                data-id="${order.OrderID}" data-status="confirmed"
                                style="background-color: #28a745;">
                                <i class="fas fa-check"></i> Xác nhận
                            </button>
                            <button type="button" class="btn btn-sm btn-danger fw-bold update-status"
                                data-id="${order.OrderID}" data-status="canceled">
                                <i class="fas fa-times"></i> Hủy đơn
                            </button>
                        `;
                    } else if (order.Status === 'confirmed') {
                        actions += `
                            <button type="button" class="btn btn-sm btn-success me-2 fw-bold update-status"
                                data-id="${order.OrderID}" data-status="delivered_success"
                                style="background-color: #28a745;">
                                <i class="fa-solid fa-truck-fast"></i> Giao hàng
                            </button>
                            <button type="button" class="btn btn-sm btn-danger fw-bold update-status"
                                data-id="${order.OrderID}" data-status="canceled">
                                <i class="fas fa-times"></i> Hủy đơn
                            </button>
                        `;
                    }

                    actions += `</div>`;
                    cell.innerHTML = actions;
                    row.appendChild(cell);

                    tableBody.appendChild(row);
                });

                // Attach click event listeners to update status buttons
                document.querySelectorAll('.update-status').forEach(button => {
                    button.addEventListener('click', function () {
                        const orderId = this.getAttribute('data-id');
                        const newStatus = this.getAttribute('data-status');

                        let title, message, btnClass;

                        // Set confirmation message based on status
                        if (newStatus === 'confirmed') {
                            title = `Xác nhận đơn hàng #${orderId}`;
                            message = 'Bạn có chắc chắn muốn xác nhận đơn hàng này?';
                            btnClass = 'btn-primary';
                        } else if (newStatus === 'delivered_success') {
                            title = `Xác nhận giao hàng #${orderId}`;
                            message = 'Bạn có chắc chắn muốn đánh dấu đơn hàng này đã giao thành công?';
                            btnClass = 'btn-primary';
                        } else if (newStatus === 'canceled') {
                            title = `Xác nhận hủy đơn hàng #${orderId}`;
                            message = 'Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.';
                            btnClass = 'btn-danger';
                        } else {
                            title = `Cập nhật trạng thái đơn hàng #${orderId}`;
                            message = `Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng thành ${getStatusLabel(newStatus)}?`;
                            btnClass = 'btn-primary';
                        }

                        // Show confirmation dialog
                        showConfirmation(title, message, function () {
                            // Call the API to update order status
                            updateOrderStatus(orderId, newStatus);
                        }, btnClass);
                    });
                });
            } else {
                // No data
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 8;
                cell.className = 'text-center py-3';
                cell.textContent = 'Không tìm thấy đơn hàng nào';
                row.appendChild(cell);
                tableBody.appendChild(row);
            }
        }

        // Function to update order status
        function updateOrderStatus(orderId, newStatus) {
            // Show loading alert
            const loadingSwal = Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng chờ trong giây lát',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/api/orders/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    orderId: orderId,
                    status: newStatus
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close loading indicator
                        loadingSwal.close();

                        // Instead of updating just the row, reload all the data
                        loadOrdersData().then(() => {
                            // Show success message after data is loaded
                            Swal.fire({
                                title: 'Cập nhật thành công',
                                text: data.message || `Đã cập nhật trạng thái đơn hàng #${orderId}`,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK'
                            });
                        });
                    } else {
                        loadingSwal.close();
                        Swal.fire({
                            title: 'Lỗi',
                            text: data.message || 'Không thể cập nhật trạng thái đơn hàng',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    loadingSwal.close();
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi xử lý yêu cầu',
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                });
        }

        // Function to update a single order row's status
        function updateOrderRowStatus(orderId, newStatus) {
            // Find the row in the table that contains the order ID
            const rows = document.querySelectorAll('#orders-table-body tr');
            let targetRow = null;

            for (const row of rows) {
                const firstCell = row.querySelector('td:first-child');
                if (firstCell && firstCell.textContent.trim() === orderId.toString()) {
                    targetRow = row;
                    break;
                }
            }

            if (!targetRow) return;

            // Update status badge
            const statusCell = targetRow.querySelector('td:nth-child(7)');
            if (statusCell) {
                const statusClass = getStatusClass(newStatus);
                const statusText = getStatusLabel(newStatus);
                statusCell.innerHTML = `<span class="badge ${statusClass}">${statusText}</span>`;
            }

            // Update action buttons
            const actionCell = targetRow.querySelector('td:nth-child(8)');
            if (actionCell) {
                const viewBtn = actionCell.querySelector('a.btn-primary');
                let actions = '';

                if (viewBtn) {
                    actions = `
                        <div class="d-flex">
                            ${viewBtn.outerHTML}
                    `;
                } else {
                    actions = `
                        <div class="d-flex">
                            <a href="/admin/orders/order-detail?id=${orderId}" class="btn btn-sm btn-primary me-2 fw-bold" 
                               style="background-color: #4e73df; border-color: #4e73df;">
                                <i class="fas fa-eye"></i> Chi tiết
                            </a>
                    `;
                }

                if (newStatus === 'pending') {
                    actions += `
                        <button type="button" class="btn btn-sm btn-success me-2 fw-bold update-status"
                            data-id="${orderId}" data-status="confirmed"
                            style="background-color: #28a745;">
                            <i class="fas fa-check"></i> Xác nhận
                        </button>
                        <button type="button" class="btn btn-sm btn-danger fw-bold update-status"
                            data-id="${orderId}" data-status="canceled">
                            <i class="fas fa-times"></i> Hủy đơn
                        </button>
                    `;
                } else if (newStatus === 'confirmed') {
                    actions += `
                        <button type="button" class="btn btn-sm btn-success me-2 fw-bold update-status"
                            data-id="${orderId}" data-status="delivered_success"
                            style="background-color: #28a745;">
                            <i class="fa-solid fa-truck-fast"></i> Giao hàng
                        </button>
                        <button type="button" class="btn btn-sm btn-danger fw-bold update-status"
                            data-id="${orderId}" data-status="canceled">
                            <i class="fas fa-times"></i> Hủy đơn
                        </button>
                    `;
                }

                actions += `</div>`;
                actionCell.innerHTML = actions;

                // Re-attach event listeners to the new buttons
                actionCell.querySelectorAll('.update-status').forEach(button => {
                    button.addEventListener('click', function () {
                        const orderId = this.getAttribute('data-id');
                        const newStatus = this.getAttribute('data-status');

                        let title, message, btnClass;

                        // Set confirmation message based on status
                        if (newStatus === 'confirmed') {
                            title = `Xác nhận đơn hàng #${orderId}`;
                            message = 'Bạn có chắc chắn muốn xác nhận đơn hàng này?';
                            btnClass = 'btn-primary';
                        } else if (newStatus === 'delivered_success') {
                            title = `Xác nhận giao hàng #${orderId}`;
                            message = 'Bạn có chắc chắn muốn đánh dấu đơn hàng này đã giao thành công?';
                            btnClass = 'btn-primary';
                        } else if (newStatus === 'canceled') {
                            title = `Xác nhận hủy đơn hàng #${orderId}`;
                            message = 'Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.';
                            btnClass = 'btn-danger';
                        } else {
                            title = `Cập nhật trạng thái đơn hàng #${orderId}`;
                            message = `Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng thành ${getStatusLabel(newStatus)}?`;
                            btnClass = 'btn-primary';
                        }

                        showConfirmation(title, message, function () {
                            updateOrderStatus(orderId, newStatus);
                        }, btnClass);
                    });
                });
            }

            // If DataTable is active, redraw the row
            if (ordersTable) {
                const rowIndex = ordersTable.row(targetRow).index();
                if (rowIndex !== undefined) {
                    ordersTable.row(rowIndex).invalidate().draw(false);
                }
            }
        }

        // Function to initialize DataTable
        function initDataTable() {
            if (typeof $.fn.DataTable !== 'undefined') {
                // Check if table is empty before initializing DataTable
                const tableBody = document.getElementById('orders-table-body');
                if (!tableBody.querySelector('tr') || tableBody.querySelector('tr td[colspan="8"]')) {
                    // If there's no data or just an empty row message, don't initialize DataTable
                    return;
                }

                ordersTable = $('#ordersTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [
                        [10, 20],
                        [10, 20]
                    ],
                    order: [
                        [2, 'desc']
                    ], // Order by date column descending
                    language: {
                        search: "Tìm kiếm: _INPUT_",
                        searchPlaceholder: "Mã đơn, Tên, SĐT, Địa chỉ,...",
                        lengthMenu: "<span class='me-2'>Hiển thị</span> _MENU_ <span class='ms-2'>đơn hàng</span>",
                        info: "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ đơn hàng",
                        infoEmpty: "Hiển thị 0 đến 0 trong tổng số 0 đơn hàng",
                        infoFiltered: "",
                        zeroRecords: "Không tìm thấy đơn hàng nào",
                        paginate: {
                            "first": "Đầu",
                            "last": "Cuối",
                            "next": "<i class='fas fa-chevron-right'></i>",
                            "previous": "<i class='fas fa-chevron-left'></i>"
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [3, 7]
                    },
                    {
                        targets: [2, 5, 6, 7], // Chỉ định các cột ko thể search(bắt đầu từ 0)
                        searchable: false
                    }],
                    drawCallback: function () {
                        // Apply styling to pagination controls
                        const api = this.api();
                        const pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');

                        if (api.page.info().pages <= 1) {
                            pagination.hide();
                        } else {
                            pagination.show();
                        }

                        pagination.addClass('pagination-sm');
                    }
                });

                // Custom styling for DataTable elements
                $('.dataTables_filter input')
                    .addClass('form-control form-control-sm')
                    .css('min-width', '250px')
                    .css('margin-left', '10px');

                $('.dataTables_length select')
                    .addClass('form-select form-select-sm')
                    .css('width', '65px')
                    .css('margin', '0 5px');

                // Fix layout spacing
                $('.dataTables_wrapper .row').addClass('align-items-center');
            }
        }
    });
</script>