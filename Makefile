.PHONY: default .log .requirements git_hooks install vendor migrate migrate_mysql migrate_rabbitmq tests phpcs .cache_test phpstan phpunit infection

MAKEFLAGS += --no-print-directory --silent
LOG_LEVEL=info

# prompt color
RED=$(shell tput setaf 1)
GREEN=$(shell tput setaf 2)
YELLOW=$(shell tput setaf 3)
BLUE=$(shell tput setaf 4)
PURPLE=$(shell tput setaf 5)
CYAN=$(shell tput setaf 6)
WHITE=$(shell tput setaf 7)
RESET=$(shell tput sgr0)

default:

-include docker/Makefile
-include kubernetes/Makefile

.log:
	case "$(level)" in \
		'error') \
			echo "$(RED)$(message)$(RESET)"; \
			exit 1;; \
		'warning') \
			if [ "$(LOG_LEVEL)" = "info" ] || [ "$(LOG_LEVEL)" = "warning" ]; then \
				echo "$(YELLOW)$(message)$(RESET)"; \
			fi;; \
		'info'|*) \
			if [ "$(LOG_LEVEL)" = "info" ]; then \
				echo "$($(color))$(message)$(RESET)"; \
			fi;; \
	esac

####################### Application #######################
.requirements:
	if [ "$$(which composer)" = "" ]; then \
    	echo "$(RED)You must install composer (https://getcomposer.org/download/)$(RESET)"; \
    	exit 1; \
    fi

	composer check-platform-reqs --no-interaction --quiet || composer check-platform-reqs --no-interaction --ansi

git_hooks:
#	if [ "$$(which git)" != "" ]; then \
#        git config core.hooksPath .githooks; \
#    fi

install: git_hooks vendor migrate

vendor: .requirements
	composer install --no-interaction --no-progress --ansi

migrate: migrate_mysql migrate_rabbitmq

migrate_mysql: .requirements
	bin/console doctrine:migrations:migrate --query-time --allow-no-migration --no-interaction

migrate_rabbitmq: .requirements
	bin/console messenger:setup-transports --no-interaction

####################### Tests #######################
tests: phpcs phpstan phpunit infection

phpcs: .requirements
	vendor/bin/phpcs $(options)

.cache_test: .requirements
	APP_ENV=test bin/console cache:warmup

phpstan: .cache_test
	vendor/bin/phpstan analyse --memory-limit=-1 --no-interaction --ansi $(options)

phpunit: .cache_test
	vendor/bin/phpunit $(options)

infection: .cache_test
	vendor/bin/infection --threads=$(shell nproc) --no-interaction $(options)
