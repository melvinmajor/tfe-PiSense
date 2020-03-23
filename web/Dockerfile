# NGINX
FROM nginx

# Set up working directory
WORKDIR /usr/share/nginx

# Set up volume environment so that the website can be updated without relaunching Dockerfile
VOLUME ["/usr/share/nginx/html"]

# Expose HTTP and HTTPS ports
EXPOSE 80 443
