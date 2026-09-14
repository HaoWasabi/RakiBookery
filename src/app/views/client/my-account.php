<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="account-sidebar card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Tài khoản của tôi</h4>
                </div>
                <div class="list-group list-group-flush">
                    <a href="/my-account" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="/my-account/order-history" class="list-group-item list-group-item-action">
                        <i class="fas fa-clipboard-list me-2"></i> Lịch sử đơn hàng
                    </a>
                    <a href="/cart" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-cart me-2"></i> Giỏ hàng
                    </a>
                    <a href="/logout" class="list-group-item list-group-item-action text-danger" id="sidebar-logout">
                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Thông tin cá nhân</h4>
                </div>
                <div class="card-body">
                    <!-- Success/Error Alert -->
                    <div id="accountUpdateAlert" class="alert alert-success alert-dismissible fade show d-none"
                        role="alert">
                        <span id="alertMessage">Thông tin đã được cập nhật thành công!</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <!-- Account Form -->
                    <form id="accountForm" method="POST" action="/my-account/update" data-aos="fade-up">
                        <!-- This form will POST to /update-account endpoint -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="Name" name="Name"
                                        placeholder="Họ và tên" value="<?php if (isset($user) && isset($user['Name']))
                                            echo htmlspecialchars($user['Name']); ?>" disabled>
                                    <label for="name">Họ và tên <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="Email" name="Email" placeholder="Email"
                                        disabled value="<?php if (isset($user) && isset($user['Email']))
                                            echo htmlspecialchars($user['Email']) ?>">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="tel" class="form-control" id="Phone" name="Phone"
                                            placeholder="Số điện thoại" value="<?php if (isset($user) && isset($user['Phone']))
                                            echo htmlspecialchars($user['Phone'] ?? '') ?>" disabled>
                                        <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center justify-content-end">
                                    <div class="personal-info-buttons">
                                        <button type="button" class="btn btn-outline-primary" id="editPersonalInfoBtn">
                                            <i class="fas fa-user-edit me-2"></i> Cập nhật thông tin cá nhân
                                        </button>
                                        <div class="edit-mode-buttons" style="display: none;">
                                            <button type="button" class="btn btn-outline-secondary me-2"
                                                id="cancelPersonalInfoBtn">
                                                <i class="fas fa-times me-2"></i> Hủy
                                            </button>
                                            <button type="button" class="btn btn-primary" id="savePersonalInfoBtn">
                                                <i class="fas fa-save me-2"></i> Lưu
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-top pt-4 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Địa chỉ giao hàng mặc định</h5>
                                    <?php
                                        $hasAddress = isset($user) && !empty($user['Address']);
                                        ?>
                                <div class="address-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="changeAddressBtn">
                                        <i class="fas fa-edit me-1"></i>Cập nhật địa chỉ
                                    </button>
                                </div>
                            </div>

                            <!-- Hiển thị địa chỉ -->
                            <div id="displayAddressSection">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <?php if ($hasAddress): ?>
                                            <p class="mb-0">
                                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                                <?php
                                                $addressParts = [];
                                                if (!empty($user['Address']))
                                                    $addressParts[] = $user['Address'];
                                                if (!empty($user['Ward']))
                                                    $addressParts[] = $user['Ward'];
                                                if (!empty($user['District']))
                                                    $addressParts[] = $user['District'];
                                                if (!empty($user['City']))
                                                    $addressParts[] = $user['City'];
                                                echo implode(', ', $addressParts);
                                                ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-exclamation-circle me-2"></i>
                                                Bạn chưa có địa chỉ giao hàng mặc định. Vui lòng cập nhật địa chỉ.
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Form chỉnh sửa địa chỉ - ẩn ban đầu -->
                            <div id="editAddressSection" style="display: none;">
                                <div class="card border mb-3">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="Address" name="Address"
                                                        placeholder="Địa chỉ chi tiết"
                                                        value="<?php echo isset($user['Address']) ? htmlspecialchars($user['Address']) : ''; ?>">
                                                    <label for="Address">Địa chỉ chi tiết <span
                                                            class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4 mb-3 mb-md-0">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="City" name="City"
                                                        placeholder="Tỉnh/Thành phố"
                                                        value="<?php echo isset($user['City']) ? htmlspecialchars($user['City']) : ''; ?>">
                                                    <label for="City">Tỉnh/Thành phố <span
                                                            class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3 mb-md-0">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="District"
                                                        name="District" placeholder="Quận/Huyện"
                                                        value="<?php echo isset($user['District']) ? htmlspecialchars($user['District']) : ''; ?>">
                                                    <label for="District">Quận/Huyện <span
                                                            class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="Ward" name="Ward"
                                                        placeholder="Phường/Xã"
                                                        value="<?php echo isset($user['Ward']) ? htmlspecialchars($user['Ward']) : ''; ?>">
                                                    <label for="Ward">Phường/Xã <span
                                                            class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="cancelAddressBtn">
                                                <i class="fas fa-times me-1"></i> Hủy
                                            </button>
                                            <button type="button" class="btn btn-primary" id="saveAddressBtn">
                                                <i class="fas fa-save me-1"></i> Lưu địa chỉ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script để xử lý form và hiệu ứng -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Form validation và xử lý
        const accountForm = document.getElementById('accountForm');
        const alertBox = document.getElementById('accountUpdateAlert');
        const alertMessage = document.getElementById('alertMessage');

        // Personal info elements
        const editPersonalInfoBtn = document.getElementById('editPersonalInfoBtn');
        const cancelPersonalInfoBtn = document.getElementById('cancelPersonalInfoBtn');
        const savePersonalInfoBtn = document.getElementById('savePersonalInfoBtn');
        const personalInfoEditButtons = document.querySelector('.personal-info-buttons .edit-mode-buttons');

        // Address elements
        const changeAddressBtn = document.getElementById('changeAddressBtn');
        const cancelAddressBtn = document.getElementById('cancelAddressBtn');
        const saveAddressBtn = document.getElementById('saveAddressBtn');
        const addressEditButtons = document.querySelector('.address-buttons .edit-mode-buttons');

        const displayAddressSection = document.getElementById('displayAddressSection');
        const editAddressSection = document.getElementById('editAddressSection');

        // Personal info fields
        const nameField = document.getElementById('Name');
        const emailField = document.getElementById('Email');
        const phoneField = document.getElementById('Phone');

        // Personal info button handlers
        if (editPersonalInfoBtn) {
            editPersonalInfoBtn.addEventListener('click', function () {
                // Hide edit button, show cancel and save buttons
                editPersonalInfoBtn.style.display = 'none';
                personalInfoEditButtons.style.display = 'flex';

                // Enable fields
                nameField.disabled = false;
                phoneField.disabled = false;

                // Focus on the first field
                nameField.focus();
            });
        }

        if (cancelPersonalInfoBtn) {
            cancelPersonalInfoBtn.addEventListener('click', function () {
                // Show edit button, hide cancel and save buttons
                editPersonalInfoBtn.style.display = 'block';
                personalInfoEditButtons.style.display = 'none';

                // Disable fields and reset values
                nameField.disabled = true;
                phoneField.disabled = true;
                nameField.value = '<?php echo htmlspecialchars($user['Name'] ?? ''); ?>';
                phoneField.value = '<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>';
            });
        }

        if (savePersonalInfoBtn) {
            savePersonalInfoBtn.addEventListener('click', function () {
                // Validate and save personal info
                const name = nameField.value.trim();
                const phone = phoneField.value.trim();

                if (!name) {
                    showAlert('Vui lòng nhập họ tên của bạn', 'danger');
                    return;
                }

                if (!phone) {
                    showAlert('Vui lòng nhập số điện thoại', 'danger');
                    return;
                }

                if (!/^(?:\+84|0)([0-9]{9})$/.test(phone)) {
                    showAlert('Số điện thoại không hợp lệ', 'danger');
                    return;
                }

                // Create form data
                const formData = new FormData();
                formData.append('Name', name);
                formData.append('Phone', phone);

                // Submit the form
                submitPersonalInfoForm(formData, 'personal');
            });
        }

        // Address button handlers
        if (changeAddressBtn) {
            changeAddressBtn.addEventListener('click', function () {
                displayAddressSection.style.display = 'none';
                editAddressSection.style.display = 'block';
            });
        }

        if (cancelAddressBtn) {
            cancelAddressBtn.addEventListener('click', function () {
                displayAddressSection.style.display = 'block';
                editAddressSection.style.display = 'none';

                // Reset address fields
                document.getElementById('Address').value = '<?php echo htmlspecialchars($user['Address'] ?? ''); ?>';
                document.getElementById('City').value = '<?php echo htmlspecialchars($user['City'] ?? ''); ?>';
                document.getElementById('District').value = '<?php echo htmlspecialchars($user['District'] ?? ''); ?>';
                document.getElementById('Ward').value = '<?php echo htmlspecialchars($user['Ward'] ?? ''); ?>';
            });
        }

        if (saveAddressBtn) {
            saveAddressBtn.addEventListener('click', function () {
                // Validate and save address
                const address = document.getElementById('Address').value.trim();
                const city = document.getElementById('City').value.trim();
                const district = document.getElementById('District').value.trim();
                const ward = document.getElementById('Ward').value.trim();

                // Validate all address fields
                if (!address) {
                    showAlert('Vui lòng nhập địa chỉ chi tiết', 'danger');
                    return;
                }

                if (!city) {
                    showAlert('Vui lòng nhập Tỉnh/Thành phố', 'danger');
                    return;
                }

                if (!district) {
                    showAlert('Vui lòng nhập Quận/Huyện', 'danger');
                    return;
                }

                if (!ward) {
                    showAlert('Vui lòng nhập Phường/Xã', 'danger');
                    return;
                }

                // Create form data only with address fields
                const formData = new FormData();
                formData.append('Address', address);
                formData.append('City', city);
                formData.append('District', district);
                formData.append('Ward', ward);
                formData.append('update_type', 'address');

                // Submit the form
                submitAddressForm(formData);
            });
        }

        // Function to submit address form data
        function submitAddressForm(formData) {
            // Show loading state
            const saveBtn = saveAddressBtn;
            const originalBtnText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang lưu...';
            saveBtn.disabled = true;

            fetch('/my-account/update', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Restore button state
                    saveBtn.innerHTML = originalBtnText;
                    saveBtn.disabled = false;

                    if (data.success) {
                        // Replace showAlert with SweetAlert2
                        Swal.fire({
                            title: 'Thành công',
                            text: 'Địa chỉ đã được cập nhật thành công!',
                            icon: 'success',
                            confirmButtonColor: '#e74c3c',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Switch back to view mode for address
                            displayAddressSection.style.display = 'block';
                            editAddressSection.style.display = 'none';

                            // Get the new address values
                            const address = document.getElementById('Address').value.trim();
                            const city = document.getElementById('City').value.trim();
                            const district = document.getElementById('District').value.trim();
                            const ward = document.getElementById('Ward').value.trim();

                            // Update the displayed address
                            const fullAddress = [];
                            if (address) fullAddress.push(address);
                            if (ward) fullAddress.push(ward);
                            if (district) fullAddress.push(district);
                            if (city) fullAddress.push(city);

                            const combinedAddress = fullAddress.join(', ');

                            // Update the display or create it if not exists
                            let addressDisplay = document.querySelector('#displayAddressSection .card-body p');

                            if (addressDisplay) {
                                addressDisplay.innerHTML = `<i class="fas fa-map-marker-alt me-2 text-danger"></i> ${combinedAddress}`;
                            } else {
                                // Create new content if it was an empty address before
                                const cardBody = document.querySelector('#displayAddressSection .card-body');
                                if (cardBody) {
                                    cardBody.innerHTML = `<p class="mb-0"><i class="fas fa-map-marker-alt me-2 text-danger"></i> ${combinedAddress}</p>`;
                                }
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi',
                            text: data.message || 'Có lỗi xảy ra khi cập nhật địa chỉ',
                            icon: 'error',
                            confirmButtonColor: '#e74c3c',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    // Restore button state
                    saveBtn.innerHTML = originalBtnText;
                    saveBtn.disabled = false;
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi kết nối đến máy chủ',
                        icon: 'error',
                        confirmButtonColor: '#e74c3c',
                        confirmButtonText: 'OK'
                    });
                    console.error('Error:', error);
                });
        }

        // Function to submit personal info form data
        function submitPersonalInfoForm(formData, type) {
            // Only handle personal info in this function now
            if (type !== 'personal') {
                console.error('Invalid form type');
                return;
            }

            // Show loading state
            const saveBtn = savePersonalInfoBtn;
            const originalBtnText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang lưu...';
            saveBtn.disabled = true;

            // Add a flag to indicate this is a personal info update
            formData.append('update_type', 'personal');

            // Submit the form to the endpoint
            fetch('/my-account/update', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // Restore button state
                    saveBtn.innerHTML = originalBtnText;
                    saveBtn.disabled = false;

                    if (data.success) {
                        // Replace showAlert with SweetAlert2
                        Swal.fire({
                            title: 'Thành công',
                            text: 'Thông tin cá nhân đã được cập nhật thành công!',
                            icon: 'success',
                            confirmButtonColor: '#e74c3c',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Switch back to view mode for personal info
                            editPersonalInfoBtn.style.display = 'block';
                            personalInfoEditButtons.style.display = 'none';
                            nameField.disabled = true;
                            phoneField.disabled = true;
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Lỗi',
                            text: data.message || 'Có lỗi xảy ra khi cập nhật thông tin cá nhân',
                            icon: 'error',
                            confirmButtonColor: '#e74c3c',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    // Restore button state
                    saveBtn.innerHTML = originalBtnText;
                    saveBtn.disabled = false;
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Có lỗi xảy ra khi kết nối đến máy chủ',
                        icon: 'error',
                        confirmButtonColor: '#e74c3c',
                        confirmButtonText: 'OK'
                    });
                    console.error('Error:', error);
                });
        }

        // Function to show alert messages
        function showAlert(message, type = 'success') {
            alertBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
            alertBox.classList.add(`alert-${type}`);
            alertMessage.textContent = message;
            alertBox.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Handle sidebar logout confirmation
        const sidebarLogout = document.getElementById('sidebar-logout');
        if (sidebarLogout) {
            sidebarLogout.addEventListener('click', function (e) {
                e.preventDefault();

                // Use SweetAlert2 for confirmation (already included in head.php)
                Swal.fire({
                    title: 'Đăng xuất',
                    text: 'Bạn có chắc chắn muốn đăng xuất?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Đăng xuất',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/logout';
                    }
                });
            });
        }
    });
</script>

<!-- CSS để tùy chỉnh giao diện (có thể chuyển sang file riêng) -->
<style>
    .account-sidebar .list-group-item {
        border-radius: 0;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .account-sidebar .list-group-item.active {
        background-color: #e74c3c;
        border-color: #e74c3c;
        color: white;
    }

    .account-sidebar .list-group-item:hover:not(.active) {
        background-color: #f8f9fa;
    }

    .form-floating>.form-control {
        padding: 1rem 0.75rem;
    }

    .form-floating>label {
        padding: 1rem 0.75rem;
    }

    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label {
        opacity: 0.65;
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }

    .personal-info-buttons .edit-mode-buttons,
    .address-buttons .edit-mode-buttons {
        display: flex;
        align-items: center;
    }
</style>