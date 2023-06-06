FROM php:8-fpm

# Set working directory
WORKDIR /var/www/

# Install Additional dependencies
RUN apt-get update \
  && apt-get install -y \
             apt-utils \
             man \
             curl \
             git \
             bash \
             vim \
             zip unzip \
             acl \
             iproute2 \
             dnsutils \
             fonts-freefont-ttf \
             fontconfig \
             dbus \
             openssh-client \
             sendmail \
             libfreetype6-dev \
             libjpeg62-turbo-dev \
             icu-devtools \
             libicu-dev \
             libmcrypt4 \
             libmcrypt-dev \
             libpng-dev \
             zlib1g-dev \
             libxml2-dev \
             libzip-dev \
             libonig-dev \
             graphviz \
             libcurl4-openssl-dev \
             pkg-config \
             libldap2-dev \
             libpq-dev



RUN set -ex \ && apk --no-cache add \ postgresql-dev

# # Add and Enable PHP-PDO Extenstions
RUN docker-php-ext-install pdo pdo_pgsql pgsql
RUN docker-php-ext-enable pdo_pgsql

RUN docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install pdo \
        pgsql pdo_pgsql \
        mysqli pdo_mysql \
        intl iconv mbstring \
        zip pcntl \
        exif opcache \
    && docker-php-source delete

# Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update

# Remove Cache
RUN rm -rf /var/cache/apk/*

# Add UID '1000' to www-data
RUN usermod -u 1000 www-data

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/

# Change current user to www
USER www-data

CMD ["php-fpm"]
