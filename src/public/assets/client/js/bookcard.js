export const createBookCard = (book) => {
    const isOutOfStock = book.Stock === 0;

    return `
        <div class="col-md-3 col-sm-6" style="margin-bottom: 20px;">
            <div class="book-card ${isOutOfStock ? 'out-of-stock' : ''}">
                ${isOutOfStock ? '<div class="out-of-stock-label">T&#7841;m h&#7871;t h&#224;ng</div>' : ''}
                <div class="book-img-container">
                    <a href="/product-detail?id=${encodeURIComponent(book.BookID)}" title="${book.Name}">
                        <img src="${book.ImageURL || '/img/img-not-available.png'}" class="book-img" alt="${book.Name}" loading="lazy">
                    </a>
                    ${isOutOfStock ? '<div class="book-img-overlay"></div>' : ''}
                    </div>
                <div class="book-info">
                    <h3 class="book-title">
                        <a href="/product-detail?id=${book.BookID}" title="${book.Name}">${book.Name}</a>
                    </h3>
                    <p class="book-category">
                        <a href="/shop?category=${encodeURIComponent(book.Category)}" class="category-link">${book.Category}</a>
                    </p>
                    <p class="book-author">${book.Author}</p>
                    <p class="book-price">${(parseFloat(book.Price)).toLocaleString('vi-VN')} &#8363;</p>

                    <!-- Hover action buttons -->
                    <div class="book-hover-actions">
                        <button class="action-btn buy-now-btn" data-book-id="${book.BookID}" ${isOutOfStock ? 'disabled' : ''} title="Xem chi ti&#7871;t">
                            <span class="btn-icon"><i class="fa-solid fa-eye"></i></span>
                            <span class="btn-text">Xem chi ti&#7871;t</span>
                        </button>
                        <button class="action-btn add-cart-btn" data-book-id="${book.BookID}" ${isOutOfStock ? 'disabled' : ''} title="Th&#234;m v&#224;o gi&#7887; h&#224;ng">
                            <span class="btn-icon"><i class="fas fa-cart-plus"></i></span>
                            <span class="btn-text">Th&#234;m v&#224;o gi&#7887;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
};
