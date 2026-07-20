# PHP 8.2 + Apache
FROM php:8.2-apache

# 必要な拡張をインストール
RUN docker-php-ext-install pdo pdo_mysql

# Apache の rewrite モジュールを有効化
RUN a2enmod rewrite

# プロジェクトファイルをコピー
COPY . /var/www/html

# 作業ディレクトリ設定
WORKDIR /var/www/html

# Composer をインストール
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# 依存関係をインストール（本番用）
RUN composer install --no-dev --optimize-autoloader

# 権限設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ポート公開
EXPOSE 80

# Apache 起動
CMD ["apache2-foreground"]
