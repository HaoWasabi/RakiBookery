<!-- User Form Fields -->
<div class="mb-3">
    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="Name" required
        oninvalid="this.setCustomValidity('Vui lòng nhập họ và tên')" oninput="this.setCustomValidity('')"
        value="<?= $user['Name'] ?? '' ?>">
</div>

<div class="mb-3">
    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
    <input type="tel" class="form-control" id="phone" name="Phone"
        oninvalid="this.setCustomValidity('Vui lòng nhập số điện thoại')" oninput="this.setCustomValidity('')" required
        value="<?= $user['Phone'] ?? '' ?>">
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
    <input type="email" class="form-control" id="email" name="Email" required
        oninvalid="this.setCustomValidity('Vui lòng nhập email')" oninput="this.setCustomValidity('')"
        value="<?= $user['Email'] ?? '' ?>" <?= isset($isEditMode) && $isEditMode ? '' : '' ?>>
    <!-- < ?php if (!isset($isEditMode) || !$isEditMode): ?>
            <div class="form-text">Email sẽ được sử dụng làm tên đăng nhập.</div>
        < ?php endif; ?> -->
</div>

<?php if (!$isEditMode): ?>
    <div class="mb-3">
        <label for="new_password" class="form-label">Mật khẩu</label>
        <div class="input-group">
            <input type="password" class="form-control" id="password" name="Password">
            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu.</div>
    </div>
<?php endif; ?>

<div class="mb-3">
    <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
    <select class="form-select" id="role" name="Role" <?php if (isset($isEditMode) && $isEditMode)
        echo 'disabled' ?>
            required>
            <option value="user" <?= isset($user['Role']) && $user['Role'] == 'user' ? 'selected' : '' ?>>Khách hàng</option>
        <option value="admin" <?= isset($user['Role']) && $user['Role'] == 'admin' ? 'selected' : '' ?>>Admin
        </option>
    </select>
</div>

<!-- < ?php if (isset($isEditMode) && $isEditMode): ?>
    <div class="mb-3">
        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
        <select class="form-select" id="status" name="status" required>
            <option value="1" < ?= isset($user['Status']) && $user['Status'] == 1 ? 'selected' : '' ?>>Hoạt động</option>
            <option value="0" < ?= isset($user['Status']) && $user['Status'] == 0 ? 'selected' : '' ?>>Khóa</option>
        </select>
    </div>
    
< ?php endif; ?> -->

<!-- Address Fields -->
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="address" class="form-label">Địa chỉ chi tiết</label>
        <input type="text" class="form-control" id="address" name="Address" value="<?= $user['Address'] ?? '' ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">Thành phố</label>
        <input type="text" class="form-control" id="city" name="City" value="<?= $user['City'] ?? '' ?>">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="district" class="form-label">Quận/Huyện</label>
        <input type="text" class="form-control" id="district" name="District" value="<?= $user['District'] ?? '' ?>">
    </div>

    <div class="col-md-6 mb-3">
        <label for="ward" class="form-label">Phường/Xã</label>
        <input type="text" class="form-control" id="ward" name="Ward" value="<?= $user['Ward'] ?? '' ?>">
    </div>
</div>