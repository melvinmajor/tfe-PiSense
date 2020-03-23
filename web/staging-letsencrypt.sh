#!/bin/bash

docker run -it --rm \
    -v /docker-volumes/etc/letsencrypt:/etc/letsencrypt \
    -v /docker-volumes/var/lib/letsencrypt:/var/lib/letsencrypt \
    -v ~/git/tfe-PiSense/web/www:/data/letsencrypt \
    -v "/docker-volumes/var/log/letsencrypt:/var/log/letsencrypt" \
    certbot/certbot \
    certonly --webroot \
    --register-unsafely-without-email --agree-tos \
    --webroot-path=/data/letsencrypt \
    --staging \
    -d camposcasares.be -d www.camposcasares.be
