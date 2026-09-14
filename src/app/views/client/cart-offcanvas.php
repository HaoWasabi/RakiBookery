<!-- Cart Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="cartOffcanvasLabel">Sản phẩm trong giỏ hàng:</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div id="cartItems">
            <!-- Cart items will be loaded here -->
        </div>
        <div id="cartSummary" class="p-3 border-top mt-auto">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span>Tổng số mục:</span>
                <div>
                    <span id="cartTotalItems">0 sản phẩm</span> cho <span id="cartTotalPrice"
                        class="text-danger fw-bold">0 đ</span>
                </div>
            </div>
            <div class="d-grid gap-2">
                <a href="/cart" class="btn btn-view-cart">
                    <span class="btn-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span class="btn-text">Xem giỏ hàng</span>
                </a>
                <a href="#" class="btn btn-checkout">
                    <span class="btn-icon"><i class="fas fa-credit-card"></i></span>
                    <span class="btn-text">Thanh toán</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Empty Cart Template (hidden) -->
<template id="emptyCartTemplate">
    <div class="empty-cart-container">
        <img src="../../img/empty-cart.png" alt="Giỏ hàng trống" class="empty-cart-image">
        <div class="empty-cart-message">Giỏ hàng của bạn đang trống</div>
        <div class="empty-cart-submessage">Hãy thêm sản phẩm vào giỏ hàng của bạn</div>
        <button onclick="if(window.location.pathname !== '/shop') window.location.href='/shop'"
            class="empty-cart-button" data-bs-dismiss="offcanvas">Tiếp tục
            mua sắm</button>
    </div>
</template>

<script type="module">
    import {
        showSweetAlert
    } from '/assets/client/js/util.js';
    document.querySelector('.btn-checkout').addEventListener('click', () => {
        <?php if (!isset($_SESSION['UserID'])): ?>
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
            // Redirect to checkout page
            window.location.href = '/cart/checkout';
        <?php endif; ?>
    });
</script>