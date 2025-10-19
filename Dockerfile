# Use the official PHP image with Apache
FROM php:8.2-apache

# Set the working directory to the web server root
WORKDIR /var/www/html

# Copy all project files from the current directory to the container
COPY . /var/www/html/

# (Optional) Ensure correct permissions for data and other writable directories if needed
# RUN chown -R www-data:www-data /var/www/html/data

# Expose port 80 for the web server
EXPOSE 80
