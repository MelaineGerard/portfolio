FROM debian:bullseye
LABEL maintainer="Melaine Gérard <pro@melaine-gerard.fr>"
LABEL version="0.1"

WORKDIR /var/www/portfolio
RUN sed -i "s/^exit 101$/exit 0/" /usr/sbin/policy-rc.d

COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf
COPY ./docker/nginx/vhost.conf /etc/nginx/sites-available/default.conf

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt update && apt -yq install wget curl lsb-release apt-transport-https ca-certificates software-properties-common
RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list && \
    apt update
RUN DEBIAN_FRONTEND=noninteractive apt -yq install \
tzdata \
nginx \
nano \
vim \
supervisor \
    php8.2 \
    php8.2-common \
    php8.2-cli \
    php8.2-gd \
    php8.2-mysql \
    php8.2-mbstring \
    php8.2-bcmath \
    php8.2-xml \
    php8.2-fpm \
    php8.2-curl \
    php8.2-zip \
    nodejs \
        && rm -rf /var/lib/apt/lists/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
php composer-setup.php --install-dir=/usr/bin --filename=composer && \
php -r "unlink('composer-setup.php');" && \
ln -s /etc/php/8.2/fpm/php.ini /etc/php/8.2/fpm/conf.d/php.ini

# Configuration de NGINX
RUN mkdir -p /etc/nginx && \
mkdir -p /etc/nginx/sites-available && \
mkdir -p /etc/nginx/sites-enabled && \
mkdir -p /run/nginx && \
ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default.conf && \
mkdir -p /var/log/supervisor

# Configurationd de PHP-FPM
RUN sed -i -e "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" \
-e "s/variables_order = \"GPCS\"/variables_order = \"EGPCS\"/g" \
/etc/php/8.2/fpm/php.ini && \
sed -i -e "s/;daemonize\s*=\s*yes/daemonize = no/g" \
-e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" \
-e "s/user = nobody/user = nginx/g" \
-e "s/group = nobody/group = nginx/g" \
-e "s/;listen.mode = 0660/listen.mode = 0666/g" \
-e "s/;listen.owner = nobody/listen.owner = nginx/g" \
-e "s/;listen.group = nobody/listen.group = nginx/g" \
-e "s/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/g" \
-e "s/^;clear_env = no$/clear_env = no/" \
/etc/php/8.2/fpm/php-fpm.conf

COPY . .

RUN chown -R nginx:nginx /var/www/portfolio/

RUN composer install

VOLUME [ "/var/log/nginx" ]

RUN chown -R www-data:www-data /var/www/portfolio/

ENV TZ Europe/Paris
EXPOSE 443 80
CMD [ "/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf" ]