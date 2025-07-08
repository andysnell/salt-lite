# syntax=docker/dockerfile:1
FROM php:8.4-fpm AS base
ARG USER_UID=1000
ARG USER_GID=1000
WORKDIR /
SHELL ["/bin/bash", "-c"]
ENV COMPOSER_HOME="/app/build/composer" \
    PATH="/app/bin:/app/vendor/bin:/app/build/composer/bin:$PATH" \
    PHP_PEAR_PHP_BIN="php -d error_reporting=E_ALL&~E_DEPRECATED" \
    SALT_BUILD_STAGE="development" \
    XDEBUG_MODE="off"

# Create a non-root user to run the application
RUN groupadd --gid $USER_GID dev && useradd --uid $USER_UID --gid $USER_GID --groups www-data --shell /bin/bash dev

# Update the package list and install the latest version of the packages
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get update && apt-get dist-upgrade --yes

# Install system dependencies
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get install --yes --quiet --no-install-recommends \
    curl \
    git \
    jq \
    less \
    unzip \
    vim-tiny \
    zip \
  && ln -s /usr/bin/vim.tiny /usr/bin/vim

# Install PHP Extensions
FROM base AS php-extensions
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get install --yes --quiet --no-install-recommends \
    libgmp-dev \
    libicu-dev \
    libzip-dev \
    librabbitmq-dev \
    zlib1g-dev
RUN --mount=type=tmpfs,target=/tmp/pear <<-EOF
  set -eux
  docker-php-ext-install -j$(nproc) bcmath exif gmp intl opcache pcntl pdo_mysql zip
  MAKEFLAGS="-j$(nproc)" pecl install amqp igbinary redis
  docker-php-ext-enable amqp igbinary redis
EOF

FROM base AS php-config
RUN <<-EOF
  set -eux
  cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
  cat <<-EOL >> "$PHP_INI_DIR/php.ini"
    date.timezone = UTC
    error_reporting = E_ALL & ~E_DEPRECATED
    max_execution_time = 600
    memory_limit = 1G
    variables_order = EGPCS
    xdebug.log_level=0
  EOL
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

FROM base AS development-php
ARG GIT_COMMIT="undefined"
ENV GIT_COMMIT=${GIT_COMMIT}
ENV SALT_BUILD_STAGE="development"
WORKDIR /app
# Header files from zlib are needed for the xdebug extension
COPY --link --from=php-extensions /usr/include/ /usr/include/
COPY --link --from=php-extensions /usr/lib/x86_64-linux-gnu /usr/lib/x86_64-linux-gnu/
COPY --link --from=php-extensions /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --link --from=php-extensions /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --link --from=php-config /usr/local/etc/php/php.ini /usr/local/etc/php/php.ini
COPY --link --from=libsodium /usr/local/lib/ /usr/local/lib/
COPY --link --from=composer/composer /usr/bin/composer /usr/local/bin/composer
USER dev

FROM node:alpine AS prettier
ENV NPM_CONFIG_PREFIX=/home/node/.npm-global
ENV PATH=$PATH:/home/node/.npm-global/bin
WORKDIR /app
RUN npm install --global --save-dev --save-exact npm@latest prettier
ENTRYPOINT ["prettier"]
