COMPOSER = composer2
SYMFONY_CONSOLE = php bin/console
SYMFONY_TOOL = symfony
CS_FIXER = php vendor/bin/php-cs-fixer
DOCKER_COMPOSE = COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1 docker-compose
USER_ID := $(shell id -u)
GROUP_ID := $(shell id -g)

build-dev: composer-dev

build-prod: composer-prod

post-deploy:
	$(SYMFONY_CONSOLE) cache:clear

migrate:
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --allow-no-migration --no-interaction -vvv

composer-dev:
	APP_ENV=dev && $(COMPOSER) install

composer-prod:
	export SYMFONY_ENV=prod && $(COMPOSER) install --no-dev --no-interaction --no-progress --profile --prefer-dist --optimize-autoloader

check:
	$(SYMFONY_TOOL) check:requirements
	$(SYMFONY_TOOL) check:security

install-symfony-tool:
	wget https://get.symfony.com/cli/installer -O - | bash
	mv ~/.symfony/bin/symfony /usr/local/bin/symfony

docker-compose-up:
	COMPOSE_DOCKER_CLI_BUILD=1 \
	DOCKER_BUILDKIT=1 \
	USER_ID=$(USER_ID) \
	GROUP_ID=$(GROUP_ID) \
	docker-compose  up --build

generate_swagger:
	docker-compose exec -u mortgage-php-fpm bin/console api:openapi:export > storage/swagger/openapi.json

# не менять название. Используется DevOps для старта контейнера
import_offers:
	bin/console app:offer:import

# не менять название. Используется DevOps для старта контейнера
calculate_min_month_payment:
	bin/console app:offer:calculate:min_month_payment

# не менять название. Используется DevOps для старта контейнера
messenger_consume:
	bin/console messenger:consume async_calculation

cpc:
	bin/console c:p:c cache.app

test-backend:
	make build-dev
	make cscheck
	make phpstancheck
	XDEBUG_MODE=coverage php vendor/bin/codecept run unit --xml /var/www/test-reports/junit.xml --coverage-xml /var/www/test-reports/clover/clover.xml