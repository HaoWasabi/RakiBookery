import { createBookCard } from "./bookcard.js";
import { initAutoNumericInput, removeDiacritics, showSweetAlert, showToast } from "./util.js";

// Initialize AOS
AOS.init({
    duration: 400,
    easing: 'ease-in-out',
    once: true,
    disable: 'mobile'
});


const swiper = new Swiper('.swiper-container', {
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    speed: 1000,
});

// Ngăn việc truy cập cart offcanvas khi đang ở giỏ hàng hoặc trang thanh toán
if (window.location.pathname === '/cart' || window.location.pathname === '/cart/checkout') {
    const cartButton = document.querySelector('.cart-btn');
    if (cartButton) {
        cartButton.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Dùng để tránh việc cuộn trang lần đầuđầu
    let isInitialShopPageLoad = true;
    let isInitialHomePageLoad = true;

    // Update cart UI when page loads
    updateCartInterface();

    // Handle search form submission
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const searchInput = this.querySelector('input[name="search"]');
            const searchTerm = searchInput.value.trim();

            // Always redirect to shop.php, with or without search term
            let url = '/shop';
            if (searchTerm) {
                url += '?search=' + encodeURIComponent(searchTerm);
            }

            window.location.href = url;
        });
    }

    // Highlight active category in dropdown
    /*  const highlightActiveCategory = () => {
         const urlParams = new URLSearchParams(window.location.search);
         const categoryParam = urlParams.get('category');
 
         if (categoryParam) {
             const categoryItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
             categoryItems.forEach(item => {
                 const itemCategory = item.textContent.trim();
                 if (itemCategory === decodeURIComponent(categoryParam)) {
                     // Remove active class from all items
                     categoryItems.forEach(i => i.classList.remove('active'));
                     // Add active to current item
                     item.classList.add('active');
                 }
             });
         }
     };
 
     // // Call function to highlight active category
     highlightActiveCategory(); */


    // Hiển thị sách bán chạy và sách nếu ở trang home
    if (document.getElementById('featured-books-container')) {
        let bestSellerBooksHtml = '';

        const availableBestSellers = bestSellerBooks.filter(book => book.Status === 1);

        availableBestSellers.forEach(book => {
            bestSellerBooksHtml += createBookCard(book);
        });

        document.getElementById('featured-books-container').innerHTML = bestSellerBooksHtml;

        // Khởi tạo phân trang với paginationjs
        // const availableBooks = allBooks.filter(book => book.Status === 1);

        // $('#pagination-container').pagination({
        //     dataSource: availableBooks,
        //     pageSize: 8,
        //     autoHidePrevious: true,
        //     autoHideNext: true,
        //     hideOnlyOnePage: true,
        //     prevText: '<i class="fas fa-chevron-left"></i>',
        //     nextText: '<i class="fas fa-chevron-right"></i>',
        //     pageRange: 2,
        //     callback: (data, pagination) => {
        //         // Render HTML
        //         let html = '';

        //         data.forEach(book => {
        //             html += createBookCard(book);
        //         });

        //         $('#books-container').html(html);

        //         // Scroll to pagination position
        //         if (pagination.pageNumber >= 1 && !isInitialHomePageLoad) {
        //             $('html, body').animate({
        //                 scrollTop: $('#books-container').offset().top - 200
        //             }, 200);
        //         }

        //         // Update the flag after the first page load
        //         isInitialHomePageLoad = false;
        //     },
        //     locator: 'items'
        // });

    }
    // Kiểm tra nếu đang ở trang shop
    if (document.getElementById('shop-products-container')) {
        // Price slider initialization
        const priceSlider = document.getElementById('price-range-slider');
        const priceMinLabel = document.querySelector('.price-min');
        const priceMaxLabel = document.querySelector('.price-max');

        const minPriceInput = initAutoNumericInput('#min-price');
        const maxPriceInput = initAutoNumericInput('#max-price');

        let prices = allBooks.map(book => parseFloat((book.Price)));
        let minPrice = 0;
        let maxPrice = Math.max(...prices);

        // Function to fetch the latest product data before searching
        const fetchLatestProductData = async () => {
            try {
                // Fetch dữ liệu
                const response = await fetch('/api/products/filtered');
                const data = await response.json();

                if (data.success) {
                    allBooks = data.data.books;

                    // Cập nhật giá trị min/max từ dữ liệu mới
                    prices = allBooks.map(book => parseFloat((book.Price)));
                    maxPrice = Math.max(...prices);

                    // Cập nhật range cho slider
                    if (priceSlider && priceSlider.noUiSlider) {
                        priceSlider.noUiSlider.updateOptions({
                            range: {
                                'min': minPrice,
                                'max': maxPrice
                            }
                        });
                    }

                    // Cập nhật label hiển thị giá max
                    if (priceMaxLabel) {
                        priceMaxLabel.textContent = formatCurrency(maxPrice);
                    }

                    return true;
                } else {
                    console.error('Error fetching product data');
                    return false;
                }
            } catch (error) {
                console.error('Error fetching product data:', error);
                return false;
            }
        };

        // Set initial values for inputs
        minPriceInput.set(minPrice);
        maxPriceInput.set(maxPrice);

        // Hàm định dạng tiền tệ
        const formatCurrency = (value) => {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        };

        // Hàm định dạng số cho tooltip
        const formatNumber = (value) => {
            return new Intl.NumberFormat('vi-VN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        };

        // Initialize noUiSlider
        noUiSlider.create(priceSlider, {
            start: [minPrice, maxPrice],
            connect: true,
            step: 1,
            range: {
                'min': minPrice,
                'max': maxPrice
            },
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            },
            tooltips: [
                {
                    to: function (value) {
                        return formatNumber(value);
                    }
                },
                {
                    to: function (value) {
                        return formatNumber(value);
                    }
                }
            ]
        });

        // Khởi tạo labels min-max
        priceMinLabel.textContent = formatCurrency(minPrice);
        priceMaxLabel.textContent = formatCurrency(maxPrice);

        // Cập nhật giá trị min, max price input khi slider di chuyển
        priceSlider.noUiSlider.on('update', function (values, handle) {
            const value = Math.round(values[handle]);
            const minValue = Math.round(values[0]);
            const maxValue = Math.round(values[1]);

            // Update input fields
            if (handle === 0) {
                minPriceInput.set(minValue);
            } else {
                maxPriceInput.set(maxValue);
            }
        });

        // Apply filter when slider stops
        priceSlider.noUiSlider.on('change', function (values) {
            const minValue = Math.round(values[0]);
            const maxValue = Math.round(values[1]);
            updateSlider(minValue, maxValue);
        });

        // Filter and display products
        let filteredBooks = [...allBooks];
        const booksPerPage = 8;


        function applyFilters() {
            const searchTerm = document.getElementById('search-term').value.toLowerCase().trim();
            const selectedCategory = document.getElementById('category-filter').value;
            const minPrice = parseFloat(minPriceInput.getNumber() || 0);
            const currentMaxPrice = parseFloat(maxPriceInput.getNumber() || maxPrice);
            const sortBy = document.getElementById('sort-by').value;

            // Filter books
            filteredBooks = allBooks.filter(book => {
                // Filter by status (active books)
                if (book.Status !== 1) return false;

                // Filter by search term
                if (searchTerm &&
                    !removeDiacritics(book.Name.toLowerCase()).includes(removeDiacritics(searchTerm))
                ) {
                    return false;
                }

                // Filter by category
                if (selectedCategory && book.CategoryID != selectedCategory) {
                    return false;
                }

                // Filter by price
                const bookPrice = parseFloat((book.Price));
                if (bookPrice < minPrice || bookPrice > currentMaxPrice) {
                    return false;
                }
                return true;
            });

            // Sort books
            switch (sortBy) {
                case 'price-asc':
                    filteredBooks.sort((a, b) =>
                        parseFloat((a.Price)) -
                        parseFloat((b.Price)));
                    break;

                case 'price-desc':
                    filteredBooks.sort((a, b) =>
                        parseFloat((b.Price)) -
                        parseFloat((a.Price)));
                    break;

                case 'name-asc':
                    filteredBooks.sort((a, b) => a.Name.localeCompare(b.Name));
                    break;

                case 'name-desc':
                    filteredBooks.sort((a, b) => b.Name.localeCompare(a.Name));
                    break;

                default:
                    break;
            }

            // Show/hide clear filters button
            const clearFiltersBtn = document.getElementById('clear-filters');
            const hasActiveFilters = searchTerm ||
                selectedCategory ||
                minPrice > 0 ||
                currentMaxPrice < maxPrice ||
                sortBy !== 'default';

            clearFiltersBtn.style.display = hasActiveFilters ? 'inline-block' : 'none';

            document.getElementById('result-count').textContent = `${filteredBooks.length} sản phẩm`;

            // Reinitialize pagination
            initPagination();
        }

        // Initialize pagination
        function initPagination() {
            $('#shop-pagination-container').pagination({
                dataSource: filteredBooks,
                pageSize: booksPerPage,
                hideOnlyOnePage: true,
                autoHidePrevious: true,
                autoHideNext: true,
                prevText: '<i class="fas fa-chevron-left"></i>',
                nextText: '<i class="fas fa-chevron-right"></i>',
                pageRange: 2,
                callback: function (data, pagination) {
                    // Render HTML
                    let html = '';

                    if (data.length === 0) {
                        html = `
                    <div class="col-12 py-5 text-center no-results-container">
                        <div class="no-results">
                            <img src="../../img/product-not-found.png" alt="Không tìm thấy sản phẩm" class="img-fluid mb-4 no-results-img">
                            <h3>Oops! Không tìm thấy cuốn sách nào phù hợp!</h3>
                            <p class="text-muted">Chúng tôi đã tìm khắp kệ mà vẫn không thấy!</p>
                            <p class="mt-2 fun-quote">Hãy kiểm tra lại từ khóa hoặc thử tìm cách khác nhé!</p>
                        </div>
                    </div>
                `;
                    } else {
                        data.forEach(book => {
                            html += createBookCard(book);
                        });
                    }

                    $('#shop-products-container').html(html);

                    // Scroll to shop section when changing pages
                    if (pagination.pageNumber >= 1 && !isInitialShopPageLoad) {
                        $('html, body').animate({
                            scrollTop: $('.shop-controls').offset().top - 120
                        }, 200);
                        // Update the flag after the first page load
                    }
                    isInitialShopPageLoad = false;
                }
            });
        }

        // Cập nhật giá trị slider khi thay đổi giá trị input
        const updateSlider = (minValue, maxValue) => {
            if (priceSlider && priceSlider.noUiSlider) {
                priceSlider.noUiSlider.set([minValue, maxValue]);
            }
        };

        // Apply filters button
        document.getElementById('apply-filter').addEventListener('click', function () {
            fetchLatestProductData().then(() => {
                applyFilters();
            });
        });

        document.getElementById('search-term').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                fetchLatestProductData().then(() => {
                    applyFilters();
                });
            }
        });

        // Apply filter when inputs change
        document.getElementById('min-price').addEventListener('change', function () {
            const minValue = minPriceInput.getNumber() || 0;
            const maxValue = maxPriceInput.getNumber() || maxPrice;
            updateSlider(minValue, maxValue);
        });

        document.getElementById('max-price').addEventListener('change', function () {
            const minValue = minPriceInput.getNumber() || 0;
            const maxValue = maxPriceInput.getNumber() || maxPrice;
            updateSlider(minValue, maxValue);
        });

        // Clear filters button
        document.getElementById('clear-filters').addEventListener('click', function () {
            fetchLatestProductData().then(() => {
                // Reset filter values
                document.getElementById('search-term').value = '';
                document.getElementById('category-filter').value = '';
                document.getElementById('sort-by').value = 'default';
                minPriceInput.set(0);
                maxPriceInput.set(maxPrice);

                // Reset slider
                priceSlider.noUiSlider.set([0, maxPrice]);

                // Apply filters
                applyFilters();
            });
        });

        // Apply initial filter
        applyFilters();

        // Fetch latest product data after initial load (không phải chờ người dùng click)
        /*         setTimeout(() => {
                    fetchLatestProductData();
                }, 1000); // Đợi 1 giây sau khi trang đã được hiển thị để không làm gián đoạn UX */
    }
});

