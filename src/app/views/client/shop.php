<!-- Shop page -->
<div class="container mt-4 mb-5">
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-lg-3">
            <div class="shop-sidebar">
                <div class="filter-section card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">Tìm kiếm sách</h4>
                    </div>
                    <div class="card-body">
                        <form id="shop-filter-form">
                            <div class="mb-3">
                                <label for="search-term" class="form-label">Tên sách</label>
                                <input type="text" class="form-control" id="search-term" placeholder="Nhập tên sách..."
                                    <?php if (isset($_GET['search'])): ?>
                                    value="<?php echo $_GET['search']; ?>"
                                    <?php endif; ?>>
                            </div>

                            <div class="mb-3">
                                <label for="category-filter" class="form-label">Thể loại</label>
                                <select class="form-select" id="category-filter">
                                    <option value="">Tất cả thể loại</option>
                                    <?php if (isset($categories) && is_array($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['CategoryID']; ?>" data-category-id="<?= $category['CategoryID']; ?>" <?= isset($_GET['category']) && $_GET['category'] == $category['Name'] ? 'selected' : ''; ?>>
                                                <?= $category['Name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Khoảng giá</label>
                                <div class="price-slider-container mb-3">
                                    <div id="price-range-slider"></div>
                                    <div class="price-range-labels">
                                        <span class="price-min"></span>
                                        <span class="price-max"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="min-price" class="form-label">Từ</label>
                                        <input type="text" class="form-control form-control-sm" id="min-price" min="0">
                                    </div>
                                    <!-- <div class="col-1 d-flex align-items-end justify-content-center">
                                        <span class="mb-1">–</span>
                                    </div> -->
                                    <div class="col-6">
                                        <label for="max-price" class="form-label">Đến</label>
                                        <input type="text" class="form-control form-control-sm" id="max-price" min="0">
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="apply-filter" class="btn btn-danger w-100">
                                <i class="fas fa-search mr-2"></i> Tìm kiếm
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="shop-controls mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="section-title shop-title mb-md-0">Tất cả sách</h2>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-md-end">
                            <label for="sort-by" class="me-2 mb-0">Sắp xếp:</label>
                            <select class="form-select form-select-sm w-auto" id="sort-by">
                                <option value="default">Mặc định</option>
                                <option value="price-asc">Giá: Thấp đến cao</option>
                                <option value="price-desc">Giá: Cao đến thấp</option>
                                <option value="name-asc">Tên: A-Z</option>
                                <option value="name-desc">Tên: Z-A</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="filter-results" class="mb-3">
                <span id="result-count" class="badge bg-secondary">0 sản phẩm</span>
                <button id="clear-filters" class="btn btn-sm btn-outline-secondary" style="display: none;">
                    <i class="fas fa-times"></i> Xóa bộ lọc
                </button>
            </div>

            <div class="row" id="shop-products-container">
                <!-- Products will be populated via JavaScript -->
                <div class="col-12 text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải sản phẩm...</p>
                </div>
            </div>

            <!-- Pagination -->
            <div id="shop-pagination-container"></div>
        </div>
    </div>
</div>