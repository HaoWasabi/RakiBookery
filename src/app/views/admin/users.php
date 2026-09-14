<!-- Users Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý người dùng</h1>
        <a href="/admin/users/add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Thêm người dùng mới
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Tìm kiếm & Lọc</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Vai trò</label>
                    <select class="form-select" id="role" name="Role">
                        <option value="">Tất cả</option>
                        <option value="admin" <?= isset($_GET['Role']) && $_GET['Role'] == 'admin' ? 'selected' : '' ?>>
                            Admin</option>
                        <option value="user" <?= isset($_GET['Role']) && $_GET['Role'] == 'user' ? 'selected' : '' ?>>
                            Khách hàng</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="Status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?= isset($_GET['Status']) && $_GET['Status'] == '1' ? 'selected' : '' ?>>
                            Hoạt động</option>
                        <option value="0" <?= isset($_GET['Status']) && $_GET['Status'] == '0' ? 'selected' : '' ?>>Bị
                            khóa</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <button type="button" id="reset-btn" class="btn btn-secondary me-2">
                    <i class="fas fa-redo mr-2"></i> Đặt lại
                </button>
                <button type="button" id="search-btn" class="btn btn-primary">
                    <i class="fas fa-search mr-2"></i> Tìm kiếm
                </button>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading-indicator" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2">Đang tải dữ liệu người dùng...</p>
    </div>

    <div class="card mb-4 d-none" id="data-container">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="usersTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Tên</th>
                            <th width="20%">Email</th>
                            <th width="15%">Số điện thoại</th>
                            <th width="10%">Vai trò</th>
                            <th width="10%">Trạng thái</th>
                            <th width="20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body">
                        <?php foreach ($users as $user): ?>
                            <tr data-id="<?= $user['UserID'] ?>"
                                data-name="<?= htmlspecialchars($user['Name'], ENT_QUOTES) ?>"
                                data-status="<?= $user['Status'] ?>">
                                <td><?= $user['UserID'] ?></td>
                                <td><?= $user['Name'] ?></td>
                                <td><?= $user['Email'] ?></td>
                                <td><?= $user['Phone'] ?? 'Chưa cập nhật' ?></td>
                                <td>
                                    <?php if ($user['Role'] == 'admin'): ?>
                                        <span class="badge bg-primary">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Khách hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['Status'] == 1): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Bị khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/admin/users/user-info?id=<?= $user['UserID'] ?>"
                                        class="btn btn-sm btn-primary fw-bold">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm fw-bold <?= $user['Status'] == 1 ? 'btn-warning' : 'btn-success' ?> ms-1 toggle-status-btn"
                                        <?= ($user['Role'] == 'admin' && $adminCount <= 1) ? 'disabled' : '' ?>
                                        title="<?= ($user['Role'] == 'admin' && $adminCount <= 1) ? 'Không thể khóa tài khoản admin duy nhất' : '' ?>">
                                        <i class="fas fa-<?= $user['Status'] == 1 ? 'lock' : 'unlock' ?>"></i>
                                        <?= $user['Status'] == 1 ? 'Khóa' : 'Mở khóa' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const roleSelect = document.getElementById('role');
        const statusSelect = document.getElementById('status');
        const searchBtn = document.getElementById('search-btn');
        const resetBtn = document.getElementById('reset-btn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const dataContainer = document.getElementById('data-container');

        let usersTable = null;
        // Initial data load - immediate, no delay
        loadUsersData();

        // Search button click event
        searchBtn.addEventListener('click', function () {
            loadUsersData();
        });

        // Reset button click event
        resetBtn.addEventListener('click', function () {
            roleSelect.value = '';
            statusSelect.value = '';
            loadUsersData();
        });

        // Function to load users data
        function loadUsersData() {
            // Destroy existing DataTable if it exists
            if (usersTable !== null) {
                usersTable.destroy();
                usersTable = null;
            } else if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().destroy();
            }

            // Show loading indicator, hide data container
            loadingIndicator.classList.remove('d-none');
            dataContainer.classList.add('d-none');

            // Prepare query parameters
            const params = new URLSearchParams();
            if (roleSelect.value) {
                params.append('Role', roleSelect.value);
            }
            if (statusSelect.value) {
                params.append('Status', statusSelect.value);
            }

            // Return a promise that resolves when data is loaded
            return new Promise((resolve, reject) => {
                // Fetch user data from server
                fetch(`/admin/users?${params.toString()}`)
                    .then(response => response.text())
                    .then(html => {
                        // Create a temporary element to parse the HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        // Extract user data from the HTML
                        const usersData = extractUsersFromHTML(tempDiv);
                        const adminCount = extractAdminCountFromHTML(tempDiv);

                        // Hide loading indicator
                        loadingIndicator.classList.add('d-none');
                        dataContainer.classList.remove('d-none');

                        // Render users table
                        renderUsersTable(usersData, adminCount);

                        // Initialize DataTable
                        initDataTable();

                        // Resolve the promise
                        resolve();
                    })
                    .catch(error => {
                        console.error('Error loading users data:', error);
                        loadingIndicator.classList.add('d-none');
                        dataContainer.classList.remove('d-none');
                        showError('Lỗi', 'Đã xảy ra lỗi khi tải dữ liệu người dùng');
                        // Reject the promise
                        reject(error);
                    });
            });
        }

        // Extract users data from HTML
        function extractUsersFromHTML(htmlContainer) {
            const users = [];
            const rows = htmlContainer.querySelectorAll('#users-table-body tr');

            rows.forEach(row => {
                const user = {
                    UserID: row.getAttribute('data-id'),
                    Name: row.getAttribute('data-name'),
                    Status: row.getAttribute('data-status'),
                    Email: row.cells[2].textContent.trim(),
                    Phone: row.cells[3].textContent.trim(),
                    Role: row.cells[4].querySelector('.badge').textContent.trim() === 'Admin' ? 'admin' : 'user'
                };
                users.push(user);
            });

            return users;
        }

        // Extract admin count from HTML
        function extractAdminCountFromHTML(htmlContainer) {
            // This is a simplified version - in a real implementation,
            // you might want to send this data in a more structured way
            // For now, we assume the PHP variable is available in the scope
            return <?= $adminCount ?? 0 ?>;
        }

        // Render users table
        function renderUsersTable(users, adminCount) {
            const tableBody = document.getElementById('users-table-body');

            // Clear existing rows
            tableBody.innerHTML = '';

            if (users && users.length > 0) {
                // Add new rows
                users.forEach(user => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', user.UserID);
                    row.setAttribute('data-name', user.Name);
                    row.setAttribute('data-status', user.Status);

                    // ID column
                    let cell = document.createElement('td');
                    cell.textContent = user.UserID;
                    row.appendChild(cell);

                    // Name column
                    cell = document.createElement('td');
                    cell.textContent = user.Name;
                    row.appendChild(cell);

                    // Email column
                    cell = document.createElement('td');
                    cell.textContent = user.Email;
                    row.appendChild(cell);

                    // Phone column
                    cell = document.createElement('td');
                    cell.textContent = user.Phone || 'Chưa cập nhật';
                    row.appendChild(cell);

                    // Role column
                    cell = document.createElement('td');
                    if (user.Role === 'admin') {
                        cell.innerHTML = '<span class="badge bg-primary">Admin</span>';
                    } else {
                        cell.innerHTML = '<span class="badge bg-secondary">Khách hàng</span>';
                    }
                    row.appendChild(cell);

                    // Status column
                    cell = document.createElement('td');
                    if (user.Status == 1) {
                        cell.innerHTML = '<span class="badge bg-success">Hoạt động</span>';
                    } else {
                        cell.innerHTML = '<span class="badge bg-danger">Bị khóa</span>';
                    }
                    row.appendChild(cell);

                    // Actions column
                    cell = document.createElement('td');
                    const isLastAdmin = (user.Role === 'admin' && adminCount <= 1);
                    const statusBtnClass = user.Status == 1 ? 'btn-warning' : 'btn-success';
                    const statusBtnIcon = user.Status == 1 ? 'lock' : 'unlock';
                    const statusBtnText = user.Status == 1 ? 'Khóa' : 'Mở khóa';

                    let actions = `
                        <a href="/admin/users/user-info?id=${user.UserID}" class="btn btn-sm btn-primary fw-bold">
                            <i class="fas fa-eye"></i> Xem
                        </a>
                        <button type="button"
                            class="btn btn-sm fw-bold ${statusBtnClass} ms-1 toggle-status-btn"
                            ${isLastAdmin ? 'disabled' : ''}
                            title="${isLastAdmin ? 'Không thể khóa tài khoản admin duy nhất' : ''}">
                            <i class="fas fa-${statusBtnIcon}"></i> ${statusBtnText}
                        </button>
                    `;

                    cell.innerHTML = actions;
                    row.appendChild(cell);

                    tableBody.appendChild(row);
                });
            } else {
                // No data
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 7;
                cell.className = 'text-center py-3';
                cell.textContent = 'Không tìm thấy người dùng nào';
                row.appendChild(cell);
                tableBody.appendChild(row);
            }
        }

        // Initialize DataTable
        function initDataTable() {
            if (typeof $.fn.DataTable !== 'undefined') {
                // Check if table is empty before initializing DataTable
                const tableBody = document.getElementById('users-table-body');
                if (!tableBody.querySelector('tr') || tableBody.querySelector('tr td[colspan="7"]')) {
                    // If there's no data or just an empty row message, don't initialize DataTable
                    return;
                }

                // If DataTable already exists on the table, destroy it
                if ($.fn.DataTable.isDataTable('#usersTable')) {
                    $('#usersTable').DataTable().destroy();
                }

                usersTable = $('#usersTable').DataTable({
                    dom: '<"row align-items-center mb-3"<"col-md-6"<"d-flex align-items-center"l>><"col-md-6"<"d-flex justify-content-end"f>>>rt<"row mt-3"<"col-md-6"i><"col-md-6"<"d-flex justify-content-end"p>>>',
                    language: {
                        "search": "Tìm kiếm:",
                        "searchPlaceholder": "Tên, Email, Số điện thoại,...",
                        "lengthMenu": "<span class='me-2'>Hiển thị</span> _MENU_ <span class='ms-2'>người dùng</span>",
                        "info": "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ người dùng",
                        "infoEmpty": "Hiển thị 0 đến 0 trong tổng số 0 người dùng",
                        "infoFiltered": "",
                        "zeroRecords": "Không tìm thấy người dùng nào",
                        "paginate": {
                            "first": "Đầu",
                            "last": "Cuối",
                            "next": "<i class='fas fa-chevron-right'></i>",
                            "previous": "<i class='fas fa-chevron-left'></i>"
                        }
                    },
                    pageLength: 10,
                    lengthMenu: [
                        [10, 20],
                        [10, 20]
                    ],
                    order: [
                        [0, 'desc']
                    ], // Sort by ID descending
                    responsive: true,
                    processing: true,
                    paging: true,
                    searching: true,
                    info: true,
                    columnDefs: [{
                        orderable: false,
                        targets: [6]
                    }, // Disable sorting for actions column
                    {
                        searchable: true,
                        targets: [1, 2, 3]
                    }, // Allow search on name, email, phone
                    {
                        searchable: false,
                        targets: [0, 4, 5, 6]
                    } // Disable search on other columns
                    ],
                    drawCallback: function () {
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

        // Toggle status button click
        $(document).on('click', '.toggle-status-btn', function () {
            const row = $(this).closest('tr');
            const userId = row.data('id');
            const userName = row.data('name');
            const currentStatus = row.data('status');

            confirmToggleStatus(userId, userName, currentStatus);
        });

        function confirmToggleStatus(userId, userName, currentStatus) {
            const action = currentStatus == 1 ? 'khóa' : 'mở khóa';
            const confirmMessage = `Bạn có chắc chắn muốn ${action} tài khoản của người dùng "${userName}"?`;

            showConfirmation(
                `Xác nhận ${action} tài khoản`,
                confirmMessage,
                function () {
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

                    fetch('/api/users/toggle-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            UserID: userId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Close loading indicator
                                loadingSwal.close();

                                // Instead of updating just the row, reload all the data
                                loadUsersData().then(() => {
                                    // Show success message after data is loaded
                                    Swal.fire({
                                        title: 'Thành công',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#3085d6'
                                    });
                                });
                            } else {
                                loadingSwal.close();
                                showError('Lỗi', data.message);
                            }
                        })
                        .catch(error => {
                            loadingSwal.close();
                            showError('Lỗi', 'Đã xảy ra lỗi, vui lòng thử lại sau.');
                            console.error('Error:', error);
                        });
                }
            );
        }

        // Function to update a single user row's status
        function updateUserRowStatus(userId, newStatus) {
            // Find the row in the table
            const row = document.querySelector(`tr[data-id="${userId}"]`);
            if (!row) return;

            // Update the row's data attribute
            row.setAttribute('data-status', newStatus);

            // Update status badge
            const statusCell = row.querySelector('td:nth-child(6)');
            if (statusCell) {
                if (newStatus == 1) {
                    statusCell.innerHTML = '<span class="badge bg-success">Hoạt động</span>';
                } else {
                    statusCell.innerHTML = '<span class="badge bg-danger">Bị khóa</span>';
                }
            }

            // Update toggle button
            const actionCell = row.querySelector('td:nth-child(7)');
            if (actionCell) {
                const toggleBtn = actionCell.querySelector('.toggle-status-btn');
                if (toggleBtn) {
                    toggleBtn.classList.remove('btn-warning', 'btn-success');
                    toggleBtn.classList.add(newStatus == 1 ? 'btn-warning' : 'btn-success');

                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        icon.className = newStatus == 1 ? 'fas fa-lock' : 'fas fa-unlock';
                    }

                    // Update button text
                    toggleBtn.innerHTML = toggleBtn.innerHTML.replace(
                        newStatus == 1 ? 'Mở khóa' : 'Khóa',
                        newStatus == 1 ? 'Khóa' : 'Mở khóa'
                    );
                }
            }

            // If DataTable is active, redraw the row
            if (usersTable) {
                usersTable.row(row).invalidate().draw(false);
            }
        }
    });
</script>