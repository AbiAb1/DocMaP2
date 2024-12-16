# Use the official PHP image with Apache support
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd pdo_mysql mbstring zip mysqli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Set the working directory to Apache's default web directory
WORKDIR /var/www/html

# Copy all project files into the container’s /var/www/html directory
COPY . .

# Set proper permissions for the web directory
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Install Composer (dependency manager for PHP)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies via Composer (if any)
RUN composer install --no-dev --optimize-autoloader

# Set the DirectoryIndex to login.php to handle root requests
RUN echo 'DirectoryIndex login.php index.php index.html' >> /etc/apache2/apache2.conf

# Expose the container’s port 80 (Apache default port)
EXPOSE 80

# Start Apache server when the container is run
CMD ["apache2-foreground"]
