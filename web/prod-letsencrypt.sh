#!/bin/bash

sudo docker run -it --rm \
    -v /docker-volumes/etc/letsencrypt:/etc/letsencrypt \
    -v /docker-volumes/var/lib/letsencrypt:/var/lib/letsencrypt \
    -v /home/mcc/git/tfe-PiSense/web/www:/data/letsencrypt \
    -v "/docker-volumes/var/log/letsencrypt:/var/log/letsencrypt" \
    certbot/certbot \
    certonly --webroot \
    --email m.camposcasares@students.ephec.be --agree-tos --no-eff-email \
    --webroot-path=/data/letsencrypt \
    -d camposcasares.be -d www.camposcasares.be
