FROM php:7.4-fpm-alpine
WORKDIR /app

# Install dependencies
RUN apk add --no-cache nginx supervisor python3 py3-pip python3-dev \
    gcc musl-dev linux-headers gfortran build-base pkgconf \
    && docker-php-ext-install pdo pdo_mysql

RUN pip3 install --no-cache-dir numpy pandas matplotlib scikit-learn

# Copy configuration files
COPY deploy/supervisor/supervisord.conf /etc/supervisord.conf
COPY deploy/nginx/nginx.conf /etc/nginx/nginx.conf
COPY deploy/nginx/conf.d/ /etc/nginx/conf.d/

# Copy application files
COPY . /app

# Install Composer
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

# Install ThinkPHP dependencies
RUN composer install

# Set permissions
RUN chown -R nobody:nobody /app \
    && chmod -R 775 /app/runtime

# Expose port
EXPOSE 80

# Start Supervisor
ENTRYPOINT ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]