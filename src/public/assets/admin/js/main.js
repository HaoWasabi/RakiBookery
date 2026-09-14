$(document).ready(function () {
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