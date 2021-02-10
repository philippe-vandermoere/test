.PHONY: .check_docker .check_jq .check_docker_version .docker_compose_version .outside_container .check_configuration .waiting_for_dependencies .waiting_for configure run start stop ps restart remove shell mysql_cli redis_cli logs logs_nginx logs_php logs_consumer logs_mysql logs_rabbitmq logs_redis

JQ_BIN=$(shell which jq)

# docker variables
DOCKER_BIN=$(shell which docker)
DOCKER_REQUIRED_VERSION=19.03.13
DOCKER_VERSION=$(shell $(DOCKER_BIN) version --format json | $(JQ_BIN) -r '.Server.Version')

# docker-compose variables
DOCKER_COMPOSE_BIN=$(shell which docker-compose)
DOCKER_COMPOSE_REQUIRED_VERSION=1.27.4
DOCKER_COMPOSE_VERSION=$(shell $(DOCKER_COMPOSE_BIN) version --short)
DOCKER_COMPOSE_ENV_FILE=docker/.env
DOCKER_COMPOSE_ENV_DIST_FILES=$(shell find docker -name .env*.dist | sort | tr "\n" ' ')
DOCKER_COMPOSE_FILES=$(shell find docker -name docker-compose*.yaml | sort -r | awk '{print "--file "$$1}'| tr "\n" ' ')
DOCKER_COMPOSE_OPTIONS=--project-directory . --env-file $(DOCKER_COMPOSE_ENV_FILE) $(DOCKER_COMPOSE_FILES)

.check_docker:
	if [ "$(DOCKER_BIN)" = "" ]; then \
		make .log level=error message='You must run this command outside the docker container'; \
	fi

.check_jq:
	if [ "$(JQ_BIN)" = "" ]; then \
		make .log level=error message='You must install jq run `sudo apt install jq`'; \
	fi

.check_docker_version: .check_jq
	version=$$(printf '%s\n' $(DOCKER_VERSION) $(DOCKER_REQUIRED_VERSION) | sort -V | head -n1); \
	if [ "$(DOCKER_VERSION)" != "$(DOCKER_REQUIRED_VERSION)" ] \
	&& [ "$${version}" = $(DOCKER_VERSION) ]; then \
		make .log level=error message="You must have docker $(DOCKER_REQUIRED_VERSION)"; \
	fi

.docker_compose_version:
	version=$$(printf '%s\n' $(DOCKER_COMPOSE_VERSION) $(DOCKER_COMPOSE_REQUIRED_VERSION) | sort -V | head -n1); \
	if [ "$(DOCKER_COMPOSE_VERSION)" != "$(DOCKER_COMPOSE_REQUIRED_VERSION)" ] \
	&& [ "$${version}" = $(DOCKER_COMPOSE_VERSION) ]; then \
		make .log level=error message="You must have docker-compose $(DOCKER_COMPOSE_REQUIRED_VERSION)"; \
	fi

.outside_container: .check_docker .check_docker_version .docker_compose_version

.check_configuration: .outside_container
	if [ ! -f "$(DOCKER_COMPOSE_ENV_FILE)" ]; then make configure; fi

	sha1_env_dist=$$(awk -F '=' '{print $$1}' $(DOCKER_COMPOSE_ENV_DIST_FILES) | sort | sha1sum); \
	sha1_env=$$(awk -F '=' '{print $$1}' $(DOCKER_COMPOSE_ENV_FILE) | sort | sha1sum); \
	if [ "$${sha1_env_dist}" != "$${sha1_env}" ]; then make configure; fi

.waiting_for_dependencies:
	make .waiting_for service=mysql port=3306 timeout=30
	make .waiting_for service=rabbitmq port=5672 timeout=30
	make .waiting_for service=redis port=6379 timeout=30

.waiting_for:
	make .log level=info message="Waiting for $(service) is ready." color=YELLOW

	set +m; timeout $(timeout) sh -c 'while true; do nc $(service) $(port) -w 1 -z && break; sleep 1; done' \
		|| make .log level=error message="Unable to connect to $(service)."

	make .log level=info message="$(service) is ready." color=GREEN

####################### Configuration #######################
configure: .outside_container
	rm -f $(DOCKER_COMPOSE_ENV_FILE)

	for file in $(DOCKER_COMPOSE_ENV_DIST_FILES); do \
		while IFS= read -r line || [ -n "$${line}" ]; do \
			if [ "$${line}" = "" ]; then continue; fi; \
			key=$$(echo "$${line}" | awk -F '=' '{print $$1}'); \
			default_value=$$(echo "$${line}" | awk -F '=' '{print substr($$0,length($$1)+2);}'); \
			case "$${key}" in \
				'COMPOSE_PROJECT_NAME'|'HTTP_HOST'|'AMQP_VHOST_URLENCODE') \
					value=$${default_value};; \
				'DOCKER_UID') \
					value=$$(id -u);; \
				*) \
					echo -n "$(CYAN)Define the value of $${key}) (default: $(YELLOW)$${default_value}$(CYAN)): $(RESET)"; \
					read -r value < /dev/tty > /dev/stdout; \
					if [ -z "$${value}" ]; then value=$${default_value}; fi; \
			esac; \
			echo "$${key}=$${value}" >> "$(DOCKER_COMPOSE_ENV_FILE)"; \
		done < "$${file}"; \
	done

	amqp_vhost=$$(awk -F "AMQP_VHOST *= *" 'NF>1{print $$2}' "$(DOCKER_COMPOSE_ENV_FILE)"); \
	amqp_vhost_encode=$$(curl -Gs -o /dev/null -w %{url_effective} --data-urlencode "$${amqp_vhost}" '' | cut -c 3-); \
	sed -e "s/^AMQP_VHOST_URLENCODE=.*$$/AMQP_VHOST_URLENCODE=$${amqp_vhost_encode}/g" -i "$(DOCKER_COMPOSE_ENV_FILE)"

####################### Docker #######################
run: .check_configuration
	docker-compose $(DOCKER_COMPOSE_OPTIONS) run --rm $(service) $(command)

start: .check_configuration
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) build --pull --parallel --quiet
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) up --detach --remove-orphans --quiet-pull mysql rabbitmq redis
	make run service=php-fpm command="make .waiting_for_dependencies"
	make run service=php-fpm command="make install"
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) up --detach --remove-orphans --quiet-pull

stop: .check_configuration
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) stop $(service)

ps: .check_configuration
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) ps

remove: .check_configuration
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) down --volumes --remove-orphans --rmi all

restart: stop start

####################### Docker CLI #######################
shell:
	make run service=php-fpm command=/bin/bash

mysql_cli: .check_configuration
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) exec mysql /bin/bash -c 'mysql -uroot -p$${MYSQL_ROOT_PASSWORD} $${MYSQL_DATABASE}'

redis_cli: .check_configuration
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) exec redis-cli

####################### Docker logs #######################
logs: .check_configuration
	$(DOCKER_COMPOSE_BIN) $(DOCKER_COMPOSE_OPTIONS) logs --timestamps --follow --tail=50 $(service)

logs_nginx:
	make logs service=nginx

logs_php:
	make logs service=php-fpm

logs_consumer:
	make logs service=php-messenger-consumer

logs_mysql:
	make logs service=mysql

logs_rabbitmq:
	make logs service=rabbitmq

logs_redis:
	make logs service=redis
