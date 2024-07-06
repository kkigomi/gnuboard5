#!/bin/sh

if [ ! -f "/var/www/html/.env.local" ]; then
    cp /var/www/html/.docker/initfile/.env.local /var/www/html
fi

if [ ! -d "/var/www/html/data" ]; then
    cp -r /var/www/html/.docker/initfile/data /var/www/html
fi

nginx -g 'daemon off;'
