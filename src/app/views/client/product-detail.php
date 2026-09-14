<!-- Product Detail Section -->
<div class="container my-5">
    <!-- Product Main Section -->
    <div class="row mb-5">
        <!-- Product Image -->
        <div class="col-md-5">
            <div class="book-image-container">
                <img src="<?= $book['ImageURL'] ?? '' ?>" alt="<?= $book['Name'] ?? 'Book' ?>"
                    class="img-fluid book-main-image">
                <?php if (isset($book['Stock']) && $book['Stock'] <= 0): ?>
                    <div class="out-of-stock-overlay">
                        <span class="out-of-stock-badge">Tạm hết hàng</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details & Purchase Options -->
        <div class="col-md-7">
            <div class="book-info-container p-4">
                <!-- Product Name -->
                <h2 class="book-title mb-3"><?= $book['Name'] ?? 'Không có tiêu đề' ?></h2>

                <!-- Product Information in two columns -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <?php if (isset($book['Author']) && $book['Author']): ?>
                            <div class="mb-2">
                                <span class="text-muted">Tác giả:</span>
                                <span style="color: #dc3545"><?= $book['Author'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($book['Publisher']) && $book['Publisher']): ?>
                            <div class="mb-2">
                                <span class="text-muted">Nhà xuất bản:</span>
                                <span style="color: #dc3545"><?= $book['Publisher'] ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <?php if (isset($book['Category']) && $book['Category']): ?>
                            <div class="mb-2">
                                <span class="text-muted">Thể loại:</span>
                                <span class="book-category"><?= $book['Category'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($book['ReleaseDate']) && $book['ReleaseDate']): ?>
                            <div class="mb-2">
                                <span class="text-muted">Ngày phát hành:</span>
                                <span><?= date('d/m/Y', strtotime($book['ReleaseDate'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($book['Price'])): ?>
                    <div class="book-price mb-3">
                        <span class="price-value"><?= number_format($book['Price'], 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>

                <!-- <div class="book-stock mb-4">
                    <span class="text-muted">Tình trạng:</span>
                    < ?php if ($book['Stock'] > 0): ?>
                        <span class="text-success">Còn hàng (< ?= $book['Stock'] ?> sản phẩm)</span>
                    < ?php else: ?>
                        <span class="text-danger">Tạm hết hàng</span>
                    < ?php endif; ?>
                </div>  -->

                <div class="purchase-options">
                    <div class="row g-3">
                        <!-- Quantity control at the top -->
                        <div class="col-12 mb-3">
                            <label for="quantity" class="form-label">Số lượng:</label>
                            <div class="input-group" style="max-width: 150px;">
                                <button class="btn btn-outline-danger" type="button" id="decrease-qty">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1"
                                    max="<?= $book['Stock'] ?? 0 ?>" <?= !isset($book['Stock']) || $book['Stock'] <= 0 ? 'disabled' : '' ?>
                                    style="border-color: #dc3545 !important; box-shadow: none !important;">
                                <button class="btn btn-outline-danger" type="button" id="increase-qty"
                                    <?= !isset($book['Stock']) || $book['Stock'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Buttons below -->
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button class="btn btn-lg w-100" id="buy-now"
                                    style="background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; border-radius: 6px !important; transition: opacity 0.2s !important;"
                                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                                    <?= !isset($book['Stock']) || $book['Stock'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-bolt me-2"></i>Mua ngay
                                </button>
                                <button class="btn btn-lg w-100" id="add-to-cart"
                                    style="background-color: #dc3545 !important; color: white !important; border-color: #dc3545 !important; border-radius: 6px !important; transition: opacity 0.2s !important;"
                                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                                    <?= !isset($book['Stock']) || $book['Stock'] <= 0 ? 'disabled' : '' ?>>
                                    <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs Section -->
    <div class="book-tabs mb-5">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description"
                    type="button" role="tab" aria-controls="description" aria-selected="true">
                    Mô tả sản phẩm
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button"
                    role="tab" aria-controls="details" aria-selected="false">
                    Thông tin chi tiết
                </button>
            </li>
        </ul>
        <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabContent">
            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                <?php if (isset($book['Description']) && $book['Description']): ?>
                    <p><?= $book['Description'] ?></p>
                <?php else: ?>
                    <p>Chưa có mô tả về cuốn sách này.</p>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tbody>
                                <?php if (isset($book['Author']) && $book['Author']): ?>
                                    <tr>
                                        <th>Tác giả:</th>
                                        <td><?= $book['Author'] ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($book['Category']) && $book['Category']): ?>
                                    <tr>
                                        <th>Thể loại:</th>
                                        <td><?= $book['Category'] ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($book['Publisher']) && $book['Publisher']): ?>
                                    <tr>
                                        <th>Nhà xuất bản:</th>
                                        <td><?= $book['Publisher'] ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($book['ReleaseDate']) && $book['ReleaseDate']): ?>
                                    <tr>
                                        <th>Ngày phát hành:</th>
                                        <td><?= date('d/m/Y', strtotime($book['ReleaseDate'])) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tbody>
                                <?php if (isset($book['Language']) && $book['Language']): ?>
                                    <tr>
                                        <th>Ngôn ngữ:</th>
                                        <td><?= $book['Language'] ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($book['Weight']) && $book['Weight']): ?>
                                    <tr>
                                        <th>Trọng lượng:</th>
                                        <td><?= $book['Weight'] ?> kg</td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($book['Dimensions']) && $book['Dimensions']): ?>
                                    <tr>
                                        <th>Kích thước:</th>
                                        <td><?= $book['Dimensions'] ?></td>
                                    </tr>
                                <?php endif; ?>

                                <?php if (isset($book['Length']) && $book['Length']): ?>
                                    <tr>
                                        <th>Số trang:</th>
                                        <td><?= $book['Length'] ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($book['Format']) && $book['Format']): ?>
                                    <tr>
                                        <th>Định dạng:</th>
                                        <td><?= $book['Format'] ?></td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product detail page -->
