FROM php:8.1-apache

# Instala certificados de autoridade (CA) para HTTPS funcionar
RUN apt-get update && apt-get install -y ca-certificates && apt-get clean

COPY . /var/www/html/
EXPOSE 80
