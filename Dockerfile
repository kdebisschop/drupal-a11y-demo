FROM php:7.4-cli

RUN DEBIAN_FRONTEND=noninteractive apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get -y install git libfreetype6-dev libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev zlib1g-dev sqlite3 unzip \
  && rm -rf /var/lib/apt/lists/* /var/cache/debconf/*-old \
  && rm -rf /var/log/*.log /var/log/apt/*.log

RUN docker-php-ext-configure gd --with-jpeg

RUN docker-php-ext-install gd
RUN docker-php-ext-install exif
RUN docker-php-ext-enable gd
RUN docker-php-ext-enable exif

# Many Drupal packages are not ready for composer v2.x yet. Install the current
# 1.x version and self-update using the --1 flag to stay up-to-date.
RUN curl -o composer-setup.php https://getcomposer.org/installer \
  && test "$(curl https://composer.github.io/installer.sig)" = "$(php -r "echo hash_file('sha384', 'composer-setup.php');")" \
  && php composer-setup.php --version="1.10.17" --install-dir="/usr/bin" --filename="composer" --quiet \
  && rm composer-setup.php \
  && composer self-update --1

RUN chown www-data:www-data /var/www \
  && chmod g+w /var/www

USER www-data
WORKDIR /var/www
ENV HOME /var/www

COPY --chown=www-data:www-data patches      /var/www/patches
COPY --chown=www-data:www-data scripts      /var/www/scripts
COPY --chown=www-data:www-data composer.json composer.lock    /var/www/
COPY --chown=www-data:www-data package.json package-lock.json /var/www/

RUN COMPOSER_MEMORY_LIMIT=2G composer install --prefer-dist --no-progress --no-suggest

COPY --chown=www-data:www-data web/sites/default/settings.php /var/www/web/sites/default/

ENV PATH "/var/www/vendor/bin:${PATH}"

WORKDIR /var/www/web

RUN drush -n --yes site:install \
  && drush pm:enable devel devel_generate

CMD ["php", "-S", "0.0.0.0:80", ".ht.router.php"]
