FROM php:8.2-apache

# Instalar extensión PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Copiar el proyecto
COPY . /var/www/html/

# Configurar Apache para que el directorio raíz sea el correcto
WORKDIR /var/www/html/