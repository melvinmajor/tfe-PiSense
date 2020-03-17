# NGINX
FROM nginx

# Set up working directory
WORKDIR /usr/share/nginx

# Configure NGINX
RUN rm /etc/nginx/nginx.conf
RUN rm /etc/nginx/conf.d/default.conf
COPY conf/nginx.conf /etc/nginx/nginx.conf
COPY conf/default.conf /etc/nginx/conf.d/default.conf

# Set up volume environment so that the website can be updated without relaunching Dockerfile
VOLUME ["/usr/share/nginx/html"]

# Expose HTTP and HTTPS ports
EXPOSE 80 443
