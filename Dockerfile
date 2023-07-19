FROM php:8.1-fpm

# Argumentos definidos en docker-compose.yml
#ARG user
#ARG uid

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
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
RUN docker-php-ext-install pdo_pgsql pgsql

# Instalar composer
COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

# Crear usuario para correr comandos Composer y Artisan
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
#RUN mkdir -p /home/$user/.composer && \
 #   chown -R $user:$user /home/$user

# Directorio de trabajo


COPY . /var/www/backend
WORKDIR /var/www/backend/

RUN composer install --no-ansi --no-dev --no-interaction --no-progress --optimize-autoloader
RUN php artisan key:generate
#RUN php artisan migrate
RUN chown -R www-data:www-data storage/ bootstrap/cache/
EXPOSE 9000

#USER $user


