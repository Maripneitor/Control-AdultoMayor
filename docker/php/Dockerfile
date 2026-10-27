FROM php:8.2-apache

# Instalar dependencias del sistema requeridas para PostgreSQL, GD y utilidades
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql gd zip bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar módulos de Apache requeridos para VitalSenior (.htaccess, rewrite, cabeceras seguras)
RUN a2enmod rewrite headers

# Configurar VirtualHost con DocumentRoot apuntando a public/
COPY docker/php/vhost.conf /etc/apache2/sites-available/000-default.conf

# Instalar Composer oficial desde imagen multietapa
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Crear directorios para subida de archivos y logs con permisos adecuados
RUN mkdir -p /var/www/html/storage/uploads /var/www/html/storage/temp /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