// Add event listener for logout button - Modified to use standard link
$(document).on('click', '#logoutBtn', function (e) {
    e.preventDefault();

    // Show confirmation dialog
    showSweetAlert('Bạn có chắc chắn muốn đăng xuất?', {
        icon: 'question',
        title: 'Đăng xuất',
        showCancelButton: true,
        confirmButtonText: 'Đăng xuất',
        cancelButtonText: 'Hủy',
    }).then((result) => {
        if (result.isConfirmed) {
            // Before logout, sync cart to server
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                try {
                    const parsedCart = JSON.parse(savedCart);
                    if (Array.isArray(parsedCart) && parsedCart.length > 0) {
                        // Convert cart items to session format
                        const sessionCart = parsedCart.map(item => {
                            const book = allBooks.find(b => b.BookID == item.id);
                            if (book) {
                                return {
                                    product_id: item.id,
                                    quantity: item.quantity,
                                    price: parseFloat((book.Price).toLocaleString('vi-VN'))
                                };
                            }
                            return null;
                        }).filter(item => item !== null);

                        // Sync to server before redirecting
                        fetch('/sync-cart', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                cart: sessionCart
                            })
                        }).finally(() => {
                            // Clear the cart in localStorage
                            localStorage.removeItem('cart');
                            // Redirect to logout route
                            window.location.href = '/logout';
                        });
                    } else {
                        // Cart is empty, just redirect
                        window.location.href = '/logout';
                    }
                } catch (error) {
                    console.error('Error parsing cart data:', error);
                    window.location.href = '/logout';
                }
            } else {
                // No cart in localStorage, just redirect
                window.location.href = '/logout';
            }
        }
    });
});

