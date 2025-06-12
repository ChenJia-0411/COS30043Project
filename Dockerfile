FROM php:8.1-apache

# Install mysqli extension for MySQL support (if needed)
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite (optional)
RUN a2enmod rewrite

# Copy your project files into the Apache server directory
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/
