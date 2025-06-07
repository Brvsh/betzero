FROM php:8.1-apache

# Instala certificados e atualiza as CAs
RUN apt-get update && \
    apt-get install -y ca-certificates curl && \
    update-ca-certificates && \
    apt-get clean

COPY . /var/www/html/
EXPOSE 80
