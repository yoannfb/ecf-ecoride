FROM php:8.1-apache

# Active mod_rewrite (utile pour les .htaccess)
RUN a2enmod rewrite

# Installe les extensions nécessaires (dont PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Définit le bon dossier public pour Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Met à jour la configuration Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Copie tout le projet
COPY . /var/www/html/

# Donne les bons droits
RUN chown -R www-data:www-data /var/www/html

