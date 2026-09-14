<!-- Category Form Fields -->
<div class="mb-3">
    <label for="name" class="form-label">Tên thể loại <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="Name" required
        oninvalid="this.setCustomValidity('Vui lòng nhập tên thể loại')" oninput="this.setCustomValidity('')"
        value="<?= $category['Name'] ?? '' ?>">
    <div class="form-text">Tên thể loại không được trùng với các thể loại đã có.</div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Mô tả</label>
    <textarea class="form-control" id="description" name="Description"
        rows="4"><?= $category['Description'] ?? '' ?></textarea>
</div>