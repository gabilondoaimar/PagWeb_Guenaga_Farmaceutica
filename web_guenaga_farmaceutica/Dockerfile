# Usa una imagen base con Apache
FROM php:8-apache

# Instalar OpenSSL y habilitar módulos de Apache
RUN apt-get update && apt-get install -y openssl \
    && a2enmod ssl \
    && a2enmod rewrite

# Copiar los archivos de la web al directorio de Apache
COPY ./web /var/www/html/
# Instalar la extensión mysqli de PHP
RUN docker-php-ext-install mysqli
# Copiar los certificados al contenedor en la ruta correcta
COPY ./ssl/cert/farmazeutika_guenaga.crt /etc/ssl/certs/farmazeutika_guenaga.crt
COPY ./ssl/private/farmazeutika_guenaga.key /etc/ssl/private/farmazeutika_guenaga.key

# Configurar el ServerName globalmente


# Copiar el archivo de configuración de Apache
COPY ./apache-config/web.conf /etc/apache2/sites-available/guenaga_web.conf
COPY ./apache-config/ssl.conf /etc/apache2/sites-available/default-ssl.conf

# RUN chmod 644 /etc/ssl/certs/farmazeutika_guenaga.crt && \
    # chmod 600 /etc/ssl/private/farmazeutika_guenaga.key

# Habilitar configuración SSL en Apache
RUN a2ensite guenaga_web.conf
RUN a2dissite 000-default.conf


# Exponer los puertos para HTTP y HTTPS
EXPOSE 80 443
