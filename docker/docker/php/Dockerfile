# Використовуємо офіційний образ PHP із FPM
FROM php:8.1-fpm

# Оновлюємо пакети та встановлюємо розширення, які часто потрібні під Laravel / Symfony тощо
RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        zip \
        unzip \
        git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring opcache \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /var/lib/apt/lists/*

# (За бажанням) встановлюємо Composer глобально
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Встановлюємо робочу директорію
WORKDIR /var/www

# Права (якщо потрібно)
RUN chown -R www-data:www-data /var/www

# Expose порт FPM
EXPOSE 9000

# За замовчуванням запускається php-fpm
CMD ["php-fpm"]

