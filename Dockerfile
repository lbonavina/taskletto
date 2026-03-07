FROM php:8.4-fpm-alpine

# ── Dependências do sistema ───────────────────────────────────────────────────
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng-dev \
    libzip-dev \
    sqlite-dev \
    oniguruma-dev \
    nodejs \
    npm

# ── Extensões PHP ─────────────────────────────────────────────────────────────
RUN docker-php-ext-install \
    pdo \
    pdo_sqlite \
    pdo_mysql \
    zip \
    mbstring \
    gd

# ── Composer ──────────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
