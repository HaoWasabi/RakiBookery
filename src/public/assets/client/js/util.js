// Hàm loại bỏ dấu tiếng Việt trong chuỗi
const removeDiacritics = (str) => {
    return str.normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') // xóa dấu tiếng Việt
        .replace(/đ/g, 'd')              // đ → d
        .replace(/Đ/g, 'D');             // Đ → D
};

/*
// AutoNumeric Input
*/

const initAutoNumericInput = (selector, withCurrency = false) => {
    const options = {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        minimumValue: "0",
        modifyValueOnWheel: false,
        allowDecimalPadding: false,
        watchExternalChanges: true,
    };

    if (withCurrency) {
        options.currencySymbol = ' ₫';
        options.currencySymbolPlacement = 's';
    }

    return new AutoNumeric(selector, options);
};


/*
// Custom Toast
*/

// Biến theo dõi toast hiện tại
let toastTimeout = null;

// Biến theo dõi ID của toast
let Id = 1;

/**
 * Hiển thị thông báo dạng toast
 * @param {string} message - Nội dung thông báo
 * @param {object} options - Tùy chọn (type, title, duration, position)
 * @returns {string} ID của toast để có thể tham chiếu sau này
 */
const showToast = (message, options = {}) => {
    // Clear previous toast timeout if exists
    if (toastTimeout !== null) {
        clearTimeout(toastTimeout);
    }

    // Default options
    const defaults = {
        type: 'success', // success, error, warning, info
        title: 'Thông báo',
        duration: 3000,
        position: 'bottom-right' // top-right, top-left, bottom-left, bottom-right
    };

    // Merge default options with provided options
    const settings = { ...defaults, ...options };

    // For backward compatibility
    if (options === true) {
        settings.type = 'error';
        settings.title = 'Thông báo';
    } else if (typeof options === 'string') {
        settings.title = options;
    }

    // Create toast container if it doesn't exist
    if (!$('#toastContainer').length) {
        $('body').append(`
            <div class="toast-container position-fixed p-3" id="toastContainer"></div>
        `);
    }

    // Set position
    const toastContainer = $('#toastContainer');
    toastContainer.removeClass('top-0 bottom-0 start-0 end-0');
    switch (settings.position) {
        case 'top-right':
            toastContainer.addClass('top-0 end-0');
            break;
        case 'top-left':
            toastContainer.addClass('top-0 start-0');
            break;
        case 'bottom-left':
            toastContainer.addClass('bottom-0 start-0');
            break;
        default: // bottom-right
            toastContainer.addClass('bottom-0 end-0');
            break;
    }

    // Generate unique ID for this toast
    const toastId = 'toast-' + Id++;

    // Set color scheme based on type
    let headerClass = 'bg-info';
    let icon = 'fa-info-circle';

    switch (settings.type) {
        case 'success':
            headerClass = 'bg-success';
            icon = 'fa-check-circle';
            break;
        case 'error':
            headerClass = 'bg-danger';
            icon = 'fa-times-circle';
            break;
        case 'warning':
            headerClass = 'bg-warning';
            icon = 'fa-exclamation-triangle';
            break;
    }

    // Determine animation based on position
    let animationIn = '';

    if (settings.position.includes('top')) {
        animationIn = 'animate__fadeInDown';
    } else {
        animationIn = 'animate__fadeInUp';
    }

    if (settings.position.includes('left')) {
        animationIn = 'animate__fadeInLeft';
    } else if (settings.position.includes('right')) {
        animationIn = 'animate__fadeInRight';
    }

    // Remove any existing toasts
    $('#toastContainer .toast').each(function () {
        $(this).remove();
    });

    // Append toast to container
    toastContainer.append(`
        <div id="${toastId}" class="toast show animate__animated ${animationIn} animate__faster" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${headerClass} text-white">
                <i class="fas ${icon} me-2"></i>
                <strong class="me-auto">${settings.title}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>
    `);

    // Determine exit animation based on position
    let animationOut = '';

    if (settings.position.includes('top')) {
        animationOut = 'animate__fadeOutUp';
    } else {
        animationOut = 'animate__fadeOutDown';
    }

    if (settings.position.includes('left')) {
        animationOut = 'animate__fadeOutLeft';
    } else if (settings.position.includes('right')) {
        animationOut = 'animate__fadeOutRight';
    }

    // Function to handle toast removal with animation
    const removeToastWithAnimation = (toastElement) => {
        $(toastElement)
            .removeClass(animationIn)
            .addClass(animationOut)
            .on('animationend', function () {
                $(this).remove();
            });
    };

    // Set timeout to auto-hide the toast
    toastTimeout = setTimeout(() => {
        removeToastWithAnimation($(`#${toastId}`));
        toastTimeout = null;
    }, settings.duration);

    // Enable manual closing
    $(`#${toastId} .btn-close`).on('click', function () {
        removeToastWithAnimation($(`#${toastId}`));

        if (toastTimeout !== null) {
            clearTimeout(toastTimeout);
            toastTimeout = null;
        }
    });

    return toastId;
};


/* 
// Custom SweetAlert
*/

const showSweetAlert = (message, options = {}) => {
    // Default options
    const defaults = {
        icon: 'success', // success, error, warning, info, question
        title: 'Thông báo',
        confirmButtonText: 'Đồng ý',
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#6c757d',
        focusConfirm: false,
        returnFocus: false,
    };

    // Merge default options with provided options
    const settings = { ...defaults, ...options };

    // Add the message to the settings
    settings.text = message;

    // Special case for confirmation dialogs
    if (options.showCancelButton) {
        if (!settings.cancelButtonText) {
            settings.cancelButtonText = 'Hủy';
        }
    }

    // Return the SweetAlert2 promise for further handling if needed
    return Swal.fire(settings);
};

export { initAutoNumericInput, removeDiacritics, showSweetAlert, showToast };

