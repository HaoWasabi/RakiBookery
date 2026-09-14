<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm người dùng mới</h1>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="user-form">
                <?php
                $isEditMode = false;
                include __DIR__ . '/partials/user-form-fields.php';
                ?>

                <hr>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/admin/users" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                            id="submitSpinner"></span>
                        <span id="submitText">Thêm người dùng</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('user-form');
        const submitBtn = document.getElementById('submitBtn');
        const submitSpinner = document.getElementById('submitSpinner');
        const submitText = document.getElementById('submitText');
        const passwordInput = document.getElementById('password');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        document.getElementById('name').focus();

        const phoneRegex = /^(?:\+84|0)([0-9]{9})$/;

        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function () {
            togglePasswordVisibility(passwordInput, this);
        });

        function togglePasswordVisibility(input, button) {
            if (input.type === 'password') {
                input.type = 'text';
                button.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                button.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }

        // Form validation and submission
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Reset previous validation errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            let isValid = true;

            // Check required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    showInvalidFeedback(field, 'Trường này không được để trống');
                    isValid = false;
                }
            });

            if (phoneInput.value && !phoneRegex.test(phoneInput.value)) {
                showInvalidFeedback(phoneInput, 'Số điện thoại không hợp lệ');
                isValid = false;
            }

            // Validate password length
            if (passwordInput.value.length < 6) {
                showInvalidFeedback(passwordInput, 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }

            // Validate email format
            const emailRegex = /^(?!.*\.\.)[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (emailInput.value && !emailRegex.test(emailInput.value)) {
                showInvalidFeedback(emailInput, 'Email không hợp lệ');
                isValid = false;
            }

            if (!isValid) {
                return;
            }

            // Show confirmation dialog
            showConfirmation(
                'Xác nhận thêm mới',
                'Bạn có chắc chắn muốn thêm người dùng mới này?',
                function () {
                    // Show loading state
                    submitBtn.disabled = true;
                    submitSpinner.classList.remove('d-none');
                    submitText.textContent = 'Đang xử lý...';

                    const formData = new FormData(form);

                    // Trim all text inputs and textareas before submission
                    const formFields = form.querySelectorAll('input, textarea');
                    formFields.forEach(field => {
                        if (field.name && (field.type === 'text' || field.tagName.toLowerCase() === 'textarea')) {
                            formData.set(field.name, field.value.trim());
                        }
                    });

                    fetch('/api/users/add', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Hide loading state
                            submitBtn.disabled = false;
                            submitSpinner.classList.add('d-none');
                            submitText.textContent = 'Thêm người dùng';

                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonText: 'Quay về danh sách',
                                    confirmButtonColor: '#3085d6'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '/admin/users';
                                    }
                                });
                            } else {
                                if (data.error === 'phone_exists') {
                                    showInvalidFeedback(phoneInput, 'Số điện thoại này đã được sử dụng bởi người dùng khác');
                                } else if (data.error === 'email_exists') {
                                    showInvalidFeedback(emailInput, 'Email này đã được sử dụng bởi người dùng khác');
                                } else {
                                    showError('Lỗi', data.message);
                                }
                            }
                        })
                        .catch(error => {
                            // Hide loading state
                            submitBtn.disabled = false;
                            submitSpinner.classList.add('d-none');
                            submitText.textContent = 'Thêm người dùng';

                            showError('Lỗi', 'Đã xảy ra lỗi khi xử lý yêu cầu. Vui lòng thử lại sau.');
                            console.error('Error:', error);
                        });
                }
            );
        });

        /*         function showError(element, message) {
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
                } */

        function showConfirmation(title, message, confirmCallback) {
            // Use SweetAlert2 if available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmCallback();
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm(message)) {
                    confirmCallback();
                }
            }
        }

        function showSuccess(title, message) {
            // Use SweetAlert2 if available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else {
                // Fallback to alert
                alert(message);
            }
        }
    });
</script>