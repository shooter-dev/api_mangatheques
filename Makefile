#          __                __
#    _____/ /_  ____  ____  / /____  _____
#   / ___/ __ \/ __ \/ __ \/ __/ _ \/ ___/
#  /__  / / / / /_/ / /_/ / /_/  __/ /
# /____/_/ /_/\____/\____/\__/\___/_/__
#       -*- By ShooterDev -*-   ____/ /__ _   __
#                              / __  / _ | | / /
#                             / /_/ /  __| |/ /
#                             \____/\___/|___/
# ==============================================
.PHONY: install composer phpstan phpstan phpinsights phpcpd phpmd database fixtures prepare tests fix analyse

install:
	@echo "          __                __                         "
	@echo "    _____/ /_  ____  ____  / /____  _____              "
	@echo "   / ___/ __ \/ __ \/ __ \/ __/ _ \/ ___/  __          "
	@echo "  /__  / / / / /_/ / /_/ / /_/  __/ / ____/ /__ _   __ "
	@echo " /____/_/ /_/\____/\____/\__/\___/_/ / __  / _ | | / / "
	@echo "       -*- By ShooterDev -*-        / /_/ /  __| |/ /  "
	@echo "                                    \____/\___/|___/   "
	@echo " ===================================================== "

	cp .env.dist .env.$(env).local
	sed -i -e 's/DATABASE_USER/$(db_user)/' .env.$(env).local
	sed -i -e 's/DATABASE_PASSWORD/$(db_password)/' .env.$(env).local
	rm -rf .env.*.local-e
	composer install
	make prepare env=$(env)
composer:
	composer valid

phpstan:
	php vendor/bin/phpstan analyse -c phpstan.neon src --no-progress

phpinsights:
	vendor/bin/phpinsights --no-interaction

phpcpd:
	vendor/bin/phpcpd src/

phpmd:
	vendor/bin/phpmd src/ text .phpmd.xml

database:
	php bin/console doctrine:database:drop --if-exists --force --env=$(env)
	php bin/console doctrine:database:create --env=$(env)
	php bin/console doctrine:schema:update --force --env=$(env)

fixtures:
	php bin/console doctrine:fixtures:load -n --env=$(env)

prepare:
	make database env=$(env)
	make fixtures env=$(env)

tests:
	php bin/phpunit --testdox

fix:
	vendor/bin/php-cs-fixer fix

analyse:
	make composer
	make phpcpd
	make phpmd
	make phpinsights
	make phpstan

run:
	clear

	@echo "          __                __                         "
	@echo "    _____/ /_  ____  ____  / /____  _____              "
	@echo "   / ___/ __ \/ __ \/ __ \/ __/ _ \/ ___/  __          "
	@echo "  /__  / / / / /_/ / /_/ / /_/  __/ / ____/ /__ _   __ "
	@echo " /____/_/ /_/\____/\____/\__/\___/_/ / __  / _ | | / / "
	@echo "       -*- By ShooterDev -*-        / /_/ /  __| |/ /  "
	@echo "                                    \____/\___/|___/   "
	@echo " ===================================================== "

	symfony serve
