COMPOSER = composer2
SYMFONY_CONSOLE = php bin/console
SYMFONY_TOOL = symfony
CS_FIXER = php vendor/bin/php-cs-fixer
DOCKER_COMPOSE = COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose
USER_ID := $(shell id -u)
GROUP_ID := $(shell id -g)

build-dev: composer-dev

post-deploy:
	$(SYMFONY_CONSOLE) cache:clear

migrate:
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --allow-no-migration --no-interaction -vvv

composer-dev:
	APP_ENV=dev && $(COMPOSER) install