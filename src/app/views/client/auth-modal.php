<!-- Login/Register Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="auth-container">
                    <ul class="nav nav-tabs auth-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="login-tab" data-bs-toggle="tab"
                                data-bs-target="#login-content" type="button" role="tab" aria-controls="login-content"
                                aria-selected="true">Đăng nhập</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="register-tab" data-bs-toggle="tab"
                                data-bs-target="#register-content" type="button" role="tab"
                                aria-controls="register-content" aria-selected="false">Đăng ký</button>
                        </li>
                    </ul>
                    <div class="tab-content auth-tab-content p-4" id="authTabContent">
                        <!-- Login Tab -->
                        <div class="tab-pane fade show active" id="login-content" role="tabpanel"
                            aria-labelledby="login-tab">
                            <form id="loginForm" action="#" method="post">
                                <div class="mb-4">
                                    <label for="loginEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="loginEmail" name="email"
                                        placeholder="Nhập email" required>
                                    <div class="invalid-feedback" id="loginEmailFeedback">
                                    </div>
                                </div>
                                <div class="mb-4 position-relative">
                                    <label for="loginPassword" class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="loginPassword" name="password"
                                            placeholder="Nhập mật khẩu" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="loginPasswordFeedback">
                                    </div>
                                </div>
                                <!-- <div class="text-end mb-4">
                                    <a href="#" class="text-danger">Quên mật khẩu?</a>
                                </div> -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-danger" id="loginSubmitBtn" disabled>
                                        <span class="btn-text">Đăng nhập</span>
                                        <span class="btn-icon"><i class="fas fa-sign-in-alt ms-1"></i></span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Bỏ
                                        qua</button>
                                </div>
                            </form>
                        </div>

                        <!-- Register Tab -->
                        <div class="tab-pane fade" id="register-content" role="tabpanel" aria-labelledby="register-tab">
                            <form id="registerForm" action="#" method="post">
                                <div class="mb-4">
                                    <label for="registerFullName" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="registerFullName" name="fullName"
                                        placeholder="Nhập họ và tên của bạn" required>
                                    <div class="invalid-feedback" id="registerFullNameFeedback">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="registerPhone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="registerPhone" name="phone"
                                        placeholder="Nhập số điện thoại của bạn" required>
                                    <div class="invalid-feedback" id="registerPhoneFeedback">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="registerEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="registerEmail" name="email"
                                        placeholder="Nhập email của bạn" required>
                                    <div class="invalid-feedback" id="registerEmailFeedback">
                                    </div>
                                </div>
                                <div class="mb-4 position-relative">
                                    <label for="registerPassword" class="form-label">Mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="registerPassword"
                                            name="password" placeholder="Nhập mật khẩu" required minlength="6">
                                        <button class="btn btn-outline-secondary password-toggle" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="registerPasswordFeedback">
                                    </div>
                                </div>
                                <div class="mb-4 position-relative">
                                    <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmPassword"
                                            name="confirmPassword" placeholder="Nhập lại mật khẩu" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="confirmPasswordFeedback">
                                    </div>
                                </div>
                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                                    <label class="form-check-label" for="agreeTerms">
                                        Bằng việc đăng ký, bạn đã đồng ý với MeepBookery về
                                        <a href="#" class="text-danger">Điều khoản dịch vụ</a> &
                                        <a href="#" class="text-danger">Chính sách bảo mật</a>
                                    </label>
                                    <div class="invalid-feedback">
                                        Bạn phải đồng ý với điều khoản dịch vụ để tiếp tục.
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-danger" id="registerSubmitBtn" disabled>
                                        <span class="btn-text">Đăng ký</span>
                                        <span class="btn-icon"><i class="fas fa-user-plus ms-1"></i></span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Bỏ
                                        qua</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>