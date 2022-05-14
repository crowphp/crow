FROM php:8.1-cli-buster

RUN apt update && apt upgrade -y
RUN apt install zip unzip libzip-dev git nodejs npm -y

RUN npm install nodemon -g
# Install PHP extensions
RUN pecl install swoole \
    && pecl install zip \
    && docker-php-ext-enable swoole zip

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


WORKDIR /usr/src/crowphp

ARG USER_ID
ARG GROUP_ID
ARG UNAME=crowphp
ARG UID="$USER_ID"
ARG GID="$GROUP_ID"
RUN groupadd -g $GID -o $UNAME
RUN useradd -m -u $UID -g $GID -o -s /bin/bash $UNAME

ADD entrypoint.sh /opt

RUN chmod +x /opt/entrypoint.sh

CMD [ "/opt/entrypoint.sh" ]
USER crowphp
