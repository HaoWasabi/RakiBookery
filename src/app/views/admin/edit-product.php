<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thông tin sản phẩm</h1>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="product-form" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $book['BookID'] ?>">

                <?php
                $isEditMode = true;
                include __DIR__ . '/partials/product-form-fields.php';
                ?>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2" id="cancel-btn" style="display: none;">
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
        const form = document.getElementById('product-form');
        const editBtn = document.getElementById('edit-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const saveBtn = document.getElementById('save-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const saveIcon = document.getElementById('save-icon');
        const formFields = form.querySelectorAll('input, textarea, select');

        // Store original values to check for changes
        const originalValues = {};
        formFields.forEach(field => {
            if (field.name && field.name !== 'BookID') {
                originalValues[field.name] = field.value;
            }
        });

        // Initially disable all form fields
        formFields.forEach(field => {
            if (field.name !== 'BookID') {
                field.disabled = true;
            }
        });

        // Edit button click handler
        editBtn.addEventListener('click', function () {
            formFields.forEach(field => {
                if (field.name !== 'BookID'
                    // && field.name !== 'Name'
                    // && field.name !== 'Price'
                    ) 
                    {
                    field.disabled = false;
                }
            });

            saveBtn.style.display = 'block';
            cancelBtn.style.display = 'block';
            editBtn.style.display = 'none';
        });

        // Cancel button click handler
        cancelBtn.addEventListener('click', function () {
            document.getElementById('image').value = '';
            restorePreviewState(getInitialPreviewState('<?= $book['ImageURL'] ?>'));

            formFields.forEach(field => {
                if (originalValues[field.name]) {
                    field.value = originalValues[field.name];
                }

                if (field.name !== 'BookID') {
                    field.disabled = true;
                }

                field.classList.remove('is-invalid');
            });

            // Hide save and cancel buttons, show edit button
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            editBtn.style.display = 'block';
        });

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

            showConfirmation('Xác nhận thay đổi', 'Bạn có chắc chắn muốn lưu thay đổi cho sản phẩm này không?', function () {
                saveBtn.disabled = true;
                loadingSpinner.classList.remove('d-none');
                saveIcon.classList.add('d-none');

                // Create custom FormData to include disabled fields
                const formData = new FormData();

                // Add all form fields including disabled ones and trim text fields
                formFields.forEach(field => {
                    if (field.name) {
                        if (field.type === 'text' || field.tagName.toLowerCase() === 'textarea') {
                            formData.append(field.name, field.value.trim());
                        } else {
                            formData.append(field.name, field.value);
                        }
                    }
                });

                // Handle file input separately
                const imageInput = document.getElementById('image');
                if (imageInput.files.length > 0) {
                    formData.append('image', imageInput.files[0]);
                } else {
                    // Ensure ImageURL from hidden field is included
                    const imageUrl = document.querySelector('input[name="ImageURL"]');
                    if (imageUrl && imageUrl.value) {
                        formData.append('ImageURL', imageUrl.value);
                    }
                }

                fetch('/api/products/update', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        saveBtn.disabled = false;
                        loadingSpinner.classList.add('d-none');
                        saveIcon.classList.remove('d-none');

                        if (data.success) {
                            showSuccess('Thành công', data.message);

                            // Update original values with new values
                            formFields.forEach(field => {
                                if (field.name && field.name !== 'BookID') {
                                    originalValues[field.name] = field.value;
                                }
                            });

                            // Switch back to view mode
                            formFields.forEach(field => {
                                if (field.name !== 'BookID') {
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