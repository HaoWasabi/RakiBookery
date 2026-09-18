<!-- Promos Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý khuyến mãi</h1>
        <a href="/admin/promos/add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Thêm khuyến mãi mới
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Tìm kiếm & Lọc</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="filter-status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="filter-status">
                        <option value="">Tất cả</option>
                        <option value="1">Hiển thị</option>
                        <option value="0">Ẩn</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <button type="button" id="reset-btn" class="btn btn-secondary me-2">
                    <i class="fas fa-redo mr-2"></i> Đặt lại
                </button>
                <button type="button" id="search-btn" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i> Tìm kiếm
                </button>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading-indicator" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2">Đang tải dữ liệu khuyến mãi...</p>
    </div>

    <!-- Data Table -->
    <div class="card mb-4 d-none" id="data-container">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="promosTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="30%">Tên mã khuyến mãi</th>
                            <th width="15%">Giảm giá (%)</th>
                            <th width="18%">Ngày tạo</th>
                            <th width="12%">Trạng thái</th>
                            <th width="20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="promos-table-body">
                        <!-- Populated by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterStatus = document.getElementById('filter-status');
        const searchBtn    = document.getElementById('search-btn');
        const resetBtn     = document.getElementById('reset-btn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const dataContainer    = document.getElementById('data-container');

        let promosTable = null;

        // Load on page ready
        loadPromosData();

        searchBtn.addEventListener('click', loadPromosData);

        resetBtn.addEventListener('click', function () {
            filterStatus.value = '';
            loadPromosData();
        });

        function loadPromosData() {
            // Destroy existing DataTable
            if (promosTable !== null) {
                promosTable.destroy();
                promosTable = null;
            } else if ($.fn.DataTable.isDataTable('#promosTable')) {
                $('#promosTable').DataTable().destroy();
            }

            loadingIndicator.classList.remove('d-none');
            dataContainer.classList.add('d-none');

            // Build params — reuse existing getAll filtered by status client-side
            // Server trả về tất cả, filter theo status ở client qua DataTable
            return new Promise((resolve, reject) => {
                fetch('/admin/promos/data')
                    .then(r => r.json())
                    .then(data => {
                        loadingIndicator.classList.add('d-none');

                        if (data.success) {
                            let promos = data.promos;

                            // Client-side status filter
                            const statusVal = filterStatus.value;
                            if (statusVal !== '') {
                                promos = promos.filter(p => String(p.Status) === statusVal);
                            }

                            renderPromosTable(promos);
                            dataContainer.classList.remove('d-none');
                            initDataTable();
                            resolve();
                        } else {
                            loadingIndicator.classList.add('d-none');
                            reject('Error loading promos');
                        }
                    })
                    .catch(error => {
                        loadingIndicator.classList.add('d-none');
                        console.error('Error:', error);
                        reject(error);
                    });
            });
        }

        function renderPromosTable(promos) {
            const tableBody = document.getElementById('promos-table-body');
            tableBody.innerHTML = '';

            if (promos && promos.length > 0) {
                promos.forEach(promo => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', promo.PromoID);
                    row.setAttribute('data-name', promo.Name);
                    row.setAttribute('data-status', promo.Status);

                    const statusBadge = promo.Status == 1
                        ? '<span class="badge bg-success">Hiển thị</span>'
                        : '<span class="badge bg-danger">Ẩn</span>';

                    const actionBtn = promo.Status == 1
                        ? `<button type="button" class="btn btn-sm btn-danger fw-bold ms-1 delete-btn">
                               <i class="fas fa-eye-slash"></i> Ẩn
                           </button>`
                        : `<button type="button" class="btn btn-sm btn-success fw-bold ms-1 restore-btn">
                               <i class="fas fa-eye"></i> Hiện
                           </button>`;

                    row.innerHTML = `
                        <td>${promo.PromoID}</td>
                        <td class="fw-bold">${promo.Name}</td>
                        <td>${promo.Discounted}%</td>
                        <td>${promo.DateCreated ?? ''}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <a href="/admin/promos/promo?id=${promo.PromoID}"
                               class="btn btn-sm fw-bold btn-primary">
                                <i class="fas fa-eye"></i> Xem
                            </a>
                            ${actionBtn}
                        </td>
                    `;

                    tableBody.appendChild(row);
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="6" class="text-center py-3">Không tìm thấy khuyến mãi nào</td>';
                tableBody.appendChild(row);
            }
        }

        function initDataTable() {
            const tableBody = document.getElementById('promos-table-body');
            if (!tableBody.querySelector('tr') ||
                tableBody.querySelector('tr td[colspan="6"]')) return;

            if ($.fn.DataTable.isDataTable('#promosTable')) {
                $('#promosTable').DataTable().destroy();
            }

            promosTable = $('#promosTable').DataTable({
                dom: '<"row align-items-center mb-3"<"col-md-6"<"d-flex align-items-center"l>><"col-md-6"<"d-flex justify-content-end"f>>>rt<"row mt-3"<"col-md-6"i><"col-md-6"<"d-flex justify-content-end"p>>>',
                language: {
                    search: 'Tìm kiếm:',
                    searchPlaceholder: 'Tên mã khuyến mãi...',
                    lengthMenu: "<span class='me-2'>Hiển thị</span> _MENU_ <span class='ms-2'>mục</span>",
                    info: 'Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ khuyến mãi',
                    infoEmpty: 'Hiển thị 0 đến 0 trong tổng số 0 khuyến mãi',
                    infoFiltered: '',
                    zeroRecords: 'Không tìm thấy khuyến mãi nào',
                    paginate: {
                        first: 'Đầu', last: 'Cuối',
                        next: "<i class='fas fa-chevron-right'></i>",
                        previous: "<i class='fas fa-chevron-left'></i>"
                    }
                },
                pageLength: 10,
                lengthMenu: [[10, 20, 50], [10, 20, 50]],
                order: [[0, 'desc']],
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [5] },
                    { searchable: true,  targets: [1] },
                    { searchable: false, targets: [0, 2, 3, 4, 5] }
                ],
                drawCallback: function () {
                    const pagination = $(this).closest('.dataTables_wrapper').find('.dataTables_paginate');
                    this.api().page.info().pages <= 1 ? pagination.hide() : pagination.show();
                    pagination.addClass('pagination-sm');
                }
            });

            $('.dataTables_filter input')
                .addClass('form-control form-control-sm')
                .css('min-width', '200px').css('margin-left', '10px');
            $('.dataTables_length select')
                .addClass('form-select form-select-sm')
                .css('width', '65px').css('margin', '0 5px');
        }

        // Delete (soft) handler
        $(document).on('click', '.delete-btn', function () {
            const row = $(this).closest('tr');
            const id   = row.data('id');
            const name = row.data('name');

            showConfirmation(
                'Xác nhận ẩn khuyến mãi',
                `Khuyến mãi "<strong>${name}</strong>" sẽ bị ẩn. Các đơn hàng cũ vẫn giữ nguyên thông tin.`,
                function () {
                    const loading = Swal.fire({
                        title: 'Đang xử lý...', allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('/admin/promos/delete', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    })
                    .then(r => r.json())
                    .then(data => {
                        loading.close();
                        if (data.success) {
                            loadPromosData().then(() => {
                                Swal.fire({ title: 'Thành công', text: data.message,
                                    icon: 'success', confirmButtonText: 'OK', confirmButtonColor: '#3085d6' });
                            });
                        } else {
                            showError('Lỗi', data.message);
                        }
                    })
                    .catch(() => { loading.close(); showError('Lỗi', 'Đã xảy ra lỗi, vui lòng thử lại.'); });
                }
            );
        });

        // Restore handler
        $(document).on('click', '.restore-btn', function () {
            const row = $(this).closest('tr');
            const id   = row.data('id');
            const name = row.data('name');

            showConfirmation(
                'Xác nhận hiển thị lại',
                `Bạn có chắc chắn muốn hiển thị lại khuyến mãi "<strong>${name}</strong>"?`,
                function () {
                    const loading = Swal.fire({
                        title: 'Đang xử lý...', allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('/admin/promos/restore', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    })
                    .then(r => r.json())
                    .then(data => {
                        loading.close();
                        if (data.success) {
                            loadPromosData().then(() => {
                                Swal.fire({ title: 'Thành công', text: data.message,
                                    icon: 'success', confirmButtonText: 'OK', confirmButtonColor: '#3085d6' });
                            });
                        } else {
                            showError('Lỗi', data.message);
                        }
                    })
                    .catch(() => { loading.close(); showError('Lỗi', 'Đã xảy ra lỗi, vui lòng thử lại.'); });
                }
            );
        });
    });
</script>
