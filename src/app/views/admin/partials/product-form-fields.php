<!-- Product Info Fields -->
<div class="row">
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sách <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="Name" placeholder="Nhập tên sách" required
                        oninvalid="this.setCustomValidity('Vui lòng nhập tên sách')"
                        oninput="this.setCustomValidity('')" value="<?= $book['Name'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="author" class="form-label">Tác giả <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author" name="Author" placeholder="Nhập tên tác giả"
                        required oninvalid="this.setCustomValidity('Vui lòng nhập tên tác giả')"
                        oninput="this.setCustomValidity('')" value="<?= $book['Author'] ?? '' ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="publisher" class="form-label">Nhà xuất bản <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="publisher" name="Publisher"
                        placeholder="Nhập nhà xuất bản" required
                        oninvalid="this.setCustomValidity('Vui lòng nhập nhà xuất bản')"
                        oninput="this.setCustomValidity('')" value="<?= $book['Publisher'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="release_date" class="form-label">Ngày phát hành
                        <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="release_date" name="ReleaseDate" required
                        oninvalid="this.setCustomValidity('Vui lòng chọn ngày phát hành hợp lệ')"
                        oninput="this.setCustomValidity('')"
                        value="<?= isset($book['ReleaseDate']) && $book['ReleaseDate'] ? date('Y-m-d', strtotime($book['ReleaseDate'])) : '' ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="category" class="form-label">Thể loại <span class="text-danger">*</span></label>
                    <select class="form-select" id="category" name="CategoryID" required
                        oninvalid="this.setCustomValidity('Vui lòng chọn thể loại')"
                        oninput="this.setCustomValidity('')" value="<?= $book['CategoryID'] ?? '' ?>">
                        <option value="">Chọn thể loại</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['CategoryID'] ?>" <?= isset($book['CategoryID']) && $category['CategoryID'] == $book['CategoryID'] ? 'selected' : '' ?>>
                                <?= $category['Name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="price" name="Price" placeholder="Nhập giá sách"
                            oninvalid="this.setCustomValidity('Vui lòng nhập giá hợp lệ')"
                            oninput="this.setCustomValidity('')" required min="0"
                            value="<?= isset($book['Price']) ? (float) str_replace(',', '', $book['Price']) : '' ?>">
                        <!-- <span class="input-group-text">VNĐ</span> -->
                        <span class="input-group-text">₫</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6" style="display: none;">
                <div class="mb-3">
                    <label for="stock" class="form-label">Tồn kho <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="stock" name="Stock" value="500000" readonly>
                    <div class="form-text text-muted">Mặc định là 500,000</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="language" class="form-label">Ngôn ngữ</label>
                    <input type="text" class="form-control" id="language" name="Language" placeholder="Nhập ngôn ngữ"
                        value="<?= $book['Language'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="format" class="form-label">Định dạng</label>
                    <input type="text" class="form-control" id="format" name="Format" placeholder="Nhập định dạng"
                        value="<?= $book['Format'] ?? '' ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="dimensions" class="form-label">Kích thước</label>
                    <input type="text" class="form-control" id="dimensions" name="Dimensions"
                        placeholder="Nhập kích thước" value="<?= $book['Dimensions'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="weight" class="form-label">Trọng lượng (kg)</label>
                    <input type="number" class="form-control" id="weight" name="Weight" min="0.01" step="0.01"
                        placeholder="Nhập trọng lượng"
                        oninvalid="this.setCustomValidity('Vui lòng nhập trọng lượng hợp lệ')"
                        oninput="this.setCustomValidity('')" value="<?= $book['Weight'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="length" class="form-label">Số trang</label>
                    <input type="number" class="form-control" id="length" name="Length" min="1"
                        placeholder="Nhập số trang" oninvalid="this.setCustomValidity('Vui lòng nhập số trang hợp lệ')"
                        oninput="this.setCustomValidity('')" value="<?= $book['Length'] ?? '' ?>">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả sản phẩm</label>
            <textarea class="form-control" id="description" name="Description" placeholder="Nhập mô tả sản phẩm"
                rows="5"><?= $book['Description'] ?? '' ?></textarea>
        </div>

        <?php if (isset($isEditMode) && $isEditMode): ?>
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="Status">
                    <option value="1" <?= isset($book['Status']) && $book['Status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= isset($book['Status']) && $book['Status'] == 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh sản phẩm</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*"
                onchange="previewImage(this)">
            <div class="form-text">
                <?php if (isset($isEditMode) && $isEditMode): ?>
                    Để trống nếu không muốn thay đổi hình ảnh hiện tại. Nếu xóa hình ảnh, hệ thống sẽ sử dụng ảnh mặc định.
                <?php else: ?>
                    Hình ảnh sẽ được hiển thị trên trang chi tiết sản phẩm. Nếu không chọn ảnh, hệ thống sẽ sử dụng ảnh mặc
                    định.
                <?php endif; ?>
            </div>
            <?php if (isset($isEditMode) && $isEditMode): ?>
                <input type="hidden" name="ImageURL" value="<?= $book['ImageURL'] ?? '' ?>">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <div class="text-center p-3 border rounded" id="image-preview-container" ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <?php if (isset($book['ImageURL']) && $book['ImageURL']): ?>
                    <img id="image-preview" src="<?= $book['ImageURL'] ?>" alt="<?= $book['Name'] ?? '' ?>"
                        class="img-fluid" style="max-height: 300px;">
                    <div id="no-image" class="text-muted py-5 d-none">
                        <i class="fas fa-image fa-4x mb-3"></i>
                        <p>Chưa có hình ảnh (sẽ dùng ảnh mặc định)</p>
                    </div>
                <?php else: ?>
                    <img id="image-preview" src="" alt="Xem trước hình ảnh" class="img-fluid d-none"
                        style="max-height: 300px;">
                    <div id="no-image" class="text-muted py-5">
                        <i class="fas fa-image fa-4x mb-3"></i>
                        <p>Chưa có hình ảnh (sẽ dùng ảnh mặc định)</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($isEditMode) && $isEditMode): ?>
            <div class="mb-3" style="display: none;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="use_default_image" name="use_default_image">
                    <label class="form-check-label" for="use_default_image">
                        Sử dụng ảnh mặc định
                    </label>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Constants for validation
    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ERROR_MESSAGES = {
        INVALID_TYPE: 'Vui lòng chọn một tệp hình ảnh có định dạng ["jpg", "jpeg", "png", "gif", "webp"].',
        INVALID_SIZE: 'Kích thước tệp vượt quá 5MB. Vui lòng chọn tệp nhỏ hơn.'
    };


    // Utility to get the initial state of the preview
    function getInitialPreviewState(imgSrc = null) {
        const preview = document.getElementById('image-preview');
        const noImage = document.getElementById('no-image');
        return {
            src: (imgSrc != null) ? imgSrc : preview.src,
            isPreviewVisible: !preview.classList.contains('d-none'),
            isNoImageVisible: !noImage.classList.contains('d-none')
        };
    }

    // Utility to restore the preview to its initial state
    function restorePreviewState(initialState) {
        const preview = document.getElementById('image-preview');
        const noImage = document.getElementById('no-image');
        preview.src = initialState.src;
        if (initialState.isPreviewVisible) {
            preview.classList.remove('d-none');
            noImage.classList.add('d-none');
        } else {
            preview.classList.add('d-none');
            noImage.classList.remove('d-none');
        }
    }

    // Utility to update the preview with a new image
    function updatePreview(file) {
        const preview = document.getElementById('image-preview');
        const noImage = document.getElementById('no-image');
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
            noImage.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }

    // Validate file type and size, returns true if valid, otherwise shows error
    function validateFile(file) {
        if (!ALLOWED_TYPES.includes(file.type)) {
            showError('Lỗi', ERROR_MESSAGES.INVALID_TYPE);
            return false;
        }
        if (file.size > MAX_FILE_SIZE) {
            showError('Lỗi', ERROR_MESSAGES.INVALID_SIZE);
            return false;
        }
        return true;
    }

    // Preview the image and assign the file to the input if valid
    function previewImage(inputOrFile) {
        const input = document.getElementById('image');
        const isFromInput = inputOrFile instanceof HTMLInputElement;
        const file = isFromInput ? inputOrFile.files[0] : inputOrFile;

        // Capture the initial state of the preview
        const initialState = getInitialPreviewState();

        // If no file, restore the initial state and clear the input (if applicable)
        if (!file) {
            restorePreviewState(initialState);
            if (isFromInput) inputOrFile.value = ''; // Clear the input
            return;
        }

        // Validate the file
        if (!validateFile(file)) {
            restorePreviewState(initialState); // Restore the original image
            if (isFromInput) inputOrFile.value = ''; // Clear the input
            return;
        }

        // If validation passes, update the preview
        updatePreview(file);

        // Assign dropped file to input for form submission
        if (!isFromInput) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;
        }
    }

    // Handle drag over (visual feedback)
    function handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById('image-preview-container')
            .classList.add('border-primary', 'bg-light');
    }

    // Handle drag leave (remove visual feedback)
    function handleDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();
        document.getElementById('image-preview-container')
            .classList.remove('border-primary', 'bg-light');
    }

    // Handle drop (process dropped file)
    function handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        const container = document.getElementById('image-preview-container');
        container.classList.remove('border-primary', 'bg-light');

        const file = event.dataTransfer.files[0];
        if (file && ALLOWED_TYPES.includes(file.type)) {
            previewImage(file);
        } else if (file) {
            showError('Lỗi', ERROR_MESSAGES.INVALID_TYPE);
        }
    }

    <?php if (isset($isEditMode) && $isEditMode): ?>
        document.addEventListener('DOMContentLoaded', function () {
            const useDefaultCheckbox = document.getElementById('use_default_image');
            const preview = document.getElementById('image-preview');
            const noImage = document.getElementById('no-image');

            useDefaultCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    preview.classList.add('d-none');
                    noImage.classList.remove('d-none');
                    document.getElementById('image').value = '';
                } else {
                    // If there's an existing image, show it again
                    if (preview.getAttribute('src')) {
                        preview.classList.remove('d-none');
                        noImage.classList.add('d-none');
                    }
                }
            });
        });
    <?php endif; ?>
</script>