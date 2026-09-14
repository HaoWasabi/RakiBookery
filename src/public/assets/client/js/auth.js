import { showSweetAlert } from "./util.js";

$(document).ready(() => {
    // Re-initialize AOS when modal opens
    $('#authModal').on('shown.bs.modal', () => {
        AOS.refresh();
    });

    // Khi modal mở, hiển thị tab tương ứng
    document.querySelectorAll('[data-auth-action]').forEach(link => {
        link.addEventListener('click', function () {
            const tabToActivate = this.getAttribute('data-auth-action') === 'login' ? '#login-tab' : '#register-tab';
            const tabTrigger = document.querySelector(tabToActivate);
            if (tabTrigger) {
                const tabInstance = new bootstrap.Tab(tabTrigger);
                tabInstance.show();
            }
        });
    });


    // Theo dõi tương tác với form
    let loginFormInteracted = false;
    let registerFormInteracted = false;

    // Helper function to show error message
    const showError = (input, message) => {
        $(input).addClass('is-invalid').removeClass('is-valid');

        // Xử lý thêm cho input group
        if ($(input).closest('.input-group').length > 0) {
            const inputGroup = $(input).closest('.input-group');
            const existingFeedback = inputGroup.siblings('.invalid-feedback');

            if (existingFeedback.length > 0) {
                existingFeedback.text(message);
                // Chỉ hiển thị nếu form đã tương tác
                if (($(input).closest('#loginForm').length > 0 && loginFormInteracted) ||
                    ($(input).closest('#registerForm').length > 0 && registerFormInteracted)) {
                    existingFeedback.show();
                }
            } else {
                // Chỉ thêm feedback mới nếu form đã tương tác
                if (($(input).closest('#loginForm').length > 0 && loginFormInteracted) ||
                    ($(input).closest('#registerForm').length > 0 && registerFormInteracted)) {
                    inputGroup.after(`<div class="invalid-feedback">${message}</div>`);
                }
            }
        } else {
            // Xử lý cho input thông thường
            const feedbackElement = $(input).siblings('.invalid-feedback');
            if (feedbackElement.length > 0) {
                feedbackElement.text(message);
                // Chỉ hiển thị nếu form đã tương tác
                if (($(input).closest('#loginForm').length > 0 && loginFormInteracted) ||
                    ($(input).closest('#registerForm').length > 0 && registerFormInteracted)) {
                    feedbackElement.show();
                }
            } else {
                // Chỉ thêm feedback mới nếu form đã tương tác
                if (($(input).closest('#loginForm').length > 0 && loginFormInteracted) ||
                    ($(input).closest('#registerForm').length > 0 && registerFormInteracted)) {
                    $(input).after(`<div class="invalid-feedback">${message}</div>`);
                }
            }
        }

        // Xử lý đặc biệt cho checkbox
        if ($(input).hasClass('form-check-input')) {
            const checkContainer = $(input).closest('.form-check');
            const existingFeedback = checkContainer.find('.invalid-feedback');

            if (existingFeedback.length > 0) {
                existingFeedback.text(message);
                // Chỉ hiển thị nếu form đã tương tác
                if (($(input).closest('#loginForm').length > 0 && loginFormInteracted) ||
                    ($(input).closest('#registerForm').length > 0 && registerFormInteracted)) {
                    existingFeedback.show();
                }
            } else {
                // Chỉ thêm feedback mới nếu form đã tương tác
                if (($(input).closest('#loginForm').length > 0 && loginFormInteracted) ||
                    ($(input).closest('#registerForm').length > 0 && registerFormInteracted)) {
                    checkContainer.append(`<div class="invalid-feedback">${message}</div>`);
                }
            }
        }
    };

    // Helper function to show success state
    const showSuccess = (input) => {
        $(input).removeClass('is-invalid').addClass('is-valid');
        $(input).siblings('.invalid-feedback').hide();
        $(input).closest('.input-group').siblings('.invalid-feedback').hide();
        $(input).closest('.form-check').find('.invalid-feedback').hide();
    };

    // Helper function to clear validation state
    const clearValidation = (input) => {
        $(input).removeClass('is-invalid is-valid');
        $(input).siblings('.invalid-feedback').hide();
        $(input).closest('.input-group').siblings('.invalid-feedback').hide();
        $(input).closest('.form-check').find('.invalid-feedback').hide();
    };

    // Focus on first field when modal opens
    $('#authModal').on('shown.bs.modal', function () {
        // Ẩn tất cả thông báo lỗi khi modal mở lên
        $('.invalid-feedback').hide();

        if ($('#login-tab').hasClass('active')) {
            $('#loginEmail').focus();
            // Chỉ validate nếu form đã được tương tác trước đó
            if (loginFormInteracted) {
                validateLoginForm();
            } else {
                // Xóa trạng thái validation nếu không có tương tác
                $('#loginForm .is-invalid, #loginForm .is-valid').removeClass('is-invalid is-valid');
                $('#loginSubmitBtn').prop('disabled', true);
            }
        } else {
            $('#registerFullName').focus();
            // Chỉ validate nếu form đã được tương tác trước đó
            if (registerFormInteracted) {
                validateRegisterForm();
            } else {
                // Xóa trạng thái validation nếu không có tương tác
                $('#registerForm .is-invalid, #registerForm .is-valid').removeClass('is-invalid is-valid');
                $('#registerSubmitBtn').prop('disabled', true);
            }
        }
    });

    // Focus on first field when tab changes
    $('#login-tab').on('shown.bs.tab', function () {
        $('#loginEmail').focus();
        // Ẩn tất cả thông báo lỗi khi chuyển tab
        $('#login-content .invalid-feedback').hide();

        // Chỉ validate nếu form đã được tương tác trước đó
        if (loginFormInteracted) {
            validateLoginForm();
        } else {
            // Xóa trạng thái validation nếu không có tương tác
            $('#loginForm .is-invalid, #loginForm .is-valid').removeClass('is-invalid is-valid');
            $('#loginSubmitBtn').prop('disabled', true);
        }
    });

    $('#register-tab').on('shown.bs.tab', function () {
        $('#registerFullName').focus();
        // Ẩn tất cả thông báo lỗi khi chuyển tab
        $('#register-content .invalid-feedback').hide();

        // Chỉ validate nếu form đã được tương tác trước đó
        if (registerFormInteracted) {
            validateRegisterForm();
        } else {
            // Xóa trạng thái validation nếu không có tương tác
            $('#registerForm .is-invalid, #registerForm .is-valid').removeClass('is-invalid is-valid');
            $('#registerSubmitBtn').prop('disabled', true);
        }
    });

    // Password visibility toggle
    $(document).on('click', '.password-toggle', function () {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    const emailRegex = /^(?!.*\.\.)[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const phoneRegex = /^(?:\+84|0)([0-9]{9})$/;

    // Field validation rules
    const validationRules = {
        // Login form fields
        loginEmail: {
            validators: [
                {
                    test: value => !!value,
                    message: 'Vui lòng nhập email.'
                },
                {
                    test: value => emailRegex.test(value),
                    message: 'Email không hợp lệ. Vui lòng kiểm tra lại.'
                }
            ]
        },
        loginPassword: {
            validators: [
                {
                    test: value => !!value,
                    message: 'Vui lòng nhập mật khẩu.'
                }
            ]
        },
        // Register form fields
        registerFullName: {
            validators: [
                {
                    test: value => !!value,
                    message: 'Vui lòng nhập họ và tên.'
                },
                {
                    test: value => value.trim() !== '',
                    message: 'Họ và tên không thể chỉ chứa khoảng trắng.'
                }
            ]
        },
        registerEmail: {
            validators: [
                {
                    test: value => !!value,
                    message: 'Vui lòng nhập email.'
                },
                {
                    test: value => emailRegex.test(value),
                    message: 'Email không hợp lệ. Vui lòng kiểm tra lại.'
                }
            ]
        },
        registerPhone: {
            validators: [
                {
                    test: value => !!value,
                    message: 'Vui lòng nhập số điện thoại.'
                },
                {
                    test: value => phoneRegex.test(value),
                    message: 'Vui lòng nhập đúng định dạng số điện thoại.(10 số bắt đầu bằng +84 hoặc 0)'
                }
            ]
        },
        registerPassword: {
            validators: [
                {
                    test: value => !!value,
                    message: 'Vui lòng nhập mật khẩu.'
                },
                {
                    test: value => value.length >= 6,
                    message: 'Mật khẩu phải có ít nhất 6 ký tự.'
                }
            ]
        },
        confirmPassword: {
            validators: [
                {
                    test: (value, formData) => !!value,
                    message: 'Vui lòng xác nhận mật khẩu.'
                },
                {
                    test: (value, formData) => value === formData.registerPassword,
                    message: 'Mật khẩu xác nhận không khớp.'
                }
            ]
        },
        agreeTerms: {
            validators: [
                {
                    test: value => value === true,
                    message: 'Bạn phải đồng ý với điều khoản sử dụng.'
                }
            ]
        }
    };

    // Validate single field
    const validateField = (fieldId, formData) => {
        const field = $('#' + fieldId);
        const value = field.val();
        const isCheckbox = field.attr('type') === 'checkbox';
        const fieldValue = isCheckbox ? field.prop('checked') : value;
        const rules = validationRules[fieldId];
        const isLoginForm = field.closest('#loginForm').length > 0;
        const isRegisterForm = field.closest('#registerForm').length > 0;
        const hasInteracted = (isLoginForm && loginFormInteracted) || (isRegisterForm && registerFormInteracted);

        if (!rules) return true; // No rules for this field

        for (const validator of rules.validators) {
            if (!validator.test(fieldValue, formData)) {
                if (hasInteracted) {
                    showError(field, validator.message);
                }
                return false;
            }
        }

        if (hasInteracted) {
            showSuccess(field);
        }
        return true;
    };

    // Validate login form
    const validateLoginForm = () => {
        let isValid = true;
        const formData = {
            loginEmail: $('#loginEmail').val(),
            loginPassword: $('#loginPassword').val()
        };

        // Validate each field
        if (!validateField('loginEmail', formData)) isValid = false;
        if (!validateField('loginPassword', formData)) isValid = false;

        // Enable/disable submit button
        $('#loginSubmitBtn').prop('disabled', !isValid);

        return isValid;
    };

    // Validate register form
    const validateRegisterForm = () => {
        let isValid = true;
        const formData = {
            registerFullName: $('#registerFullName').val().trim(),
            registerEmail: $('#registerEmail').val().trim(),
            registerPhone: $('#registerPhone').val(),
            registerPassword: $('#registerPassword').val(),
            confirmPassword: $('#confirmPassword').val(),
            agreeTerms: $('#agreeTerms').prop('checked')
        };

        // Validate each field
        if (!validateField('registerFullName', formData)) isValid = false;
        if (!validateField('registerEmail', formData)) isValid = false;
        if (!validateField('registerPhone', formData)) isValid = false;
        if (!validateField('registerPassword', formData)) isValid = false;
        if (!validateField('confirmPassword', formData)) isValid = false;
        if (!validateField('agreeTerms', formData)) isValid = false;

        // Enable/disable submit button
        $('#registerSubmitBtn').prop('disabled', !isValid);

        return isValid;
    };

    // Event listeners for login form
    $('#loginEmail, #loginPassword').on('input', function () {
        loginFormInteracted = true;
        validateLoginForm();
    });

    // Event listeners for register form
    $('#registerFullName, #registerEmail, #registerPhone, #registerPassword, #confirmPassword').on('input', function () {
        registerFormInteracted = true;
        validateRegisterForm();
    });

    $('#agreeTerms').on('change', function () {
        registerFormInteracted = true;
        validateRegisterForm();
    });

    // Form validation với hiệu ứng pulse
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        if (!validateLoginForm()) {
            // Add shake animation for invalid fields
            $('.is-invalid').closest('.mb-4').addClass('shake');
            setTimeout(() => {
                $('.shake').removeClass('shake');
            }, 600);
            return false;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.addClass('btn-pulse-shadow');
        submitBtn.prop('disabled', true);

        // Show loading animation
        const btnText = submitBtn.find('.btn-text');
        const btnIcon = submitBtn.find('.btn-icon');
        const originalText = btnText.text();
        const originalIcon = btnIcon.html();

        btnText.text('');
        btnIcon.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/auth/login',
            type: 'POST',
            data: {
                email: $('#loginEmail').val(),
                password: $('#loginPassword').val()
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Đóng modal
                    $('#authModal').modal('hide');

                    // Check if there's a cart cookie to sync
                    if (response.has_cart_cookie) {
                        // Fetch cart data from cookie
                        $.ajax({
                            url: '/auth/get-cart-cookie',
                            type: 'GET',
                            dataType: 'json',
                            success: function (cookieData) {
                                if (cookieData.success && cookieData.cart) {
                                    // Update localStorage cart with cookie data
                                    localStorage.setItem('cart', JSON.stringify(cookieData.cart));

                                    // Update cart interface if function exists
                                    if (typeof window.updateCartInterface === 'function') {
                                        window.updateCartInterface();
                                    }
                                }
                                // Reload page after cart sync
                                window.location.reload();
                            },
                            error: function () {
                                // Still reload page even if cart sync fails
                                window.location.reload();
                            }
                        });
                    } else {
                        // No cart cookie, just reload the page
                        window.location.reload();
                    }
                } else {

                    showSweetAlert(response.message, {
                        icon: 'error',
                        title: 'Đăng nhập thất bại',
                        confirmButtonText: 'Thử lại'
                    });

                    // Reset button
                    btnText.text(originalText);
                    btnIcon.html(originalIcon);
                    submitBtn.removeClass('btn-pulse-shadow');
                    submitBtn.prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                showSweetAlert('Có lỗi xảy ra khi xử lý yêu cầu', {
                    icon: 'error',
                    title: 'Lỗi hệ thống',
                    confirmButtonText: 'Đã hiểu'
                });

                // Reset button
                btnText.text(originalText);
                btnIcon.html(originalIcon);
                submitBtn.removeClass('btn-pulse-shadow');
                submitBtn.prop('disabled', false);
            }
        });
    });

    $('#registerForm').on('submit', function (e) {
        e.preventDefault();

        if (!validateRegisterForm()) {
            // Add shake animation for invalid fields
            $('.is-invalid').closest('.mb-4, .form-check').addClass('shake');
            setTimeout(() => {
                $('.shake').removeClass('shake');
            }, 600);
            return false;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.addClass('btn-pulse-shadow');
        submitBtn.prop('disabled', true);

        // Show loading animation
        const btnText = submitBtn.find('.btn-text');
        const btnIcon = submitBtn.find('.btn-icon');
        const originalText = btnText.text();
        const originalIcon = btnIcon.html();

        btnText.text('');
        btnIcon.html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/auth/register',
            type: 'POST',
            data: {
                fullName: $('#registerFullName').val(),
                email: $('#registerEmail').val(),
                phone: $('#registerPhone').val(),
                password: $('#registerPassword').val()
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Đóng modal
                    $('#authModal').modal('hide');

                    showSweetAlert(response.message, {
                        icon: 'success',
                        title: 'Đăng ký thành công',
                        confirmButtonText: 'Đã hiểu',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {

                    showSweetAlert(response.message, {
                        icon: 'error',
                        title: 'Đăng ký thất bại',
                        confirmButtonText: 'Đã hiểu'
                    });

                    // Reset button
                    btnText.text(originalText);
                    btnIcon.html(originalIcon);
                    submitBtn.removeClass('btn-pulse-shadow');
                    submitBtn.prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                showSweetAlert('Có lỗi xảy ra khi xử lý yêu cầu', {
                    icon: 'error',
                    title: 'Lỗi hệ thống'
                });

                // Reset button
                btnText.text(originalText);
                btnIcon.html(originalIcon);
                submitBtn.removeClass('btn-pulse-shadow');
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Reset validation state when modal is hidden
    $('#authModal').on('hidden.bs.modal', function () {
        // Remove all dynamically added error messages
        $('.invalid-feedback:not([id])').remove();
        // Hide all other feedback messages
        $('.invalid-feedback').hide();
        // Remove validation classes
        $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
        // Không reset biến tương tác để giữ trạng thái giữa các lần mở form
    });

});