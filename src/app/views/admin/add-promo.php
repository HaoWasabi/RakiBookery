<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm khuyến mãi mới</h1>
        <a href="/admin/promos" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="promo-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Name" class="form-label">Tên mã khuyến mãi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="Name" name="Name"
                                placeholder="VD: SALE10, SUMMER20..." required
                                oninvalid="this.setCustomValidity('Vui lòng nhập tên mã khuyến mãi')"
                                oninput="this.setCustomValidity('')">
                            <div class="form-text">Tên mã khuyến mãi phải duy nhất, không phân biệt hoa thường.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Discounted" class="form-label">Phần trăm giảm giá (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="Discounted" name="Discounted"
                                    placeholder="VD: 10" min="1" max="100" step="1" required
                                    oninvalid="this.setCustomValidity('Vui lòng nhập % giảm giá từ 1 đến 100')"
                                    oninput="this.setCustomValidity('')">
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
                                value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="Status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="Status" name="Status">
                                <option value="1" selected>Hoạt động</option>
                                <option value="0">Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-secondary me-2"
                        onclick="window.location.href='/admin/promos'">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="loading-spinner"
                            role="status" aria-hidden="true"></span>
                        <i class="fas fa-save mr-2" id="save-icon"></i> Lưu khuyến mãi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('promo-form');
        const saveBtn = document.getElementById('save-btn');
        const loadingSpinner = document.getElementById('loading-spinner');
        const saveIcon = document.getElementById('save-icon');

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

            showConfirmation('Xác nhận thêm mới', 'Bạn có chắc chắn muốn thêm khuyến mãi này không?', function () {
                saveBtn.disabled = true;
                loadingSpinner.classList.remove('d-none');
                saveIcon.classList.add('d-none');

                const formData = new FormData(form);
                const jsonData = Object.fromEntries(formData.entries());

                fetch('/admin/promos/add', {
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
                        Swal.fire({
                            title: 'Thành công',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'Quay về danh sách',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            window.location.href = data.redirect || '/admin/promos';
                        });
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
