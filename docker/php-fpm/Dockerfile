# syntax=registry2.devbanki.ru/public/docker/dockerfile:1.3.1
FROM registry2.devbanki.ru/images/php-8.1-fpm-alpine-dev:3.2.2

ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USER=www-data
ARG GROUP=www-data

ENV PHP_FPM_REQUEST_TERMINATE_TIMEOUT="0"

WORKDIR /var/www

RUN set -xe \
    && usermod -u $USER_ID $USER && groupmod -g $GROUP_ID $GROUP

COPY docker/php-fpm/php-override.ini "$PHP_INI_DIR/conf.d/php-override.ini"
COPY docker/php-fpm/entrypoint.sh /
COPY --chown=$USER . .

RUN chmod +x /entrypoint.sh

RUN set -xe \
    && chown -R $USER:$USER .

RUN  set -xe \
     && mkdir -p ~/.ssh \
     && ssh-keyscan -p 7999 git.banki.ru >> ~/.ssh/known_hosts

USER $USER

RUN  set -xe \
     && mkdir -p ~/.ssh \
     && ssh-keyscan -p 7999 git.banki.ru >> ~/.ssh/known_hosts

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php-fpm"]
