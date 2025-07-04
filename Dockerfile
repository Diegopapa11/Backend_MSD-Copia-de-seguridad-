# Se utiliza una imagen de PHP 8.2 con apache
FROM php:8.2-apache

# Habilitar los modulos de apache
RUN a2enmod rewrite headers
RUN a2enmod proxy_http

# Instalar las extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    git \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_mysql xml curl opcache

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Habilitar composer allow superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copiar nuestras llaves SSH del directorio de ssh al contenedor
COPY ssh/id_rsa /root/.ssh/id_rsa
RUN chmod 600 /root/.ssh/id_rsa
COPY ssh/id_rsa.pub /root/.ssh/id_rsa.pub
RUN chmod 644 /root/.ssh/id_rsa.pub

# Configurar el known_hosts para evitar problemas de autenticación
RUN ssh-keyscan github.com >> /root/.ssh/known_hosts

# Clonar el repositorio de Laravel por SSH
RUN git clone git@github.com:ASolisEk/ringmastermx.git /var/www/html \
    && git config --global --add safe.directory /var/www/html \
    && cd /var/www/html \
    && git checkout main \
    && git pull \
    && composer install \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chown -R www-data:www-data /var/www/html/storage

# Install Xdebug 3.3.2
RUN pecl install xdebug-3.3.2 \
    && docker-php-ext-enable xdebug

# Configure Xdebug
RUN echo 'xdebug.mode=debug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.client_port=9003' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.start_with_request=yes' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Crear un archivo de configuración de Apache para laravel
COPY laravel.conf /etc/apache2/sites-available/laravel.conf

# Habilitar el sitio de laravel
RUN a2ensite laravel.conf

#exportar el puerto 80
EXPOSE 80

# Ejecutar el comando de inicio de apache
CMD ["apache2-foreground"]