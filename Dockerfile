# Gunakan base image resmi PHP dengan Apache
FROM php:8.2-apache

# 1. Instal ekstensi PHP yang diperlukan
# PDO dan PDO MySQL diperlukan untuk koneksi database
RUN docker-php-ext-install pdo pdo_mysql

# 2. Aktifkan modul rewrite Apache untuk .htaccess
RUN a2enmod rewrite

# 3. Atur direktori kerja
WORKDIR /var/www/html

# 4. Salin semua file dari direktori proyek ke dalam direktori web root kontainer
COPY . .

# 5. Buat direktori untuk unggahan dan berikan izin yang benar
# Apache berjalan sebagai user www-data
RUN mkdir -p /var/www/html/uploads/barang && \
    chown -R www-data:www-data /var/www/html/uploads

# 6. Expose port 80 untuk server web
EXPOSE 80
