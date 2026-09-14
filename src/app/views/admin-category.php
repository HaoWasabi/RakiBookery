<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <?php if (isset($_SESSION['category_success']) || isset($_SESSION['category_error'])): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div id="liveToast"
                class="toast align-items-center text-white <?= isset($_SESSION['category_success']) ? 'bg-success' : 'bg-danger' ?> border-0 show"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= htmlspecialchars($_SESSION['category_success'] ?? $_SESSION['category_error']) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['category_success'], $_SESSION['category_error']); ?>
    <?php endif; ?>


    <div class="container mt-5">
        <h2 class="mb-4">📚 Quản lý danh mục</h2>
        <a href="/category/create" class="btn btn-success mb-3">➕ Thêm danh mục</a>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($category as $cat): ?>
                    <tr>
                        <td><?= $cat['CategoryID'] ?></td>
                        <td><?= htmlspecialchars($cat['Name']) ?></td>
                        <td><?= htmlspecialchars($cat['Description']) ?></td>
                        <td>
                            <a href="/category/edit?id=<?= $cat['CategoryID'] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                            <a href="/category/delete?id=<?= $cat['CategoryID'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc muốn xóa không?')">🗑️ Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        setTimeout(function () {
            let toastEl = document.getElementById('liveToast');
            if (toastEl) {
                let toast = bootstrap.Toast.getOrCreateInstance(toastEl);
                toast.hide();
            }
        }, 3000);
    </script>

</body>

</html>