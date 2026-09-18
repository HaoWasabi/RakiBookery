<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa khuyến mãi</h1>
        <a href="/admin/promos" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="promo-form">
                <input type="hidden" name="id" value="<?= $promo['PromoID'] ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Name" class="form-label">Tên mã khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="Name" name="Name"
                                value="<?= htmlspecialchars($promo['Name']) ?>" required disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Discounted" class="form-label">Phần trăm giảm giá (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="Discounted" name="Discounted"
                                    value="<?= htmlspecialchars($promo['Discounted']) ?>"
                                    min="1" max="100" step="1" required disabled>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="DateCreated" class="form-label">Ngày tạo</label>
                            <input type="date" class="form-control" id="DateCreated" name="DateCreated"
                                value="<?= htmlspecialchars($promo['DateCreated'] ?? '') ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="Status" name="Status" disabled>
                                <option value="1" <?= ($promo['Status'] ?? 1) == 1 ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= ($promo['Status'] ?? 1) == 0 ? 'selected' : '' ?>>Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-secondary me-2" id="cancel-btn" style="display:none;">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary me-2" id="save-btn" style="display:none;">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="loading-spinner"
                            role="status" aria-hidden="true"></span>
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
        const form = document.getElementById('promo-form');
        const editBtn = document.getElementById('edit-btn');
        const saveBtn = document.getElementById('save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const saveIcon = document.getElementById('save-icon');
        const formFields = form.querySelectorAll('input:not([name="id"]), select');

        // Store original values for cancel
        const originalValues = {};
        formFields.forEach(field => { originalValues[field.name] = field.value; });

        editBtn.addEventListener('click', function () {
            formFields.forEach(field => { field.disabled = false; });
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
        });

        cancelBtn.addEventListener('click', function () {
            formFields.forEach(field => {
                field.value = originalValues[field.name] ?? field.value;
                field.disabled = true;
            });
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            editBtn.style.display = 'inline-block';
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    showInvalidFeedback(field, 'Trường này không được để trống');
                    isValid = false;
                }
            });

            const discounted = parseFloat(document.getElementById('Discounted').value);
            if (isNaN(discounted) || discounted < 1 || discounted > 100) {
                showInvalidFeedback(document.getElementById('Discounted'), 'Phần trăm giảm giá phải từ 1 đến 100');
                isValid = false;
            }

            if (!isValid) return;

            showConfirmation('Xác nhận thay đổi', 'Bạn có chắc chắn muốn lưu thay đổi cho khuyến mãi này không?', function () {
                saveBtn.disabled = true;
                loadingSpinner.classList.remove('d-none');
                saveIcon.classList.add('d-none');

                const formData = new FormData(form);
                const jsonData = Object.fromEntries(formData.entries());

                fetch('/admin/promos/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(jsonData)
                })
                .then(r => r.json())
                .then(data => {
                    saveBtn.disabled = false;
                    loadingSpinner.classList.add('d-none');
                    saveIcon.classList.remove('d-none');

                    if (data.success) {
                        showSuccess('Thành công', data.message);

                        // Update original values and switch back to view mode
                        formFields.forEach(field => {
                            originalValues[field.name] = field.value;
                            field.disabled = true;
                        });
                        saveBtn.style.display = 'none';
                        cancelBtn.style.display = 'none';
                        editBtn.style.display = 'inline-block';
                    } else {
                        showError('Lỗi', data.message);
                    }
                })
                .catch(() => {
                    saveBtn.disabled = false;
                    loadingSpinner.classList.add('d-none');
                    saveIcon.classList.remove('d-none');
                    showError('Lỗi', 'Đã xảy ra lỗi, vui lòng thử lại sau.');
                });
            });
        });
    });
</script>
