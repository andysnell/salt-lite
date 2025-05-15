# syntax=docker/dockerfile:1
FROM php:8.4-cli as base
ENV COMPOSER_HOME "/app/build/composer"
ENV PATH "/app/bin:/app/vendor/bin:/app/build/composer/bin:$PATH"
ENV PHP_PEAR_PHP_BIN="php -d error_reporting=E_ALL&~E_DEPRECATED"
ENV SALT_BUILD_STAGE "development"
ENV XDEBUG_MODE "off"
WORKDIR /app

# Create a non-root user to run the application
RUN groupadd --gid 1000 dev && useradd --uid 1000 --gid 1000 --groups www-data --shell /bin/bash dev

# Update the package list and install the latest version of the packages
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get update && apt-get dist-upgrade --yes

# Install system dependencies
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get install --yes --quiet --no-install-recommends \
    curl \
    jq \
    less \
    unzip \
    zip;

# Install PHP Extensions
FROM base AS php-extensions
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get install --yes --quiet --no-install-recommends \
    libgmp-dev \
    libicu-dev \
    libzip-dev \
    zlib1g-dev
RUN --mount=type=tmpfs,target=/tmp/pear <<-EOF
  set -eux
  docker-php-ext-install -j$(nproc) bcmath exif gmp intl pcntl zip
  MAKEFLAGS="-j$(nproc)" pecl install igbinary xdebug
  docker-php-ext-enable igbinary xdebug
  cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
EOF

# The Sodium extension originally compiled with PHP is based on an older version
# of the libsodium library provided by Debian. Since it was compiled as a shared
# extension, we can compile the latest stable version of libsodium from source and
# rebuild the extension.
FROM base AS libsodium
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get install --yes --quiet --no-install-recommends \
    autoconf  \
    automake \
    build-essential \
    git \
    libtool \
    tcc
RUN git clone --branch stable --depth 1 --no-tags  https://github.com/jedisct1/libsodium /usr/src/libsodium
WORKDIR /usr/src/libsodium
RUN <<-EOF
  ./configure
  make -j$(nproc) && make -j$(nproc) check
  make -j$(nproc) install
  docker-php-ext-install -j$(nproc) sodium
EOF

FROM base AS development
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get install --yes --quiet --no-install-recommends git openssh-client
COPY --link --from=php-extensions /usr/lib/x86_64-linux-gnu /usr/lib/x86_64-linux-gnu/
COPY --link --from=php-extensions /usr/include/ /usr/include/
COPY --link --from=php-extensions /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --link --from=php-extensions /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --link --from=php-extensions /usr/local/etc/php/php.ini /usr/local/etc/php/php.ini
COPY --link --from=composer/composer:latest-bin /composer /usr/local/bin/composer
COPY --link --from=libsodium /usr/local/lib/ /usr/local/lib/
COPY --link settings.ini /usr/local/etc/php/conf.d/settings.ini
RUN mkdir -p /app/build/composer && chown -R 1000:1000 /app
USER dev

FROM development as integration
COPY --link --chown=1000:1000 ./ /app
COPY --link --chown=1000:1000 ./.env.dist /app/.env
COPY --link --chown=1000:1000 phpstan.dist.neon /app/phpstan.neon
COPY --link --chown=1000:1000 phpunit.dist.xml /app/phpunit.xml
RUN --mount=type=cache,mode=0777,uid=1000,gid=1000,target=/app/build/composer/cache \
    --mount=type=secret,id=GITHUB_TOKEN,uid=1000,gid=1000,required=true <<-EOF
    set -eux
    composer config --global github-oauth.github.com $(cat /run/secrets/GITHUB_TOKEN)
    composer install --classmap-authoritative
    rm -f /app/build/composer/auth.json
EOF
RUN composer install --classmap-authoritative