// Add event listeners for the hover action buttons
$(document).on('click', '.add-cart-btn', function (e) {
    e.preventDefault();
    const bookId = $(this).data('book-id');
    // Find the book in allBooks array
    const book = allBooks.find(b => b.BookID == bookId);
    if (book) {
        addToCart(book);
    }
});

$(document).on('click', '.buy-now-btn', function (e) {
    e.preventDefault();
    const bookId = $(this).data('book-id');
    // Find the book in allBooks array
    const book = allBooks.find(b => b.BookID == bookId);

    if (book) {
        // setTimeout(() => {
        window.location.href = '/product-detail?id=' + bookId;
        // }, 300);
    }
});

// Cart functionality
$(document).ready(() => {
    // Initialize cart object
    let cart = {
        items: [],
        totalItems: 0,
        totalPrice: 0
    };

    // Initialize cart offcanvas
    const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));

    // Function definitions

    // Function to update cart summary (totals, badges)
    const updateCartSummary = () => {
        // Calculate total items & price
        cart.totalItems = cart.items.reduce((total, item) => total + item.quantity, 0);
        cart.totalPrice = cart.items.reduce((total, item) => total + (item.price * item.quantity), 0);

        // Update UI
        $('.cart-count').text(cart.totalItems);
        $('#cartTotalPrice').text(`${cart.totalPrice.toLocaleString()} đ`);
        $('#cartTotalItems').text(`${cart.totalItems} sản phẩm`);

        // Also update global cart interface
        // updateCartInterface(); // Removing this to avoid circular updates
    };

    // Update a single cart item's quantity and price in the UI
    // without re-rendering the entire cart
    const updateCartItemUI = (itemId, quantity) => {
        const item = cart.items.find(item => item.id === itemId);
        if (!item) return;

        const itemContainer = $(`.cart-item input[data-id="${itemId}"]`).closest('.cart-item');
        if (itemContainer.length === 0) return;

        // Update quantity input
        itemContainer.find('.item-qty').val(quantity);

        // Update item total price
        const itemTotal = item.price * quantity;
        itemContainer.find('.d-flex.justify-content-between .text-danger:last-child').text(`${itemTotal.toLocaleString()} đ`);

        // Update quantity in display
        itemContainer.find('.text-secondary').text(`SL: ${quantity} x`);

        // Update increase button state based on stock
        const book = allBooks.find(b => b.BookID === itemId);
        const maxStock = book ? book.Stock : 99;
        const increaseBtn = itemContainer.find('.increase-qty');

        if (quantity >= maxStock) {
            increaseBtn.prop('disabled', true);
            increaseBtn.css({ 'opacity': '0.25', 'cursor': 'not-allowed' });
        } else {
            increaseBtn.prop('disabled', false);
            increaseBtn.css({ 'opacity': '', 'cursor': '' });
        }

        // Update stock warning message if needed
        const stockWarningContainer = itemContainer.find('small.text-muted');
        if (maxStock < 10) {
            if (stockWarningContainer.length === 0) {
                itemContainer.find('.d-flex.align-items-center').after(
                    `<small class="text-muted mt-1 d-block">Còn ${maxStock} "${item.name}" trong kho</small>`
                );
            } else {
                stockWarningContainer.text(`Còn ${maxStock} "${item.name}" trong kho`);
            }
        } else if (stockWarningContainer.length > 0) {
            stockWarningContainer.remove();
        }
    };

    // Save cart to browser local storage
    const saveCartToLocalStorage = () => {
        // Only save minimal data (id and quantity) to localStorage
        const minimalCart = cart.items.map(item => ({
            id: item.id,
            quantity: item.quantity
        }));

        localStorage.setItem('cart', JSON.stringify(minimalCart));

        // Ensure global cart interface is updated with latest data
        updateCartInterface();
    };

    // Load cart from browser local storage
    const loadCartFromLocalStorage = () => {
        const savedCart = localStorage.getItem('cart');
        if (savedCart) {
            try {
                const parsedData = JSON.parse(savedCart);

                // Check which format the saved cart is in
                if (parsedData && typeof parsedData === 'object') {
                    // Reset cart items
                    cart.items = [];

                    if (Array.isArray(parsedData)) {
                        // New format: array of {id, quantity}
                        parsedData.forEach(item => {
                            // Find book in allBooks array
                            const book = allBooks.find(b => b.BookID == item.id);

                            if (book) {
                                // Add to cart with saved quantity
                                cart.items.push({
                                    id: book.BookID,
                                    name: book.Name,
                                    price: parseFloat((book.Price).toLocaleString('vi-VN')),
                                    quantity: item.quantity,
                                    image: book.ImageURL
                                });
                            }
                        });
                    } else if (Array.isArray(parsedData.items)) {

                        parsedData.items.forEach(item => {
                            // Verify item exists in current books
                            const book = allBooks.find(b => b.BookID == item.id);

                            if (book) {
                                // Add to cart with saved data but updated book info
                                cart.items.push({
                                    id: book.BookID,
                                    name: book.Name,
                                    price: parseFloat((book.Price).toLocaleString('vi-VN')),
                                    quantity: item.quantity,
                                    image: book.ImageURL
                                });
                            }
                        });

                        // Save in new format immediately
                        saveCartToLocalStorage();
                    }

                    // Update cart summary
                    updateCartSummary();
                }
            } catch (error) {
                console.error('Error parsing cart from localStorage:', error);
                // Reset cart and localStorage on error
                cart.items = [];
                localStorage.removeItem('cart');
            }
        }
    };

    // Function to render cart items
    const renderCartItems = () => {
        let cartItemsHtml = '';

        if (cart.items.length === 0) {
            // Empty cart view with image
            cartItemsHtml = $('#emptyCartTemplate').html();
            // Hide cart summary
            $('#cartSummary').hide();
        } else {
            // Show cart summary
            $('#cartSummary').show();

            cart.items.forEach(item => {
                // Find book in allBooks to get current stock
                const book = allBooks.find(b => b.BookID === item.id);
                const maxStock = book ? book.Stock : 99; // Fallback to 99 if book not found

                cartItemsHtml += `
                    <div class="card border-0 rounded-0 border-bottom cart-item">
                        <div class="card-body p-3">
                            <div class="d-flex">
                                <a href="/product-detail?id=${item.id}" class="cart-item-img-link">
                                    <img src="${item.image}" alt="${item.name}" class="cart-item-img me-3">
                                </a>
                                <div class="flex-grow-1">
                                    <a href="/product-detail?id=${item.id}" class="cart-item-name">
                                        <h6 class="card-title mb-1">${item.name}</h6>
                                    </a>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="text-danger fw-bold" style="font-size: 16px;">
                                            <span class="text-secondary me-2">SL: ${item.quantity} x</span>${item.price.toLocaleString()} đ
                                        </div>
                                        <div class="text-danger fw-bold" style="font-size: 16px;">
                                            ${(item.price * item.quantity).toLocaleString()} đ
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <button class="btn btn-outline-danger decrease-qty" type="button" data-id="${item.id}">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control text-center item-qty" value="${item.quantity}" 
                                                   data-id="${item.id}" min="1" max="${maxStock}" 
                                                   style="border-color: #dc3545 !important; box-shadow: none !important;">
                                            <button class="btn btn-outline-danger increase-qty" type="button" data-id="${item.id}" 
                                                   ${item.quantity >= maxStock ? 'disabled' : ''} 
                                                   style="${item.quantity >= maxStock ? 'opacity: 0.25; cursor: not-allowed;' : ''}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <button class="btn btn-sm text-danger remove-item ms-3" data-id="${item.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    ${maxStock < 10 ? `<small class="text-muted mt-1 d-block">Còn ${maxStock} "${item.name}" trong kho</small>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        $('#cartItems').html(cartItemsHtml);
    };

    // Function to remove items from cart
    const removeFromCart = (itemId) => {
        const itemIndex = cart.items.findIndex(item => item.id === itemId);
        if (itemIndex !== -1) {
            const item = cart.items[itemIndex];
            cart.items.splice(itemIndex, 1);
            updateCartSummary();
            renderCartItems(); // We need to re-render the entire cart when removing items
            saveCartToLocalStorage();
            showToast(`Đã xóa "${item.name}" khỏi giỏ hàng`, {
                type: 'success',
                title: 'Giỏ hàng'
            });
        }
    };

    // Function to update item quantity
    const updateItemQuantity = (itemId, change) => {
        const item = cart.items.find(item => item.id === itemId);
        if (item) {
            const book = allBooks.find(b => b.BookID === itemId);
            const maxStock = book ? book.Stock : 99;

            const newQty = item.quantity + change;
            if (newQty > 0 && newQty <= maxStock) {
                item.quantity = newQty;
                updateCartSummary();
                updateCartItemUI(itemId, newQty); // Update only the relevant parts of the UI
                saveCartToLocalStorage();
            } else if (newQty > maxStock) {
                showToast(`Chỉ còn ${maxStock} "${book ? book.Name : 'sản phẩm này'}" trong kho`, {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
            } else if (newQty <= 0) {
                removeFromCart(itemId);
            }
        }
    };

    // Function to set item quantity directly
    const setItemQuantity = (itemId, quantity) => {
        const item = cart.items.find(item => item.id === itemId);
        if (item) {
            const book = allBooks.find(b => b.BookID === itemId);
            const maxStock = book ? book.Stock : 99;

            // Handle invalid quantity input (empty, NaN, or contains non-numeric characters)
            if (quantity === '' || isNaN(quantity) || !/^\d+$/.test(String(quantity))) {
                // Reset to current quantity in data model
                updateCartItemUI(itemId, item.quantity);
                showToast('Vui lòng nhập số lượng hợp lệ', {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
                return;
            }

            // Convert to integer if it's a valid number string
            quantity = parseInt(quantity);

            // Handle zero stock case
            if (maxStock <= 0) {
                removeFromCart(itemId);
                showToast(`Sản phẩm "${book ? book.Name : 'này'}" đã hết hàng và đã được xóa khỏi giỏ hàng`, {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
                return;
            }

            if (quantity > 0 && quantity <= maxStock) {
                item.quantity = quantity;
                updateCartSummary();
                updateCartItemUI(itemId, quantity);
                saveCartToLocalStorage();
            } else if (quantity > maxStock) {
                item.quantity = maxStock;
                updateCartSummary();
                updateCartItemUI(itemId, maxStock);
                saveCartToLocalStorage();
                showToast(`Chỉ còn ${maxStock} "${book ? book.Name : 'sản phẩm này'}" trong kho`, {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
            } else if (quantity <= 0) {
                removeFromCart(itemId);
            }
        }
    };

    // Function to add items to cart - global scope for accessibility from other functions
    window.addToCart = (book, skipNotification = false) => {
        // Check if item already exists in cart
        const existingItem = cart.items.find(item => item.id === book.BookID);

        // Check current stock
        const currentStock = book.Stock || 0;

        // Explicitly check for zero stock
        if (currentStock <= 0) {
            if (!skipNotification) {
                showToast(`Rất tiếc, sách "${book.Name}" đã hết hàng`, {
                    type: 'error',
                    title: 'Giỏ hàng'
                });
            }
            return false; // Item was not added
        }

        if (existingItem) {
            // Check if adding one more would exceed stock
            if (existingItem.quantity >= currentStock) {
                if (!skipNotification) {
                    showToast(`Đã đạt giới hạn tồn kho của sách "${book.Name}"`, {
                        type: 'error',
                        title: 'Giỏ hàng'
                    });
                }
                return false; // Item was not added
            }
            existingItem.quantity++;

            // If the cart is open, update the item in the UI
            if ($('#cartOffcanvas').hasClass('show')) {
                updateCartItemUI(book.BookID, existingItem.quantity);
            }
        } else {
            cart.items.push({
                id: book.BookID,
                name: book.Name,
                price: parseFloat((book.Price).toLocaleString('vi-VN')),
                quantity: 1,
                image: book.ImageURL
            });

            // If cart is open, we need to re-render to show the new item
            if ($('#cartOffcanvas').hasClass('show')) {
                renderCartItems();
            }
        }

        // Update cart summary and badge
        updateCartSummary();

        // Save cart to localStorage
        saveCartToLocalStorage();

        // Show notification unless skipNotification is true
        if (!skipNotification) {
            showToast(`Đã thêm "${book.Name}" vào giỏ hàng`, {
                type: 'success',
                title: 'Giỏ hàng',
            });
        }

        return true; // Item was successfully added
    };

    // Load cart from localStorage on page load
    loadCartFromLocalStorage();

    // Show cart when clicking cart button
    $('.cart-btn').on('click', (e) => {
        e.preventDefault();
        renderCartItems();
        cartOffcanvas.show();
    });

    // Event delegation for cart item controls
    $(document).on('click', '.remove-item', function () {
        const itemId = parseInt($(this).data('id'));
        removeFromCart(itemId);
    });

    // Add quantity control event handlers with event delegation
    $(document).on('click', '.increase-qty', function () {
        const itemId = parseInt($(this).data('id'));
        updateItemQuantity(itemId, 1);
    });

    $(document).on('click', '.decrease-qty', function () {
        const itemId = parseInt($(this).data('id'));
        updateItemQuantity(itemId, -1);
    });

    $(document).on('change', '.item-qty', function () {
        const itemId = parseInt($(this).data('id'));
        const newQty = $(this).val(); // Get raw value to handle empty or non-numeric inputs
        // const maxStock = parseInt($(this).attr('max'));

        // Handle quantity changes - setItemQuantity will now validate the input
        setItemQuantity(itemId, newQty);
    });

    // Handle checkout button click
    $('#checkoutBtn').on('click', () => {
        // Redirect to checkout page
        window.location.href = 'checkout.php';
    });

    // Check stock levels of items in cart on page load
    // This ensures out-of-stock items are removed from cart
    const checkStockLevels = () => {
        let removedItems = [];

        [...cart.items].forEach(item => {
            const book = allBooks.find(b => b.BookID === item.id);

            if (!book || book.Stock <= 0) {
                // Remove item from cart
                const itemIndex = cart.items.findIndex(i => i.id === item.id);
                if (itemIndex !== -1) {
                    const removedItem = cart.items.splice(itemIndex, 1)[0];
                    removedItems.push(removedItem.name);
                }
            } else if (book && item.quantity > book.Stock) {
                // Adjust quantity if it exceeds current stock
                item.quantity = book.Stock;
            }
        });

        // Update cart if any items were removed
        if (removedItems.length > 0) {
            updateCartSummary();
            saveCartToLocalStorage();

            if (removedItems.length === 1) {
                showToast(`Sản phẩm "${removedItems[0]}" đã hết hàng và đã được xóa khỏi giỏ hàng`, {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
            } else if (removedItems.length > 1) {
                showToast(`${removedItems.length} sản phẩm đã hết hàng và đã được xóa khỏi giỏ hàng`, {
                    type: 'warning',
                    title: 'Giỏ hàng'
                });
            }
        }
    };

    // Check stock levels after loading cart
    checkStockLevels();
});

/**
 * Cập nhật giỏ hàng và số hiển thị(cartbadge) dựa trên dữ liệu lưu trữ cục bộ
 */
window.updateCartInterface = () => {
    const savedCart = localStorage.getItem('cart');
    let cartCount = 0;

    try {
        const cart = JSON.parse(savedCart);
        const items = Array.isArray(cart) ? cart : cart?.items;
        if (Array.isArray(items)) {
            cartCount = items.reduce((sum, item) => sum + item.quantity, 0);
        }
    } catch (e) {
        console.error('Cart parse error:', e);
    }

    document.querySelectorAll('.cart-count').forEach(badge => {
        badge.textContent = cartCount;
        badge.classList.add('badge-animated');
        badge.addEventListener('animationend', function handler() {
            badge.classList.remove('badge-animated');
            badge.removeEventListener('animationend', handler);
        });
    });
};


