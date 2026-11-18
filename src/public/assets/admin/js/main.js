$(document).ready(function () {
    // ── Interceptor toàn cục: redirect về login khi session hết hạn (HTTP 401) ──
    const _origFetch = window.fetch;
    window.fetch = function (...args) {
        return _origFetch.apply(this, args).then(function (response) {
            if (response.status === 401) {
                // Clone để đọc body mà không làm hỏng response gốc
                response.clone().json().then(function (data) {
                    Swal.fire({
                        title: 'Phiên đăng nhập hết hạn',
                        text: 'Vui lòng đăng nhập lại để tiếp tục.',
                        icon: 'warning',
                        confirmButtonColor: '#e74c3c',
                        confirmButtonText: 'Đăng nhập lại'
                    }).then(function () {
                        window.location.href = '/admin/login';
                    });
                }).catch(function () {
                    window.location.href = '/admin/login';
                });
            }
            return response;
        });
    };

    // Interceptor cho jQuery AJAX (DataTables và các nơi dùng $.ajax/$.get/$.post)
    $(document).ajaxComplete(function (event, xhr) {
        if (xhr.status === 401) {
            Swal.fire({
                title: 'Phiên đăng nhập hết hạn',
                text: 'Vui lòng đăng nhập lại để tiếp tục.',
                icon: 'warning',
                confirmButtonColor: '#e74c3c',
                confirmButtonText: 'Đăng nhập lại'
            }).then(function () {
                window.location.href = '/admin/login';
            });
        }
    });

    // Close sidebar on mobile when clicking outside
    $(document).on('click touchstart', function (e) {
        if ($(window).width() < 768) {
            if (!$(e.target).closest('.sidebar').length &&
                !$(e.target).closest('.sidebar-toggle-btn').length &&
                $('body').hasClass('sidebar-collapsed')) {
                $('body').removeClass('sidebar-collapsed');
            }
        }
    });

    window.showSuccess = function (title, message) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'success',
            confirmButtonColor: '#e74c3c'
        });
    };

    window.showError = function (title, message) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'error',
            confirmButtonColor: '#e74c3c'
        });
    };

    window.showConfirmation = function (title, message, callback) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    };


    window.showInvalidFeedback = function (element, message) {
        if (typeof element === 'string') {
            // Use SweetAlert2 if available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: element,
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                // Fallback to alert
                alert(message);
            }
            return;
        }

        // Field-specific error
        element.classList.add('is-invalid');

        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;

        element.parentNode.appendChild(feedback);
    }

    // Xử lý sự kiện đăng xuất
    $(document).on('click', '#logoutBtn, #navbar-logoutBtn', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Đăng xuất',
            text: 'Bạn có chắc chắn muốn đăng xuất?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Đăng xuất',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/admin/logout';
            }
        });
    });
});