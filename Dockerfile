FROM php:8.2-apache

# キャッシュ無効化用ダミー変数
ARG CACHEBUST=1

# 必要な拡張をインストール
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Apache の MPM 設定を完全リセット
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load && \
    a2dismod mpm_event && a2dismod mpm_worker && \
    a2enmod mpm_prefork && a2enmod rewrite

# プロジェクトファイルをコピー
COPY . /var/www/html

WORKDIR /var/www/html

# Composer をインストール
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
