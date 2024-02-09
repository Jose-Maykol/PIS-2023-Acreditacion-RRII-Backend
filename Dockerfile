FROM php:8.1-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpq-dev \
    libssl-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    libpng-dev \
    zip \
    unzip

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP 
RUN docker-php-ext-install pdo_pgsql pgsql gd zip

# Instalar composer
COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/backend

COPY . /var/www/backend/
# Establecer acceso a las carpetas de archivos
RUN chown -R www-data:www-data storage/ bootstrap/cache/


# Instalar dependencias
RUN composer install --no-ansi --no-dev --no-interaction --no-progress --optimize-autoloader

# Exponer el puerto 9000 del contenedor
EXPOSE 9000

# Ejecutar el servicio fpm
CMD ["php-fpm"]


