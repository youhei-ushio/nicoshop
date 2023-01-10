FROM php:8.1.13-zts-alpine3.17

RUN apk --no-cache add autoconf gcc g++ make
RUN apk add --update linux-headers
RUN pecl install xdebug

RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-zts-20210902/xdebug.so" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.mode=debug"
RUN echo "xdebug.client_host=host.docker.internal"
