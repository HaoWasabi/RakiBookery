<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa thể loại</h1>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="category-form">
                <input type="hidden" name="id" value="<?= $category['CategoryID'] ?>">

                <?php
                $isEditMode = true;
                include __DIR__ . '/partials/category-form-fields.php';
                ?>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2" id="cancel-btn" style="display: none;">
                        <!-- onclick="window.location.href='/admin/categories'"> -->
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="save-btn" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="loading-spinner" role="status"
                            aria-hidden="true"></span>
                        <i class="fas fa-save mr-2" id="save-icon"></i> Cập nhật
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
        const form = document.getElementById('category-form');
        const loadingSpinner = document.getElementById('loading-spinner');
        const editBtn = document.getElementById('edit-btn');
        const saveBtn = document.getElementById('save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const saveIcon = document.getElementById('save-icon');
        const nameInput = document.getElementById('name');
        const originalName = '<?= $category['Name'] ?>';
        const originalDescription = '<?= $category['Description'] ?>';
        const formFields = form.querySelectorAll('input, textarea, select');

        // Store original values
        const originalValues = {};
        formFields.forEach(field => {
            originalValues[field.name] = field.value;
        });

        formFields.forEach(field => {
            if (field.name !== 'id') {
                field.disabled = true;
            }
        });

        // Edit button click handler
        editBtn.addEventListener('click', function () {
            formFields.forEach(field => {
                field.disabled = false;
            });

            saveBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
            editBtn.style.display = 'none';
        });

        // Cancel button click handler
        cancelBtn.addEventListener('click', function () {
            formFields.forEach(field => {
                if (originalValues[field.name]) {
                    field.value = originalValues[field.name];
                }
                field.disabled = true;
            });

            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            editBtn.style.display = 'block';
        });

        // Capture form submission and process
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            let isValid = true;

            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    showInvalidFeedback(field, 'Trường này không được để trống');
                    isValid = false;
                }
            });

            if (!isValid) {
                return;
            }

            showConfirmation('Xác nhận thay đổi', 'Bạn có chắc chắn muốn lưu thay đổi cho thể loại này không?', function () {
                // Show loading
                saveBtn.disabled = true;
                loadingSpinner.classList.remove('d-none');
                saveIcon.classList.add('d-none');

                const formData = new FormData(form);

                // Trim all text inputs and textareas before submission
                formFields.forEach(field => {
                    if (field.name && (field.type === 'text' || field.tagName.toLowerCase() === 'textarea')) {
                        formData.set(field.name, field.value.trim());
                    }
                });

                fetch('/api/categories/update', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        // Hide loading
                        saveBtn.disabled = false;
                        loadingSpinner.classList.add('d-none');
                        saveIcon.classList.remove('d-none');

                        if (data.success) {
                            showSuccess('Thành công', data.message);

                            /* setTimeout(() => {
                                window.location.href = data.redirect || '/admin/categories';
                            }, 1500); */

                            // Update original values with new values
                            formFields.forEach(field => {
                                if (field.name && field.name !== 'id') {
                                    originalValues[field.name] = field.value;
                                }
                            });

                            // Switch back to view mode
                            formFields.forEach(field => {
                                if (field.name !== 'id') {
                                    field.disabled = true;
                                }
                            });

                            // Hide save and cancel buttons, show edit button
                            saveBtn.style.display = 'none';
                            cancelBtn.style.display = 'none';
                            editBtn.style.display = 'block';
                        } else {
                            showError('Lỗi', data.message);
                        }
                    })
                    .catch(error => {
                        // Hide loading
                        saveBtn.disabled = false;
                        loadingSpinner.classList.add('d-none');
                        saveIcon.classList.remove('d-none');

                        showError('Lỗi', 'Đã xảy ra lỗi, vui lòng thử lại sau.');
                        console.error('Error:', error);
                    });
            });
        });
    });
</script>