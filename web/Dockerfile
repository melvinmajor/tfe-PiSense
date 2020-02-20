FROM nginx:alpine

RUN rm /etc/nginx/conf.d/default.conf

# COPY www/ /usr/share/nginx/html

VOLUME /usr/share/nginx/html
VOLUME /etc/nginx

EXPOSE 80 443

CMD ["nginx", "-g", "daemonoff;"]