<script type="module">
    import {
        showSweetAlert
    } from '/assets/client/js/util.js';

    document.addEventListener('DOMContentLoaded', () => {
        // const productId = < ?= $book['BookID'] ?>;
        // const book = allBooks.find(b => b.BookID == productId);
        const book = <?= json_encode($book) ?>;
        const maxStock = <?= isset($book['Stock']) ? $book['Stock'] : 0 ?>;

        // Quantity controls
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-qty');
        const increaseBtn = document.getElementById('increase-qty');

        // Update quantity functions
        function updateQuantity(change) {
            let currentQty = parseInt(quantityInput.value);
            let newQty = currentQty + change;

            if (newQty < 1) newQty = 1;
            if (newQty > maxStock) newQty = maxStock;

            quantityInput.value = newQty;
        }

        // Add event listeners
        decreaseBtn.addEventListener('click', () => updateQuantity(-1));
        increaseBtn.addEventListener('click', () => updateQuantity(1));

        // Direct input validation
        quantityInput.addEventListener('change', () => {
            let qty = parseInt(quantityInput.value);
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > maxStock) qty = maxStock;
            quantityInput.value = qty;
        });

        // Add to cart button
        document.getElementById('add-to-cart').addEventListener('click', () => {
            if (!book) return;

            // Get current quantity
            const quantity = parseInt(quantityInput.value);

            // Simulate multiple adds to cart
            let added = false;
            for (let i = 0; i < quantity; i++) {
                added = window.addToCart(book, i > 0); // Only show notification for first addition
            }

            if (added && quantity > 1) {
                showToast(`Đã thêm ${quantity} sản phẩm "${book.Name}" vào giỏ hàng`, {
                    type: 'success',
                    title: 'Giỏ hàng'
                });
            }

            // Update the cart interface
            window.updateCartInterface();
        });

        document.getElementById('buy-now').addEventListener('click', () => {
            if (!book) return;

            // Get current quantity
            const quantity = parseInt(quantityInput.value);

            // Add to cart first without notification
            let added = false;
            for (let i = 0; i < quantity; i++) {
                added = window.addToCart(book, true);
            }

            // Update the cart interface
            updateCartInterface();

            if (added) {
                window.location.href = '/cart';
            }
        });
    });
</script>

<style>
    /* Product Detail Page Styles */
    .book-title {
        font-size: 2rem;
        color: #333;
        font-weight: 700;
        border-bottom: 1px solid #bdbdbd;
        padding-bottom: 0.5rem;
    }

    .book-image-container {
        position: relative;
        background-color: #f9f9f9;
        padding: 2rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .book-main-image {
        max-height: 400px;
        object-fit: contain;
    }

    .out-of-stock-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }

    .out-of-stock-badge {
        background-color: #808991;
        color: white;
        padding: 0.5rem 1.5rem;
        font-size: 1.2rem;
        font-weight: 700;
        border-radius: 0.25rem;
        transform: rotate(-10deg);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .book-info-container {
        background-color: #f9f9f9;
        border-radius: 0.5rem;
        height: 100%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .book-author {
        font-size: 1.1rem;
    }

    .book-price {
        font-size: 1.8rem;
        font-weight: 700;
        color: #e74c3c;
    }

    .price-value::after {
        content: " ₫";
    }

    .purchase-options {
        /* margin-top: 2rem; */
        padding-top: 1.5rem;
        border-top: 2px dashed #A5D6A7;
    }

    /* Quantity Input Styling */
    .input-group .form-control {
        height: calc(2rem + 8px);
    }

    .input-group .btn {
        padding: 0.25rem 0.75rem;
    }

    /* Tab Content Styling */
    .book-tabs .nav-tabs .nav-link {
        color: #555;
        font-weight: 500;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .book-tabs .nav-tabs .nav-link.active {
        color: #e74c3c;
        font-weight: 700;
        border-top: 3px solid #e74c3c;
    }

    .tab-content {
        background-color: #fff;
    }

    .tab-pane {
        min-height: 200px;
    }

    /* Table styling for book details */
    .tab-pane .table {
        margin-bottom: 0;
    }

    .tab-pane .table th {
        width: 40%;
        color: #666;
        font-weight: 600;
    }

    .tab-pane .table td {
        color: #333;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .book-title {
            font-size: 1.5rem;
        }

        .book-image-container {
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .purchase-options .row {
            flex-direction: column;
        }

        .purchase-options .col-md-3 {
            width: 100%;
            margin-bottom: 1rem;
        }

        .purchase-options .col-md-9 {
            width: 100%;
        }
    }
</style>