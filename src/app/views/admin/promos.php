<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý khuyến mãi</h1>
        <a href="/admin/promos/add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Thêm khuyến mãi mới
        </a>
    </div>

    <!-- Alert messages -->
    <div id="alertMessage" class="d-none"></div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="promosTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="30%">Tên khuyến mãi</th>
                            <th width="15%">Giảm giá (%)</th>
                            <th width="20%">Ngày tạo</th>
                            <th width="10%">Trạng thái</th>
                            <th width="20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($promos)): ?>
                            <?php foreach ($promos as $promo): ?>
                                <tr id="row-<?= $promo['PromoID'] ?>">
                                    <td><?= htmlspecialchars($promo['PromoID']) ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($promo['Name']) ?></td>
                                    <td><?= htmlspecialchars($promo['Discounted']) ?>%</td>
                                    <td><?= htmlspecialchars($promo['DateCreate'] ?? '') ?></td>
                                    <td>
                                        <?php if (isset($promo['Status']) && $promo['Status']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Không hoạt động</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/admin/promos/promo?id=<?= $promo['PromoID'] ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <button class="btn btn-sm btn-danger btn-delete"
                                            data-id="<?= $promo['PromoID'] ?>"
                                            data-name="<?= htmlspecialchars($promo['Name']) ?>">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-3">Chưa có khuyến mãi nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa khuyến mãi <strong id="deletePromoName"></strong> không?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<script>
    let deletePromoId = null;

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            deletePromoId = this.dataset.id;
            document.getElementById('deletePromoName').textContent = this.dataset.name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', function () {
        if (!deletePromoId) return;

        fetch('/admin/promos/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: deletePromoId })
        })
        .then(res => res.json())
        .then(data => {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            if (data.success) {
                document.getElementById('row-' + deletePromoId).remove();
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(() => showAlert('danger', 'Có lỗi xảy ra. Vui lòng thử lại.'));
    });

    function showAlert(type, message) {
        const el = document.getElementById('alertMessage');
        el.className = `alert alert-${type}`;
        el.textContent = message;
        el.classList.remove('d-none');
        setTimeout(() => el.classList.add('d-none'), 3000);
    }
</script>
