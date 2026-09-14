<div class="container my-5">
    <h2 class="section-title mb-4">Giỏ hàng của tôi</h2>

    <!-- Cart Content Wrapper -->
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Sản phẩm đã chọn</h5>
                        <button class="btn btn-outline-danger btn-sm" id="clearCart">
                            <i class="fas fa-trash me-2"></i>Xóa tất cả
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Empty Cart Message (hidden by default) -->
                    <div id="emptyCartMessage" class="text-center py-5" style="display: none;">
                        <img src="../../img/empty-cart.png" alt="Giỏ hàng trống" class="img-fluid mb-3"
                            style="max-width: 200px;">
                        <h4 class="mb-3">Giỏ hàng của bạn đang trống</h4>
                        <p class="text-muted mb-4">Hãy thêm sản phẩm vào giỏ hàng của bạn</p>
                        <a href="/shop" class="btn btn-danger">
                            <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>

                    <!-- Cart Items Table -->
                    <div id="cartItemsContainer">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" width="100">Sản phẩm</th>
                                        <th scope="col">Tên sách</th>
                                        <th scope="col" class="text-center">Đơn giá</th>
                                        <th scope="col" class="text-center">Số lượng</th>
                                        <th scope="col" class="text-end">Thành tiền</th>
                                        <th scope="col" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <!-- Cart items will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Continue Shopping Button -->
            <div class="d-flex mb-4">
                <a href="/shop" class="btn btn-outline-dark" id="shoppingBtn">
                    <i class="fas fa-chevron-left me-2"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Tổng tiền giỏ hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tổng sản phẩm:</span>
                        <span id="cartSummaryCount">0 sản phẩm</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tạm tính:</span>
                        <span id="subtotal" class="fw-bold">0 ₫</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5">Tổng cộng:</span>
                        <span id="total" class="h5 text-danger">0 ₫</span>
                    </div>
                    <div class="d-grid gap-2">
                        <button id="checkoutButton" class="btn btn-danger btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Thanh toán
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    // Import the showToast function from util.js
    import {
        showToast
    } from '/assets/client/js/util.js';

    // Get cart items container elements
    const cartTableBody = document.getElementById('cartTableBody');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const cartSummaryCount = document.getElementById('cartSummaryCount');
    const subtotalElement = document.getElementById('subtotal');
    const totalElement = document.getElementById('total');
    const checkoutButton = document.getElementById('checkoutButton');
    const clearCartButton = document.getElementById('clearCart');

    // Function to update cart UI
    function updateCartUI() {
        // Get cart data from localStorage
        const savedCart = localStorage.getItem('cart');
        let cartItems = [];

        if (savedCart) {
            try {
                const parsedData = JSON.parse(savedCart);
                if (Array.isArray(parsedData)) {
                    cartItems = parsedData;
                }
            } catch (error) {
                console.error('Error parsing cart data:', error);
            }
        }

        // Calculate totals for cart items
        let totalItems = 0;
        let totalPrice = 0;
        let cartItemsHtml = '';

        if (cartItems.length > 0) {
            cartItems.forEach(item => {
                // Find book details from allBooks array
                const book = allBooks.find(b => b.BookID == item.id);

                if (book) {
                    const price = parseFloat((book.Price).toLocaleString('vi-VN'));
                    const itemTotal = price * item.quantity;
                    totalItems += item.quantity;
                    totalPrice += itemTotal;

                    // Check if book is in stock
                    const isOutOfStock = book.Stock <= 0;
                    const stockWarning = book.Stock < 10 ?
                        `<div class="text-danger small">Chỉ còn ${book.Stock} sản phẩm</div>` : '';

                    cartItemsHtml += `
                    <tr data-id="${book.BookID}">
                        <td>
                            <a href="/product-detail?id=${book.BookID}">
                                <img src="${book.ImageURL}" alt="${book.Name}" class="img-fluid" 
                                    style="max-width: 80px; max-height: 120px;">
                            </a>
                        </td>
                        <td>
                            <a href="/product-detail?id=${book.BookID}" class="text-decoration-none text-dark">
                                <h6 class="mb-1">${book.Name}</h6>
                            </a>
                            <div class="text-muted small">${book.Author}</div>
                            ${stockWarning}
                        </td>
                        <td class="text-center">${price.toLocaleString()} ₫</td>
                        <td class="text-center">
                            <div class="input-group input-group-sm" style="max-width: 120px; margin: 0 auto;">
                                <button class="btn btn-outline-secondary decrease-qty" type="button" 
                                    ${item.quantity <= 1 ? 'disabled' : ''}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center item-qty" value="${item.quantity}" 
                                    min="1" max="${book.Stock}" 
                                    ${isOutOfStock ? 'disabled' : ''}>
                                <button class="btn btn-outline-secondary increase-qty" type="button"
                                    ${item.quantity >= book.Stock || isOutOfStock ? 'disabled' : ''}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </td>
                        <td class="text-end fw-bold">${itemTotal.toLocaleString()} ₫</td>
                        <td class="text-center">
                            <button class="btn btn-sm text-danger remove-item" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                }
            });

            cartTableBody.innerHTML = cartItemsHtml;
        }

        // Update display based on cart state
        resetCartInfo(totalItems, totalPrice);
    }

    // Event delegation for quantity buttons
    document.addEventListener('click', function (e) {
        if (e.target.closest('.decrease-qty')) {
            const row = e.target.closest('tr');
            const itemId = parseInt(row.dataset.id);
            updateItemQuantity(itemId, -1);
        } else if (e.target.closest('.increase-qty')) {
            const row = e.target.closest('tr');
            const itemId = parseInt(row.dataset.id);
            updateItemQuantity(itemId, 1);
        } else if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            const itemId = parseInt(row.dataset.id);
            removeItem(itemId);
        }
    });

    // Event for input quantity change
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('item-qty')) {
            const row = e.target.closest('tr');
            const itemId = parseInt(row.dataset.id);
            const newQty = parseInt(e.target.value);
            setItemQuantity(itemId, newQty);
        }
    });

    // Clear cart button
    clearCartButton.addEventListener('click', function () {
        // Use SweetAlert for confirmation
        Swal.fire({
            title: 'Xóa giỏ hàng',
            text: 'Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Clear cart in localStorage
                localStorage.removeItem('cart');

                // Reset the cart display to empty state
                resetCartInfo(0, 0);

                // Update cart UI
                updateCartUI();

                // Update global cart interface
                window.updateCartInterface();

                // Show success message
                showToast('Đã xóa toàn bộ giỏ hàng', {
                    type: 'success',
                    title: 'Giỏ hàng'
                });
            }
        });
    });

    // Checkout button
    checkoutButton.addEventListener('click', function () {
        <?php if (!isset($_SESSION['UserID'])): ?>
            // If user is not logged in, show login required message
            Swal.fire({
                title: 'Đăng nhập',
                text: 'Vui lòng đăng nhập để tiến hành thanh toán',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đăng nhập',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show login modal
                    const authModal = new bootstrap.Modal(document.getElementById('authModal'));
                    authModal.show();
                    // Set the tab to login
                    const loginTab = document.querySelector('#authModal [data-bs-target="#login-tab-pane"]');
                    if (loginTab) {
                        loginTab.click();
                    }
                }
            });
        <?php else: ?>
            // If user is logged in, redirect to checkout page
            window.location.href = '/cart/checkout';
        <?php endif; ?>
    });

    // Function to update item quantity
    function updateItemQuantity(itemId, change) {
        // Get cart data
        const savedCart = localStorage.getItem('cart');
        let cartItems = [];

        if (savedCart) {
            try {
                const parsedData = JSON.parse(savedCart);
                if (Array.isArray(parsedData)) {
                    cartItems = parsedData;
                }
            } catch (error) {
                console.error('Error parsing cart data:', error);
            }
        }

        // Find item
        const itemIndex = cartItems.findIndex(item => item.id === itemId);
        if (itemIndex !== -1) {
            // Get book details to check stock
            const book = allBooks.find(b => b.BookID == itemId);
            if (!book) return;

            const maxStock = book.Stock || 0;
            const newQty = cartItems[itemIndex].quantity + change;

            // Check if new quantity is valid
            if (newQty > 0 && newQty <= maxStock) {
                cartItems[itemIndex].quantity = newQty;

                // Save updated cart
                localStorage.setItem('cart', JSON.stringify(cartItems));

                // Update UI
                updateCartUI();
                // Update global cart interface
                window.updateCartInterface();

                // Update cart info with new totals
                updateCartTotals(cartItems);
            } else if (newQty > maxStock) {
                // Show error message for max stock
                showToast(`Chỉ còn ${maxStock} "${book.Name}" trong kho`, {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
            }
        }
    }

    // Function to set item quantity directly
    function setItemQuantity(itemId, quantity) {
        // Get cart data
        const savedCart = localStorage.getItem('cart');
        let cartItems = [];

        if (savedCart) {
            try {
                const parsedData = JSON.parse(savedCart);
                if (Array.isArray(parsedData)) {
                    cartItems = parsedData;
                }
            } catch (error) {
                console.error('Error parsing cart data:', error);
            }
        }

        // Find item
        const itemIndex = cartItems.findIndex(item => item.id === itemId);
        if (itemIndex !== -1) {
            // Get book details to check stock
            const book = allBooks.find(b => b.BookID == itemId);
            if (!book) return;

            const maxStock = book.Stock || 0;

            // Handle invalid quantity input (empty, NaN, or contains non-numeric characters)
            if (quantity === '' || isNaN(quantity) || !/^\d+$/.test(String(quantity))) {
                // Reset to current quantity in UI
                updateCartUI();
                showToast('Vui lòng nhập số lượng hợp lệ', {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
                return;
            }

            // Convert to integer if it's a valid number string
            quantity = parseInt(quantity);

            // Check if quantity is valid
            if (quantity > 0 && quantity <= maxStock) {
                cartItems[itemIndex].quantity = quantity;
            } else if (quantity > maxStock) {
                cartItems[itemIndex].quantity = maxStock;
                showToast(`Chỉ còn ${maxStock} "${book.Name}" trong kho`, {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
            } else if (quantity <= 0) {
                // If quantity is 0 or negative, remove item
                removeItem(itemId);
                return;
            }

            // Save updated cart
            localStorage.setItem('cart', JSON.stringify(cartItems));

            // Update UI
            updateCartUI();
            // Update global cart interface
            window.updateCartInterface();

            // Update cart info with new totals
            updateCartTotals(cartItems);
        }
    }

    // Function to remove item
    function removeItem(itemId) {
        // Get cart data
        const savedCart = localStorage.getItem('cart');
        let cartItems = [];

        if (savedCart) {
            try {
                const parsedData = JSON.parse(savedCart);
                if (Array.isArray(parsedData)) {
                    cartItems = parsedData;
                }
            } catch (error) {
                console.error('Error parsing cart data:', error);
            }
        }

        // Find item
        const itemIndex = cartItems.findIndex(item => item.id === itemId);
        if (itemIndex !== -1) {
            // Get book name for message
            const book = allBooks.find(b => b.BookID == itemId);
            const bookName = book ? book.Name : 'Sản phẩm';

            // Remove item
            cartItems.splice(itemIndex, 1);

            // Save updated cart
            localStorage.setItem('cart', JSON.stringify(cartItems));

            // Update UI
            updateCartUI();
            // Update global cart interface
            window.updateCartInterface();

            // Update cart info with new totals
            updateCartTotals(cartItems);

            // Show success message
            showToast(`Đã xóa "${bookName}" khỏi giỏ hàng`, {
                type: 'success',
                title: 'Giỏ hàng'
            });
        }
    }

    // Function to calculate and update cart totals
    function updateCartTotals(cartItems) {
        let totalItems = 0;
        let totalPrice = 0;

        cartItems.forEach(item => {
            const book = allBooks.find(b => b.BookID == item.id);
            if (book) {
                const price = parseFloat((book.Price).toLocaleString('vi-VN'));
                totalItems += item.quantity;
                totalPrice += price * item.quantity;
            }
        });

        // Reset cart information with new values
        resetCartInfo(totalItems, totalPrice);
    }

    // Function to reset cart information display
    function resetCartInfo(itemCount, totalAmount) {
        // Update summary displays
        cartSummaryCount.textContent = `${itemCount} sản phẩm`;
        subtotalElement.textContent = `${totalAmount.toLocaleString()} ₫`;
        totalElement.textContent = `${totalAmount.toLocaleString()} ₫`;

        // Show/hide elements based on item count
        if (itemCount === 0) {
            emptyCartMessage.style.display = 'block';
            cartItemsContainer.style.display = 'none';
            clearCartButton.disabled = true;
            checkoutButton.disabled = true;
            document.getElementById('shoppingBtn').style.display = 'none';
        } else {
            emptyCartMessage.style.display = 'none';
            cartItemsContainer.style.display = 'block';
            clearCartButton.disabled = false;
            checkoutButton.disabled = false;
            document.getElementById('shoppingBtn').style.display = 'block';
        }
    }

    // Initialize cart UI
    updateCartUI();
</script>

<style>
    /* Cart page specific styles */
    .table-responsive {
        border-radius: 0.25rem;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .item-qty {
        text-align: center;
        max-width: 60px;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    .decrease-qty,
    .increase-qty {
        transition: all 0.2s;
    }

    .decrease-qty:disabled,
    .increase-qty:disabled {
        opacity: 0.5;
    }

    .remove-item {
        border: none;
        background: transparent;
        transition: all 0.2s;
    }

    .remove-item:hover {
        transform: scale(1.2);
    }
</style>