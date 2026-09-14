<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý thể loại</h1>
        <a href="/admin/categories/add" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Thêm thể loại mới
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="25%">Tên thể loại</th>
                            <th width="50%">Mô tả</th>
                            <th width="20%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $category['CategoryID'] ?></td>
                                    <td class="fw-bold"><?= $category['Name'] ?></td>
                                    <td><?= $category['Description'] ?? 'Không có mô tả' ?></td>
                                    <td>
                                        <a href="/admin/categories/category?id=<?= $category['CategoryID'] ?>"
                                            class="btn btn-sm btn-primary fw-bold">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">Chưa có thể loại nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>