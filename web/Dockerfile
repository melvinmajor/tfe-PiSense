FROM nginx
WORKDIR /usr/share/nginx

RUN rm /etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf
COPY conf/nginx.conf /etc/nginx/nginx.conf
COPY conf/default.conf /etc/nginx/conf.d/default.conf

VOLUME ["/usr/share/nginx/html"]

EXPOSE 80 443
