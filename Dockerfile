FROM php:8.2-apache

# Apache Port ändern
RUN sed -i 's/80/5000/' /etc/apache2/ports.conf \
 && sed -i 's/:80/:5000/' /etc/apache2/sites-available/000-default.conf

# Upload-Ordner erstellen & Rechte setzen
RUN mkdir /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

# PHP-Dateien kopieren
COPY . /var/www/html

EXPOSE 5000