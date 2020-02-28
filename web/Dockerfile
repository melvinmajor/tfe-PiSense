FROM nginx
WORKDIR /usr/share/nginx

RUN rm /etc/nginx/nginx.conf
COPY conf/nginx.conf /etc/nginx/nginx.conf

VOLUME ["/usr/share/nginx/html"]

EXPOSE 80 443
