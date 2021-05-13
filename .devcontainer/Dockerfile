FROM mcr.microsoft.com/vscode/devcontainers/php:8.0

RUN docker-php-ext-install pdo pdo_mysql

RUN echo "xdebug.log_level = 0" >> /usr/local/etc/php/conf.d/xdebug.ini
