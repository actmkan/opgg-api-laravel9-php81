FROM ubuntu:21.10

LABEL maintainer="Taylor Otwell"
WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ARG BUILD_ENV

RUN test -n "$BUILD_ENV"

RUN sed -i 's/archive.ubuntu.com/mirror.kakao.com/g' /etc/apt/sources.list

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 nginx \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && apt-key adv --homedir ~/.gnupg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys E5267A6C \
    && apt-key adv --homedir ~/.gnupg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C300EE8C \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu impish main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y php8.1 php8.1-fpm php8.1-cli php8.1-dev \
       php8.1-pgsql php8.1-sqlite3 php8.1-gd \
       php8.1-curl \
       php8.1-imap php8.1-mysql php8.1-mbstring \
       php8.1-xml php8.1-zip php8.1-bcmath php8.1-soap \
       php8.1-intl php8.1-readline \
       php8.1-ldap \
       php8.1-msgpack php8.1-igbinary php8.1-redis php8.1-swoole \
       php8.1-memcached php8.1-pcov php8.1-xdebug \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get update \
    && apt-get install -y mysql-client \
    && apt-get install -y postgresql-client \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.1


RUN rm -rf /etc/nginx/sites-enabled/*
COPY docker-run /usr/local/bin/docker-run
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.1/cli/conf.d/custom.ini
COPY laravel.site /etc/nginx/sites-enabled/laravel
RUN chmod +x /usr/local/bin/docker-run


RUN mkdir -p -m 0600 /root/.ssh \
  && ssh-keyscan github.com >> /root/.ssh/known_hosts

ADD id_rsa /root/.ssh/id_rsa

RUN rm -rf /var/www/html/*
RUN git clone -b master --single-branch git@github.com:actmkan/opgg-api-laravel9-php81.git /var/www/html

RUN chown -R www-data:www-data /var/www/html && chmod -R g+sw /var/www/html

USER www-data
COPY .env.$BUILD_ENV /var/www/html/.env
RUN composer install
RUN chmod -R 775 /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/storage

USER root
EXPOSE 80

ENTRYPOINT ["docker-run"]
