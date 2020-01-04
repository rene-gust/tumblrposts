FROM nginx:1.13

# start in /var/www/html
RUN echo "cd /var/www/html" > /root/.bashrc

# timezone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# FIX permission for nginx
RUN usermod -u 1000 www-data
RUN sed -i 's/^user.*nginx;/user www-data;/g' /etc/nginx/nginx.conf
