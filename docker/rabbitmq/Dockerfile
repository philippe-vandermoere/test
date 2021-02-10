FROM rabbitmq:3.8-management-alpine

RUN set -xe; \
    echo 'log.console.level = warning' > /etc/rabbitmq/conf.d/log.conf; \
    chown rabbitmq:rabbitmq /etc/rabbitmq/conf.d/log.conf; \
