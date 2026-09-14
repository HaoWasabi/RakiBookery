<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= isset($category) ? "Chỉnh sửa" : "Thêm" ?> danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2 class="mb-4"><?= isset($category) ? "✏️ Chỉnh sửa danh mục" : "➕ Thêm danh mục mới" ?></h2>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control" required
                    value="<?= isset($category) ? htmlspecialchars($category['Name']) : '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" required rows="4"><?= isset($category) ? htmlspecialchars($category['Description']) : '' ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?= isset($category) ? "Lưu thay đổi" : "Thêm danh mục" ?></button>
            <a href="/category" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

</body>

</html>