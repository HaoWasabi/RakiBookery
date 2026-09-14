<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thông tin người dùng</h1>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="user-form">
                <input type="hidden" name="id" value="<?= $user['UserID'] ?>">

                <?php
                $isEditMode = true;
                include __DIR__ . '/partials/user-form-fields.php';
                ?>

                <div class="mb-3">
                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="Password" disabled>
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password" disabled>
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu.</div>
                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary me-2" id="cancel-btn" style="display: none;">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-primary" style="display: none;">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                            id="submitSpinner"></span>
                        <i class="fas fa-save mr-2" id="submitIcon"></i> <span id="submitText">Cập nhật</span>
                    </button>
                    <button type="button" class="btn btn-primary" id="edit-btn">
                        <i class="fas fa-edit mr-2"></i> Chỉnh sửa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('user-form');
        const editBtn = document.getElementById('edit-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const submitBtn = document.getElementById('submitBtn');
        const submitSpinner = document.getElementById('submitSpinner');
        const submitText = document.getElementById('submitText');
        const passwordInput = document.getElementById('password');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const phoneRegex = /^(?:\+84|0)([0-9]{9})$/;

        const togglePasswordBtn = document.getElementById('toggle-password');
        const formFields = form.querySelectorAll('input, textarea, select');

        // Store original values
        const originalValues = {};
        formFields.forEach(field => {
            if (field.name && field.name !== 'id' && field.name !== 'Password') {
                originalValues[field.name] = field.value;
            }
        });

        // Initially disable all form fields
        formFields.forEach(field => {
            if (field.name !== 'id') {
                field.disabled = true;
            }
        });

        // Toggle password visibility
        togglePasswordBtn.addEventListener('click', function () {
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

        // Edit button click handler
        editBtn.addEventListener('click', function () {
            formFields.forEach(field => {
                if (field.name !== 'id'
                    // && field.name !== "Email"
                    && field.name !== "Role") {
                    field.disabled = false;
                }
            });
            togglePasswordBtn.disabled = false;

            submitBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
            editBtn.style.display = 'none';
        });

        // Cancel button click handler
        cancelBtn.addEventListener('click', function () {
            formFields.forEach(field => {
                if (originalValues[field.name]) {
                    field.value = originalValues[field.name];
                }

                if (field.name === 'new_password') {
                    field.value = '';
                }

                if (field.name !== 'id') {
                    field.disabled = true;
                }

                field.classList.remove('is-invalid');
            });
            togglePasswordBtn.disabled = true;

            // Hide save and cancel buttons, show edit button
            submitBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            editBtn.style.display = 'block';
        });

        const disableFields = () => {
            formFields.forEach(field => {
                if (field.name !== 'id') {
                    field.disabled = true;
                }
            });
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

            // Validate password if provided
            if (passwordInput.value && passwordInput.value.length < 6) {
                showInvalidFeedback(passwordInput, 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }

            if (!isValid) {
                return;
            }

            showConfirmation(
                'Xác nhận cập nhật',
                'Bạn có chắc chắn muốn cập nhật thông tin người dùng này?',
                function () {
                    // Show loading state
                    submitBtn.disabled = true;
                    submitSpinner.classList.remove('d-none');
                    submitText.textContent = 'Đang xử lý...';

                    const formData = new FormData(form);

                    // Trim all text inputs and textareas before submission
                    formFields.forEach(field => {
                        if (field.name && (field.type === 'text' || field.tagName.toLowerCase() === 'textarea')) {
                            formData.set(field.name, field.value.trim());
                        }
                    });

                    fetch('/api/users/update', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Hide loading state
                            submitBtn.disabled = false;
                            submitSpinner.classList.add('d-none');
                            submitText.textContent = 'Cập nhật';

                            if (data.success) {
                                showSuccess('Thành công', data.message);

                                // Update original values
                                formFields.forEach(field => {
                                    if (field.name && field.name !== 'id' && field.name !== 'new_password') {
                                        originalValues[field.name] = field.value;
                                    }
                                });

                                // Clear password field
                                passwordInput.value = '';

                                disableFields();

                                // Hide save and cancel buttons, show edit button
                                submitBtn.style.display = 'none';
                                cancelBtn.style.display = 'none';
                                editBtn.style.display = 'block';
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
                            submitText.textContent = 'Cập nhật';

                            showError('Lỗi', 'Đã xảy ra lỗi khi xử lý yêu cầu. Vui lòng thử lại sau.');
                            console.error('Error:', error);
                        });
                }
            );
        });
        /* 
                function showConfirmation(title, message, confirmCallback) {
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
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: title,
                            text: message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        alert(message);
                    }
                } */
    });
</script>