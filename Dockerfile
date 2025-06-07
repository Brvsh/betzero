FROM php:8.1-apache

# Corrige problema de DNS
RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf

# Instala certificados e atualiza as CAs
RUN apt-get update && \
    apt-get install -y ca-certificates curl && \
    update-ca-certificates && \
    apt-get clean

COPY . /var/www/html/
EXPOSE 80
