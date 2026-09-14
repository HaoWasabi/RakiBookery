import { showSweetAlert } from '/assets/client/js/util.js';

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('login-form');
    const loginBtn = document.getElementById('login-btn');
    const loginSpinner = document.getElementById('login-spinner');
    const loginIcon = document.getElementById('login-icon');

    // Toggle password visibility
    document.querySelector('.password-toggle').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });

    // Bắt sự kiện Enter để đăng nhập
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            loginForm.dispatchEvent(new Event('submit'));
        }
    });

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Show loading state
        loginBtn.disabled = true;
        loginSpinner.style.display = 'inline-block';
        loginIcon.style.display = 'none';

        // Get form data
        const formData = new FormData(loginForm);
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        // Send login request
        fetch('/admin/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formDataObj)
        })
            .then(response => response.json())
            .then(data => {
                // Reset loading state
                loginBtn.disabled = false;
                loginSpinner.style.display = 'none';
                loginIcon.style.display = 'inline-block';

                if (data.success) {
                    // Show success message with SweetAlert
                    /* showSweetAlert(data.message, {
                        icon: 'success',
                        title: 'Đăng nhập thành công',
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        window.location.href = '/admin/dashboard';
                    }); */
                    window.location.href = '/admin/dashboard';
                } else {
                    showSweetAlert(data.message, {
                        icon: 'error',
                        title: 'Đăng nhập thất bại',
                        confirmButtonText: 'Thử lại'
                    });
                }
            })
            .catch(error => {
                // Reset loading state
                loginBtn.disabled = false;
                loginSpinner.style.display = 'none';
                loginIcon.style.display = 'inline-block';
                showSweetAlert('Đã xảy ra lỗi, vui lòng thử lại sau.', {
                    icon: 'error',
                    title: 'Lỗi hệ thống',
                    confirmButtonText: 'Đã hiểu'
                });
                console.error('Login error:', error);
            });
    });
});