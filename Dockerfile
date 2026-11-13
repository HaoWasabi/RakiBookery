# syntax=docker/dockerfile:1

# ─────────────────────────────────────────────
# Stage 1: builder (cài extension + composer nếu cần)
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

# Nếu dùng Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files trước để tận dụng cache
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

# ─────────────────────────────────────────────
# Stage 2: runtime (gọn nhất)
# ─────────────────────────────────────────────
FROM php:8.2-apache AS runtime

# Chỉ cài runtime libs cần thiết
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4 \
    && docker-php-ext-install -j"$(nproc)" curl pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Copy Apache config
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy source + vendor từ builder
COPY --from=builder /var/www/html/vendor /var/www/html/vendor
COPY src/ /var/www/html/

# Tạo user non-root (giống NeonFoodmap)
RUN groupadd -r appuser && useradd -r -g appuser appuser \
    && chown -R appuser:appuser /var/www/html \
    && chmod -R 775 /var/www/html/public/uploads

# Apache vẫn cần root để bind port 80, nhưng PHP-FPM/process chạy non-root nếu cấu hình thêm.
# Với pure Apache image, giữ root là phổ biến, nhưng có thể chuyển sang php-fpm + nginx nếu muốn non-root hoàn toàn.

WORKDIR /var/www/html/public

EXPOSE 80

# Healthcheck (rất hữu ích cho ECS/ECR)
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost/ || exit 1

CMD ["apache2-foreground"]