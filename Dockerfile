# syntax=docker/dockerfile:1

# ─────────────────────────────────────────────
# Stage 1: builder (Cài extension, git, unzip và Composer)
# ─────────────────────────────────────────────
FROM php:8.2-apache AS builder

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        git \
        unzip \
    && docker-php-ext-install -j"$(nproc)" curl pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Lấy composer từ image chính thức
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Đặt WORKDIR tại /var/www để vendor nằm đúng gốc /var/www/vendor
WORKDIR /var/www

# Copy file quản lý gói trước để tận dụng cache Docker
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ─────────────────────────────────────────────
# Stage 2: runtime (Môi trường chạy chính thức)
# ─────────────────────────────────────────────
FROM php:8.2-apache AS runtime

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
    && docker-php-ext-install -j"$(nproc)" curl pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Copy cấu hình Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# 1. Copy thư mục vendor vào /var/www/vendor (Đã khớp với BASE_PATH: /var/www trong log)
COPY --from=builder /var/www/vendor /var/www/vendor

# 2. Copy toàn bộ mã nguồn vào /var/www/html/ (hoặc từ src/ nếu project để code trong src/)
COPY src/ /var/www/html/

# Phân quyền cho www-data (user mặc định của Apache)
RUN chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/public/uploads

WORKDIR /var/www/html

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost/ || exit 1

CMD ["apache2-foreground"]