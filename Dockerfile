# Dockerfile
FROM php:8.2-apache

# Extensions dont tu as besoin
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite
RUN a2enmod rewrite

# Définir le DocumentRoot sur /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
 && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT%/public/}/!g' /etc/apache2/apache2.conf

# (Optionnel) copier un php.ini de dev
# COPY ./docker/php.ini /usr/local/etc/php/conf.d/dev.ini


