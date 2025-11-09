# Sistema Atlas - Dockerfile PHP 8.2 Apache
# Imagen base oficial de PHP con Apache
FROM php:8.2-apache

# Información del mantenedor
LABEL maintainer="Sistema Atlas"
LABEL description="Contenedor PHP 8.2 con Apache para Sistema Atlas"

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configurar y instalar extensión GD para manejo de imágenes
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Instalar extensiones PDO y PDO MySQL para conexión a base de datos
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite de Apache para URLs amigables
RUN a2enmod rewrite

# Configurar PHP para desarrollo
RUN { \
    echo 'upload_max_filesize = 10M'; \
    echo 'post_max_size = 10M'; \
    echo 'max_execution_time = 300'; \
    echo 'date.timezone = America/Bogota'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Configurar DocumentRoot de Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 80
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]

