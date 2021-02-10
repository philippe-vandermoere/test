FROM php:8.0-fpm-alpine as base

# configure alpine
RUN set -xe; \
    apk add --update --no-cache \
        bash \
        bash-completion \
        curl \
        grep \
        jq \
        make \
        ncurses \
        openssl \
    ;

RUN set -xe; \
    apk add --no-cache --virtual .build-deps shadow; \
    mkdir -p /var/www/html; \
    usermod www-data -d /var/www; \
    chown -R www-data:www-data /var/www; \
    apk del --no-network .build-deps;

# configure shell
ENV ENV=/etc/profile

RUN set -xe; \
    { \
        echo "alias ls='ls --color=auto'"; \
        echo "alias ll='ls -lah'"; \
        echo "alias grep='grep --color=auto'"; \
    } | tee /etc/profile.d/aliases.sh; \
    { \
        echo "# prompt colors"; \
        echo 'BLACK="$(tput bold)$(tput setaf 0)"'; \
        echo 'RED="$(tput bold)$(tput setaf 1)"'; \
        echo 'GREEN="$(tput bold)$(tput setaf 2)"'; \
        echo 'YELLOW="$(tput bold)$(tput setaf 3)"'; \
        echo 'BLUE="$(tput bold)$(tput setaf 4)"'; \
        echo 'PURPLE="$(tput bold)$(tput setaf 5)"'; \
        echo 'CYAN="$(tput bold)$(tput setaf 6)"'; \
        echo 'WHITE="$(tput bold)$(tput setaf 7)"'; \
        echo 'RESET="$(tput sgr0)"'; \
        echo ; \
        echo 'user()  {'; \
        echo '  if [[ $(id -u) -eq 0 ]]; then'; \
        echo '      echo -n "${RED}\u"'; \
        echo '  else'; \
        echo '      echo -n "${GREEN}\u"'; \
        echo '  fi'; \
        echo '}'; \
        echo ; \
        echo 'git_prompt() {'; \
        echo '  local branch=$(git symbolic-ref HEAD --short 2> /dev/null)'; \
        echo '  local tag=$(git describe --exact-match --tags HEAD 2> /dev/null)'; \
        echo ; \
        echo '  if [[ ! -z ${branch} ]]; then'; \
        echo '      echo -n "${YELLOW}(${branch})"'; \
        echo '  fi'; \
        echo ; \
        echo '  if [[ ! -z ${tag} ]]; then'; \
        echo '      echo -n "${GREEN}(${tag})"'; \
        echo '  fi'; \
        echo '}'; \
        echo ; \
        echo 'export PS1="$(user)${CYAN}@${BLUE}\h ${PURPLE}\w \$(git_prompt)${RESET}\n\$ "'; \
    } | tee /etc/profile.d/prompt.sh; \
    ln -sf /bin/bash /bin/sh; \
    ln -sf /bin/bash /bin/ash; \
    ln -sf /etc/profile /root/.bashrc; \
    ln -sf /etc/profile /var/www/.bashrc;

RUN set -xe; \
    curl -sSfL https://getcomposer.org/composer-2.phar -o /usr/local/bin/composer; \
    chmod +x /usr/local/bin/composer; \
    curl -sSfL https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions -o /usr/local/bin/install-php-extensions; \
    chmod +x /usr/local/bin/install-php-extensions; \
    install-php-extensions \
        amqp \
        opcache \
        pdo_mysql \
        redis \
    ;

# php-fpm config
ENV PHP_FPM_PM_LOG_LEVEL=warning \
    PHP_FPM_PM_MAX_CHILDREN=5 \
    PHP_FPM_PM_START_SERVER=2 \
    PHP_FPM_PM_MIN_SPARE_SERVER=1 \
    PHP_FPM_PM_MAX_SPARE_SERVER=3 \
    PHP_FPM_PM_STATUS_PATH=/status \
    PHP_FPM_PM_PING_PATH=/ping \
    TIMEZONE=UTC

