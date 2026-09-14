<!-- Products Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý sản phẩm</h1>
        <a href="/admin/products/add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Thêm sản phẩm mới
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold">Tìm kiếm & Lọc</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Thể loại</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Tất cả</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['CategoryID'] ?>" <?= isset($_GET['category']) && $_GET['category'] == $cat['CategoryID'] ? 'selected' : '' ?>>
                                <?= $cat['Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="Status">
                        <option value="">Tất cả</option>
                        <option value="1" <?= isset($_GET['status']) && $_GET['status'] == '1' ? 'selected' : '' ?>>
                            Hiển thị</option>
                        <option value="0" <?= isset($_GET['status']) && $_GET['status'] == '0' ? 'selected' : '' ?>>Ẩn
                        </option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <button type="button" id="reset-btn" class="btn btn-secondary me-2">
                    <i class="fas fa-redo mr-2"></i> Đặt lại
                </button>
                <button type="button" id="search-btn" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Tìm kiếm
                </button>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="loading-indicator" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2">Đang tải dữ liệu sản phẩm...</p>
    </div>

    <div class="card mb-4 d-none" id="data-container">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="productsTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="8%">Hình ảnh</th>
                            <th width="12%">Tên sách</th>
                            <th width="12%">Tác giả</th>
                            <th width="10%">Nhà xuất bản</th>
                            <th width="10%">Ngày phát hành</th>
                            <th width="10%">Thể loại</th>
                            <th width="8%">Giá</th>
                            <th width="5%">Trạng thái</th>
                            <th width="20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="products-table-body">
                        <?php if (isset($books) && !empty($books)): ?>
                            <?php foreach ($books as $book): ?>
                                <tr data-id="<?= $book['BookID'] ?>"
                                    data-name="<?= htmlspecialchars($book['Name'], ENT_QUOTES) ?>"
                                    data-status="<?= $book['Status'] ?>">
                                    <td><?= $book['BookID'] ?></td>
                                    <td>
                                        <?php if (isset($book['ImageURL']) && $book['ImageURL']): ?>
                                            <img src="<?= $book['ImageURL'] ?>" alt="<?= $book['Name'] ?>" class="img-thumbnail"
                                                style="height: 80px; width: auto; object-fit: contain;">
                                        <?php else: ?>
                                            <div class="text-center text-muted">
                                                <i class="fas fa-image fa-3x"></i>
                                                <p class="small">Không có ảnh</p>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?= $book['Name'] ?></td>
                                    <td><?= $book['Author'] ?></td>
                                    <td><?= $book['Publisher'] ?? 'N/A' ?></td>
                                    <td><?= $book['ReleaseDate'] ? date('d/m/Y', strtotime($book['ReleaseDate'])) : 'N/A' ?>
                                    </td>
                                    <td><?= $book['Category'] ?></td>
                                    <td><?= number_format($book['Price'], 0, ',', '.') ?> ₫
                                </td>
                                    <td>
                                        <?php if ($book['Status'] == 1): ?>
                                            <span class="badge bg-success">Hiển thị</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ẩn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/admin/products/product-detail?id=<?= $book['BookID'] ?>"
                                            class="btn btn-sm fw-bold btn-primary">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <?php if ($book['Status'] == 1): ?>
                                            <button type="button" class="btn btn-sm btn-danger fw-bold ms-1 delete-btn">
                                                <i class="fas fa-trash"></i> Xóa/Ẩn
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-success fw-bold ms-1 restore-btn">
                                                <i class="fas fa-undo"></i> Hiện
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const categorySelect = document.getElementById('category');
        const statusSelect = document.getElementById('status');
        const searchBtn = document.getElementById('search-btn');
        const resetBtn = document.getElementById('reset-btn');
        const loadingIndicator = document.getElementById('loading-indicator');
        const dataContainer = document.getElementById('data-container');

        let productsTable = null;

        // Initial data load - immediate, no delay
        loadProductsData();

        // Search button click event
        searchBtn.addEventListener('click', function () {
            loadProductsData();
        });

        // Reset button click event
        resetBtn.addEventListener('click', function () {
            categorySelect.value = '';
            statusSelect.value = '';
            loadProductsData();
        });

        // Function to load products data
        function loadProductsData() {
            // Destroy existing DataTable if it exists
            if (productsTable !== null) {
                productsTable.destroy();
                productsTable = null;
            } else if ($.fn.DataTable.isDataTable('#productsTable')) {
                $('#productsTable').DataTable().destroy();
            }

            // Show loading indicator, hide data container
            loadingIndicator.classList.remove('d-none');
            dataContainer.classList.add('d-none');

            // Prepare query parameters
            const params = new URLSearchParams();
            if (categorySelect.value) {
                params.append('category', categorySelect.value);
            }
            if (statusSelect.value) {
                params.append('status', statusSelect.value);
            }

            // Return a promise that resolves when data is loaded
            return new Promise((resolve, reject) => {
                // Fetch data
                fetch(`/api/products/filtered?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        // Hide loading indicator
                        loadingIndicator.classList.add('d-none');

                        if (data.success) {
                            // Update the DOM with the fetched data
                            updateCategoriesDropdown(data.data.categories);
                            renderProductsTable(data.data.books);

                            // Show data container
                            dataContainer.classList.remove('d-none');

                            // Initialize DataTable
                            initDataTable();

                            // Resolve the promise
                            resolve();
                        } else {
                            console.error('Error loading products data');
                            reject('Error loading products data');
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

        // Function to update categories dropdown
        function updateCategoriesDropdown(categories) {
            const selectedValue = categorySelect.value;

            // Clear current options except the first one
            while (categorySelect.options.length > 1) {
                categorySelect.remove(1);
            }

            // Add new options
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.CategoryID;
                option.textContent = category.Name;
                if (selectedValue && selectedValue == category.CategoryID) {
                    option.selected = true;
                }
                categorySelect.appendChild(option);
            });
        }

        // Function to render products table
        function renderProductsTable(books) {
            const tableBody = document.getElementById('products-table-body');

            // Clear existing rows
            tableBody.innerHTML = '';

            if (books && books.length > 0) {
                // Add new rows
                books.forEach(book => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', book.BookID);
                    row.setAttribute('data-name', book.Name);
                    row.setAttribute('data-status', book.Status);

                    // ID column
                    let cell = document.createElement('td');
                    cell.textContent = book.BookID;
                    row.appendChild(cell);

                    // Image column
                    cell = document.createElement('td');
                    if (book.ImageURL) {
                        const img = document.createElement('img');
                        img.src = book.ImageURL;
                        img.alt = book.Name;
                        img.className = 'img-thumbnail';
                        img.style.height = '80px';
                        img.style.width = 'auto';
                        img.style.objectFit = 'contain';
                        cell.appendChild(img);
                    } else {
                        cell.innerHTML = `
                            <div class="text-center text-muted">
                                <i class="fas fa-image fa-3x"></i>
                                <p class="small">Không có ảnh</p>
                            </div>
                        `;
                    }
                    row.appendChild(cell);

                    // Name column
                    cell = document.createElement('td');
                    cell.className = 'fw-bold';
                    cell.textContent = book.Name;
                    row.appendChild(cell);

                    // Author column
                    cell = document.createElement('td');
                    cell.textContent = book.Author;
                    row.appendChild(cell);

                    // Publisher column
                    cell = document.createElement('td');
                    cell.textContent = book.Publisher || 'N/A';
                    row.appendChild(cell);

                    // Release Date column
                    cell = document.createElement('td');
                    if (book.ReleaseDate) {
                        const releaseDate = new Date(book.ReleaseDate);
                        cell.textContent = releaseDate.toLocaleDateString('vi-VN');
                        cell.setAttribute('data-order', Math.floor(releaseDate.getTime() / 1000));
                    } else {
                        cell.textContent = 'N/A';
                    }
                    row.appendChild(cell);

                    // Category column
                    cell = document.createElement('td');
                    cell.textContent = book.Category;
                    row.appendChild(cell);

                    // Price column
                    cell = document.createElement('td');
                    cell.textContent = new Intl.NumberFormat('vi-VN').format(book.Price) + ' ₫';
                    row.appendChild(cell);

                    // Status column
                    cell = document.createElement('td');
                    if (book.Status == 1) {
                        cell.innerHTML = '<span class="badge bg-success">Hiển thị</span>';
                    } else {
                        cell.innerHTML = '<span class="badge bg-danger">Ẩn</span>';
                    }
                    row.appendChild(cell);

                    // Actions column
                    cell = document.createElement('td');
                    let actions = `
                        <a href="/admin/products/product-detail?id=${book.BookID}" class="btn btn-sm fw-bold btn-primary">
                            <i class="fas fa-eye"></i> Xem
                        </a>
                    `;

                    if (book.Status == 1) {
                        actions += `
                            <button type="button" class="btn btn-sm btn-danger fw-bold ms-1 delete-btn">
                                <i class="fas fa-trash"></i> Xóa/Ẩn
                            </button>
                        `;
                    } else {
                        actions += `
                            <button type="button" class="btn btn-sm btn-success fw-bold ms-1 restore-btn">
                                <i class="fas fa-undo"></i> Hiện
                            </button>
                        `;
                    }

                    cell.innerHTML = actions;
                    row.appendChild(cell);

                    tableBody.appendChild(row);
                });
            } else {
                // No data
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.colSpan = 10; // Updated to match the new column count
                cell.className = 'text-center py-3';
                cell.textContent = 'Không tìm thấy sản phẩm nào';
                row.appendChild(cell);
                tableBody.appendChild(row);
            }
        }

        // Function to initialize DataTable
        function initDataTable() {
            if (typeof $.fn.DataTable !== 'undefined') {
                // Check if table is empty before initializing DataTable
                const tableBody = document.getElementById('products-table-body');
                if (!tableBody.querySelector('tr') || tableBody.querySelector('tr td[colspan="10"]')) {
                    // If there's no data or just an empty row message, don't initialize DataTable
                    return;
                }

                // If DataTable already exists on the table, destroy it
                if ($.fn.DataTable.isDataTable('#productsTable')) {
                    $('#productsTable').DataTable().destroy();
                }

                productsTable = $('#productsTable').DataTable({
                    dom: '<"row align-items-center mb-3"<"col-md-6"<"d-flex align-items-center"l>><"col-md-6"<"d-flex justify-content-end"f>>>rt<"row mt-3"<"col-md-6"i><"col-md-6"<"d-flex justify-content-end"p>>>',
                    language: {
                        "search": "Tìm kiếm:",
                        "searchPlaceholder": "Tên sách, Tác giả, Thể loại,...",
                        "lengthMenu": "<span class='me-2'>Hiển thị</span> _MENU_ <span class='ms-2'>sản phẩm</span>",
                        "info": "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ sản phẩm",
                        "infoEmpty": "Hiển thị 0 đến 0 trong tổng số 0 sản phẩm",
                        "infoFiltered": "",
                        "zeroRecords": "Không tìm thấy sản phẩm nào",
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
                        targets: [1, 7 ,9]
                    }, // Disable sorting for image and actions columns
                    {
                        searchable: true,
                        targets: [2, 3, 4, 5, 6]
                    }, // Allow search on name, author, publisher, release date, category
                    {
                        searchable: false,
                        targets: [0, 1, 7, 8, 9]
                    } // Disable search on other columns
                    ],
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

        // Function to confirm delete
        function confirmDelete(id, name) {
            showConfirmation(
                'Xác nhận xóa sản phẩm',
                'Nếu sản phẩm đã được bán/đặt trước đó, nó sẽ bị ẩn.',
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

                    fetch('/api/products/delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: id,
                            action: 'delete'
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Close loading indicator
                                loadingSwal.close();

                                // Instead of updating just the row, reload all the data
                                loadProductsData().then(() => {
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
                            console.error('Error:', error);
                            showError('Lỗi', 'Đã xảy ra lỗi khi xử lý yêu cầu.');
                        });
                }
            );
        }

        // Function to confirm restore
        function confirmRestore(id, name) {
            showConfirmation(
                'Xác nhận hiển thị lại sản phẩm',
                `Bạn có chắc chắn muốn hiển thị lại sản phẩm "${name}"?`,
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

                    fetch('/api/products/delete', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: id,
                            action: 'restore'
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Close loading indicator
                                loadingSwal.close();

                                // Instead of updating just the row, reload all the data
                                loadProductsData().then(() => {
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
                            console.error('Error:', error);
                            showError('Lỗi', 'Đã xảy ra lỗi khi xử lý yêu cầu.');
                        });
                }
            );
        }

        // Handle delete button click
        $(document).on('click', '.delete-btn', function () {
            const row = $(this).closest('tr');
            const id = row.data('id');
            const name = row.data('name');
            confirmDelete(id, name);
        });

        // Handle restore button click
        $(document).on('click', '.restore-btn', function () {
            const row = $(this).closest('tr');
            const id = row.data('id');
            const name = row.data('name');
            confirmRestore(id, name);
        });
    });
</script>