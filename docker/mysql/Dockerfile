FROM mysql:8.0

RUN set -xe; \
    { \
        echo '[mysqld]'; \
        echo 'log_error_verbosity=2'; \
    } | tee /etc/mysql/conf.d/log.cnf; \