RUN set -xe; \
    rm -f /usr/local/etc/php-fpm.d/*; \
    { \
        echo '[global]'; \
        echo 'daemonize = no'; \
        echo 'log_level = ${PHP_FPM_PM_LOG_LEVEL}'; \
        echo 'error_log = /proc/self/fd/2;'; \
        echo 'log_limit = 8192'; \
    } | tee /usr/local/etc/php-fpm.d/fpm.conf; \
    { \
        echo '[www]'; \
        echo 'listen = [::]:9000'; \
        echo ; \
        echo 'pm = dynamic'; \
        echo 'pm.max_children = ${PHP_FPM_PM_MAX_CHILDREN}'; \
        echo 'pm.start_servers = ${PHP_FPM_PM_START_SERVER}'; \
        echo 'pm.min_spare_servers = ${PHP_FPM_PM_MIN_SPARE_SERVER}'; \
        echo 'pm.max_spare_servers = ${PHP_FPM_PM_MAX_SPARE_SERVER}'; \
        echo ; \
        echo 'pm.status_path = ${PHP_FPM_PM_STATUS_PATH}'; \
        echo 'ping.path = ${PHP_FPM_PM_PING_PATH}'; \
        echo ; \
        echo 'clear_env = no'; \
        echo 'catch_workers_output = yes'; \
        echo 'decorate_workers_output = no'; \
        echo ; \
        echo 'access.log = /proc/self/fd/2'; \
        echo 'access.format = "%R - %u[%{%FT%TZ}t] ”%m %r%Q%q” %s %f %{mili}d %{kilo}M %C%% %{HTTP_X_REQUEST_ID}e"'; \
    } | tee /usr/local/etc/php-fpm.d/www.conf; \
    { \
        echo '[Date]'; \
        echo 'date.timezone = ${TIMEZONE}'; \
    } | tee "${PHP_INI_DIR}/conf.d/timezone.ini";

WORKDIR /var/www/html

FROM base as dev

# PHP extensions DEV
RUN set -xe; \
    install-php-extensions \
        xdebug \
    ;

# Configuration PHP DEV
RUN set -xe; \
    cp "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/conf.d/php.ini"; \
    { \
        echo 'xdebug.mode = coverage,debug'; \
        echo 'xdebug.start_with_request = trigger'; \
        echo 'xdebug.discover_client_host = yes'; \
    } | tee "${PHP_INI_DIR}/conf.d/xdebug.ini";

ARG UID=1000

RUN set -xe; \
    apk add --no-cache --virtual .build-deps shadow; \
    usermod -u ${UID} www-data; \
    apk del --no-network .build-deps;

USER www-data

FROM base as prod

ENV APP_ENV=prod

# Install php-fpm-healthcheck
RUN set -xe; \
    apk add --update --no-cache fcgi; \
    curl -sSfL https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck -o /usr/local/bin/php-fpm-healthcheck; \
    chmod +x /usr/local/bin/php-fpm-healthcheck;

# Configuration PHP DEV
RUN set -xe; \
    cp "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/conf.d/php.ini"; \
    { \
        echo 'expose_php = no'; \
    } | tee "${PHP_INI_DIR}/conf.d/security.ini"; \
    { \
        echo 'opcache.preload = /var/www/html/config/preload.php'; \
        echo 'opcache.memory_consumption = 256'; \
        echo 'opcache.max_accelerated_files = 20000'; \
        echo 'opcache.validate_timestamps = 0'; \
        echo 'realpath_cache_size = 4096K'; \
        echo 'realpath_cache_ttl = 600'; \
    } | tee "${PHP_INI_DIR}/conf.d/cache.ini";

USER www-data

COPY --chown=www-data:www-data . /var/www/html

RUN set -xe; \
    composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction; \
    bin/console cache:warmup;
