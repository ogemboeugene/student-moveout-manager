# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable mod_rewrite and mod_headers for URL rewriting and .htaccess support
RUN a2enmod rewrite headers deflate expires

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli zip

# Set recommended PHP.ini settings for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure PHP settings
RUN echo "upload_max_filesize = 32M" >> $PHP_INI_DIR/php.ini \
    && echo "post_max_size = 32M" >> $PHP_INI_DIR/php.ini \
    && echo "memory_limit = 128M" >> $PHP_INI_DIR/php.ini \
    && echo "max_execution_time = 300" >> $PHP_INI_DIR/php.ini \
    && echo "max_input_vars = 3000" >> $PHP_INI_DIR/php.ini

# Set the document root to the project directory
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Update Apache configuration to point to our document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy application files to the Apache document root
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create a custom Apache configuration for rewrite rules
RUN echo '<Directory /var/www/html>' > /etc/apache2/conf-available/app.conf \
    && echo '    AllowOverride All' >> /etc/apache2/conf-available/app.conf \
    && echo '    Require all granted' >> /etc/apache2/conf-available/app.conf \
    && echo '</Directory>' >> /etc/apache2/conf-available/app.conf \
    && a2enconf app

# Set ServerName to suppress Apache warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
