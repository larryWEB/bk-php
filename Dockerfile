# Use an official PHP image
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Copy files from GitHub repo to container
COPY . /var/www/html/

# Expose port 80
EXPOSE 80

# Start Apache server
CMD ["apache2-foreground"]
