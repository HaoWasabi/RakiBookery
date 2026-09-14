<div class="container my-5">
    <h2 class="section-title mb-4">Thanh toán</h2>

    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <!-- User Information Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <!-- Error Alert -->
                    <div id="checkoutErrorAlert" class="alert alert-danger alert-dismissible fade show d-none"
                        role="alert">
                        <span id="errorAlertMessage">Vui lòng điền đầy đủ thông tin trước khi tiếp tục.</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <form id="checkoutForm" method="POST" action="/place-order">
                        <!-- Personal Information - Always Disabled -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Họ và tên" value="<?= htmlspecialchars($user['Name'] ?? '') ?>"
                                        disabled required>
                                    <label for="name">Họ và tên <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                        value="<?= htmlspecialchars($user['Email'] ?? '') ?>" disabled required>
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="Số điện thoại"
                                        value="<?= htmlspecialchars($user['Phone'] ?? '') ?>" disabled required>
                                    <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="border-top pt-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Địa chỉ giao hàng</h5>
                                <?php
                                $hasAddress = !empty($user['Address']) && !empty($user['City']) && !empty($user['District']) && !empty($user['Ward']);
                                if ($hasAddress): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="newAddressBtn">
                                        <i class="fas fa-edit me-1"></i>Nhập địa chỉ mới
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Existing Address Display -->
                            <?php if ($hasAddress): ?>
                                <div id="displayAddressSection">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <?php
                                            $fullAddress = [];
                                            if (!empty($user['Address']))
                                                $fullAddress[] = $user['Address'];
                                            if (!empty($user['Ward']))
                                                $fullAddress[] = $user['Ward'];
                                            if (!empty($user['District']))
                                                $fullAddress[] = $user['District'];
                                            if (!empty($user['City']))
                                                $fullAddress[] = $user['City'];
                                            $combinedAddress = implode(', ', $fullAddress);
                                            ?>
                                            <p class="mb-0">
                                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                                <?= htmlspecialchars($combinedAddress); ?>
                                            </p>
                                            <input type="hidden" name="address_id" id="address_id"
                                                value="<?= htmlspecialchars($user['AddressID'] ?? '0') ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- New Address Form -->
                            <div id="newAddressSection" <?= $hasAddress ? 'style="display:none;"' : '' ?>>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="new_address" name="new_address"
                                                placeholder="Địa chỉ" value="" required>
                                            <label for="new_address">Địa chỉ chi tiết <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="new_city" name="new_city"
                                                placeholder="Tỉnh/Thành phố" value="" required>
                                            <label for="new_city">Tỉnh/Thành phố <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="new_district"
                                                name="new_district" placeholder="Quận/Huyện" value="" required>
                                            <label for="new_district">Quận/Huyện <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="new_ward" name="new_ward"
                                                placeholder="Phường/Xã" value="" required>
                                            <label for="new_ward">Phường/Xã <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($hasAddress): ?>
                                    <div class="text-end mb-3">
                                        <button type="button" class="btn btn-outline-secondary" id="cancelNewAddressBtn">
                                            <i class="fas fa-times me-1"></i>Hủy
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Phương thức thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="payment-methods">
                        <?php foreach ($payment_methods as $method): ?>
                            <div class="form-check payment-method-item mb-3">
                                <input class="form-check-input" type="radio" name="paymentMethod"
                                    id="payment-<?= $method['PaymentMethodID'] ?>" value="<?= $method['PaymentMethodID'] ?>"
                                    <?= $method['PaymentMethodID'] == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label d-flex align-items-center"
                                    for="payment-<?= $method['PaymentMethodID'] ?>">
                                    <?php if (stripos($method['Name'], 'tiền mặt') !== false || stripos($method['Name'], 'cod') !== false): ?>
                                        <i class="fas fa-money-bill-wave text-success me-2"></i>
                                    <?php else: ?>
                                        <i class="fas fa-credit-card text-primary me-2"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($method['Name']) ?>
                                </label>
                                <?php if (stripos($method['Name'], 'tiền mặt') !== false || stripos($method['Name'], 'cod') !== false): ?>
                                    <div class="form-text ms-4">Thanh toán khi nhận hàng</div>
                                <?php else: ?>
                                    <div class="form-text ms-4">Thanh toán qua thẻ hoặc ví điện tử</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Order Items Preview -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Đơn hàng của bạn</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Cart Items Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="80">Sản phẩm</th>
                                    <th scope="col">Tên sách</th>
                                    <th scope="col" class="text-center">Đơn giá</th>
                                    <th scope="col" class="text-center">Số lượng</th>
                                    <th scope="col" class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody id="checkoutTableBody">
                                <!-- Cart items will be loaded here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 position-sticky" style="top: 1rem;">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Tổng đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tổng sản phẩm:</span>
                        <span id="orderSummaryCount">0 sản phẩm</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tạm tính:</span>
                        <span id="orderSubtotal" class="fw-bold">0 ₫</span>
                    </div>
                    <!-- <div class="d-flex justify-content-between mb-3">
                        <span>Phí vận chuyển:</span>
                        <span id="orderShipping">30.000 ₫</span>
                    </div> -->
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5">Tổng cộng:</span>
                        <span id="orderTotal" class="h5 text-danger">0 ₫</span>
                    </div>
                    <div class="d-grid gap-2">
                        <button id="placeOrderButton" class="btn btn-danger btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Đặt hàng
                        </button>
                        <a href="/cart" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get cart items container elements
        const checkoutTableBody = document.getElementById('checkoutTableBody');
        const orderSummaryCount = document.getElementById('orderSummaryCount');
        const orderSubtotal = document.getElementById('orderSubtotal');
        const orderTotal = document.getElementById('orderTotal');
        const placeOrderButton = document.getElementById('placeOrderButton');
        const checkoutForm = document.getElementById('checkoutForm');
        const errorAlert = document.getElementById('checkoutErrorAlert');
        const errorAlertMessage = document.getElementById('errorAlertMessage');

        // Function to sync localStorage cart to session
        function syncCartToSession() {
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
                    return false;
                }
            }

            // If cart is empty, return false
            if (cartItems.length === 0) {
                return false;
            }

            // Convert cart items to session format
            const sessionCart = cartItems.map(item => {
                // Find book details from allBooks array
                const book = allBooks.find(b => b.BookID == item.id);
                if (book) {
                    return {
                        product_id: item.id,
                        quantity: item.quantity,
                        price: parseFloat((book.Price).toLocaleString('vi-VN'))
                    };
                }
                return null;
            }).filter(item => item !== null); // Remove null items

            // Send cart data to server to update session
            fetch('/sync-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    cart: sessionCart
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Cart synced to session:', data);
                    return data.success;
                })
                .catch(error => {
                    console.error('Error syncing cart to session:', error);
                    return false;
                });

            return true;
        }

        // Function to update checkout UI
        function updateCheckoutUI() {
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

            // If cart is empty, redirect to cart page
            if (cartItems.length === 0) {
                Swal.fire({
                    title: 'Giỏ hàng trống',
                    text: 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán.',
                    icon: 'warning',
                    confirmButtonColor: '#e74c3c',
                    confirmButtonText: 'Đi đến cửa hàng'
                }).then((result) => {
                    window.location.href = '/shop';
                });
                return;
            }

            // Generate cart items HTML
            let html = '';
            let totalItems = 0;
            let subtotal = 0;

            cartItems.forEach(item => {
                // Find book details from allBooks array
                const book = allBooks.find(b => b.BookID == item.id);

                if (book) {
                    const price = parseFloat((book.Price).toLocaleString('vi-VN'));
                    const itemTotal = price * item.quantity;
                    totalItems += item.quantity;
                    subtotal += itemTotal;

                    html += `
                <tr>
                    <td>
                        <img src="${book.ImageURL}" alt="${book.Name}" class="img-fluid" 
                            style="max-width: 60px; max-height: 90px;">
                    </td>
                    <td>
                        <h6 class="mb-0">${book.Name}</h6>
                        <small class="text-muted">${book.Author}</small>
                    </td>
                    <td class="text-center">${price.toLocaleString()} ₫</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-end fw-bold">${itemTotal.toLocaleString()} ₫</td>
                </tr>
                `;
                }
            });

            checkoutTableBody.innerHTML = html;

            // Update summary
            orderSummaryCount.textContent = `${totalItems} sản phẩm`;
            orderSubtotal.textContent = `${subtotal.toLocaleString()} ₫`;

            const total = subtotal;
            orderTotal.textContent = `${total.toLocaleString()} ₫`;

            // Store order summary for submission
            window.orderSummary = {
                items: cartItems,
                subtotal: subtotal,
                total: total,
                itemCount: totalItems
            };

            // Sync cart to session
            syncCartToSession();
        }

        // Handle place order button click
        placeOrderButton.addEventListener('click', function () {
            // Get required fields based on which address section is visible
            let requiredFields;
            const isUsingNewAddress = document.getElementById('displayAddressSection') ?
                document.getElementById('displayAddressSection').style.display === 'none' : true;

            if (isUsingNewAddress) {
                // Using new address - validate new address fields
                requiredFields = document.querySelectorAll('#newAddressSection [required]');
            } else {
                // Using existing address - only validate fields not in address section
                requiredFields = document.querySelectorAll('#checkoutForm [required]:not([id^="new_"])');
            }

            let isValid = true;
            let firstInvalidField = null;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                errorAlert.classList.remove('d-none');
                errorAlertMessage.textContent = 'Vui lòng điền đầy đủ thông tin trước khi tiếp tục.';

                // Scroll to first invalid field
                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalidField.focus();
                }

                return;
            }

            // Get selected payment method
            const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
            if (!selectedPaymentMethod) {
                errorAlert.classList.remove('d-none');
                errorAlertMessage.textContent = 'Vui lòng chọn phương thức thanh toán.';
                return;
            }

            // Prepare order data
            const formData = new FormData();

            // Add address information
            const addressId = document.getElementById('address_id');
            const displayAddressSection = document.getElementById('displayAddressSection');

            // Check if we're using existing address or new address
            const isUsingExistingAddress = addressId &&
                addressId.value !== '0' &&
                displayAddressSection &&
                displayAddressSection.style.display !== 'none';

            if (isUsingExistingAddress) {
                // Using existing address
                formData.append('address_id', addressId.value);
            } else {
                // Using new address
                formData.append('new_address', document.getElementById('new_address').value);
                formData.append('new_ward', document.getElementById('new_ward').value);
                formData.append('new_district', document.getElementById('new_district').value);
                formData.append('new_city', document.getElementById('new_city').value);
            }

            // Add payment method
            formData.append('payment_method_id', selectedPaymentMethod.value);

            // Show loading state
            /* Swal.fire({
                title: 'Đang xử lý',
                text: 'Đơn hàng của bạn đang được xử lý...',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
 */
            // Submit order to server
            fetch('/process_checkout', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Đặt hàng thành công!',
                            text: `Đơn hàng đã được đặt thành công. Cảm ơn bạn đã mua hàng!`,
                            icon: 'success',
                            confirmButtonColor: '#e74c3c',
                            confirmButtonText: 'Xem đơn hàng'
                        }).then((result) => {
                            // Clear cart
                            localStorage.removeItem('cart');

                            // Update global cart interface if available
                            if (typeof window.updateCartInterface === 'function') {
                                window.updateCartInterface();
                            }

                            // Redirect to order detail page
                            window.location.href = `/my-account/order-history/order-detail?id=${data.orderId}`;
                        });
                    } else {
                        Swal.fire({
                            title: 'Đặt hàng thất bại',
                            text: data.message || 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại sau.',
                            icon: 'error',
                            confirmButtonColor: '#e74c3c',
                            confirmButtonText: 'Đóng'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi kết nối đến máy chủ. Vui lòng thử lại sau.',
                        icon: 'error',
                        confirmButtonColor: '#e74c3c',
                        confirmButtonText: 'Đóng'
                    });
                });
        });

        // Form input event listeners to clear validation errors
        checkoutForm.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('input', function () {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }

                // Hide error alert if all required fields are filled
                const invalidFields = checkoutForm.querySelectorAll('.is-invalid');
                if (invalidFields.length === 0) {
                    errorAlert.classList.add('d-none');
                }
            });
        });

        // Handle new address button
        const newAddressBtn = document.getElementById('newAddressBtn');
        const cancelNewAddressBtn = document.getElementById('cancelNewAddressBtn');
        const displayAddressSection = document.getElementById('displayAddressSection');
        const newAddressSection = document.getElementById('newAddressSection');

        if (newAddressBtn) {
            newAddressBtn.addEventListener('click', function () {
                displayAddressSection.style.display = 'none';
                newAddressSection.style.display = 'block';
                newAddressBtn.style.display = 'none';

                // Enable and mark the address fields as required
                document.getElementById('new_address').required = true;
                document.getElementById('new_city').required = true;
                document.getElementById('new_district').required = true;
                document.getElementById('new_ward').required = true;
                document.getElementById('address_id').value = '0'; // Reset address ID to force new address
            });
        }

        if (cancelNewAddressBtn) {
            cancelNewAddressBtn.addEventListener('click', function () {
                displayAddressSection.style.display = 'block';
                newAddressSection.style.display = 'none';
                newAddressBtn.style.display = 'block';

                // Disable and remove required from new address fields
                document.getElementById('new_address').required = false;
                document.getElementById('new_city').required = false;
                document.getElementById('new_district').required = false;
                document.getElementById('new_ward').required = false;

                // Reset address ID to original value
                document.getElementById('address_id').value = '<?= htmlspecialchars($user['AddressID'] ?? '0') ?>';
            });
        }

        // Initialize checkout UI and set required fields based on address mode
        updateCheckoutUI();

        // If user has an address, disable required attributes on the new address fields initially
        if (document.getElementById('displayAddressSection')) {
            document.getElementById('new_address').required = false;
            document.getElementById('new_city').required = false;
            document.getElementById('new_district').required = false;
            document.getElementById('new_ward').required = false;
        }
    });
</script>

<style>
    /* Checkout page specific styles */
    .payment-method-item {
        padding: 0.75rem;
        border-radius: 0.25rem;
        transition: all 0.2s;
    }

    .payment-method-item:hover {
        background-color: rgba(231, 76, 60, 0.05);
    }

    .payment-method-item .form-check-input:checked {
        background-color: #e74c3c;
        border-color: #e74c3c;
    }

    .payment-method-item .form-check-label {
        cursor: pointer;
        font-weight: 500;
    }

    /* Form validation */
    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-select.is-invalid {
        padding-right: 4.125rem;
        background-position: right 0.75rem center, center right 2.25rem;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e"), url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    }
</style>