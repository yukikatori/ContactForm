# PHP 8.2 + Apache
FROM php:8.2-apache

# 必要な拡張をインストール
RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Apache の rewrite モジュールを有効化
RUN a2dismod mpm_event && a2enmod mpm_prefork && a2enmod rewrite

# プロジェクトファイルをコピー
COPY . /var/www/html

# 作業ディレクトリ設定
WORKDIR /var/www/html

# Composer をインストール
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Composer 実行時に root 警告を無視
ENV COMPOSER_ALLOW_SUPERUSER=1

# 依存関係をインストール（本番用）
RUN composer install --no-dev --optimize-autoloader

# 権限設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ポート公開
EXPOSE 80

# Apache 起動
CMD ["apache2-foreground"]
