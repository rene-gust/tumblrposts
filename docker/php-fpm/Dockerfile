FROM ubuntu:16.04

RUN apt-get update && \
    apt-get install --no-install-recommends -y \
        unzip \
        curl \
        php-fpm \
        php-apcu \
        php-ctype \
        php-curl \
        php-dom \
        php-fileinfo \
        php-ftp \
        php-iconv \
        php-gd \
        php-json \
        php-mbstring \
        php-mysql \
        php-mysqli \
        php-mysqlnd \
        php-pdo \
        php-pear \
        php-phar \
        php-posix \
        php-simplexml \
        php-soap \
        php-tokenizer \
        php-xml \
        php-xmlreader \
        php-xmlwriter \
        php-zip \
        php-xdebug \
        php-sqlite3 \
        php-redis \
        composer \
        vim \
        wget \
        openjdk-8-jdk \
        ant \
        git \
        ssh \
        telnet

RUN echo 'deb http://s3-eu-west-1.amazonaws.com/tideways/packages debian main' > /etc/apt/sources.list.d/tideways.list && \
    wget -qO - https://s3-eu-west-1.amazonaws.com/tideways/packages/EEB5E8F4.gpg | apt-key add - && \
    apt-get update && \
    apt-get install --no-install-recommends -y \
    tideways-php

# start in /var/www/html
RUN echo "cd /var/www/html" > /root/.bashrc

# Tidy up
RUN apt-get -y autoremove && apt-get clean && apt-get autoclean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# timezone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# FIX permission for PHP
RUN usermod -u 1000 www-data

RUN service php7.0-fpm start

COPY php-fpm.conf /etc/php/7.0/fpm/pool.d/www.conf
COPY usr/local/bin/xdebug /usr/local/bin
COPY usr/local/bin/tideways /usr/local/bin
RUN chmod +x usr/local/bin/xdebug
RUN chmod +x usr/local/bin/tideways
RUN sed -i 's/^display_errors.*Off/display_errors = On/g' /etc/php/7.0/fpm/php.ini
RUN sed -i 's/^display_startup_errors.*Off/display_startup_errors = On/g' /etc/php/7.0/fpm/php.ini

COPY etc/php/7.0/mods-available/xdebug.ini /etc/php/7.0/mods-available/xdebug.ini
COPY etc/php/7.0/mods-available/apcu.ini /etc/php/7.0/mods-available/apcu.ini
COPY etc/php/7.0/mods-available/tideways.ini /etc/php/7.0/mods-available/tideways.ini

RUN if [ -f "etc/hosts" ] ; then cat etc/hosts > /etc/hosts ; fi

CMD ["/usr/sbin/php-fpm7.0"]