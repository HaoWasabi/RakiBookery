<!-- Add Product Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm sản phẩm mới</h1>
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="product-form" enctype="multipart/form-data">
                <?php
                // Set empty book array for new product
                $book = [];
                $isEditMode = false;

                // Include the form fields partial
                include __DIR__ . '/partials/product-form-fields.php';
                ?>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-secondary me-2"
                        onclick="window.location.href='/admin/products'">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="loading-spinner" role="status"
                            aria-hidden="true"></span>
                        <i class="fas fa-save mr-2" id="save-icon"></i> Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('product-form');
        const saveBtn = document.getElementById('save-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const saveIcon = document.getElementById('save-icon');

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

            // Show confirmation dialog
            showConfirmation('Xác nhận thêm mới', 'Bạn có chắc chắn muốn thêm sản phẩm mới này không?', function () {
                // Show loading state
                saveBtn.disabled = true;
                loadingSpinner.classList.remove('d-none');
                saveIcon.classList.add('d-none');

                const formData = new FormData(form);

                // Trim all text inputs and textareas before submission
                const formFields = form.querySelectorAll('input, textarea');
                formFields.forEach(field => {
                    if (field.name && (field.type === 'text' || field.tagName.toLowerCase() === 'textarea')) {
                        formData.set(field.name, field.value.trim());
                    }
                });

                // Handle file input separately
                const imageInput = document.getElementById('image');
                if (imageInput.files.length > 0) {
                    formData.set('image', imageInput.files[0]);
                }

                fetch('/api/products/add', {
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
                            Swal.fire({
                                title: 'Thành công',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'Quay về danh sách',
                                confirmButtonColor: '#3085d6'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = data.redirect || '/admin/products';
                                }
                            });
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