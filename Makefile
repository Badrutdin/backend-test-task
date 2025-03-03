USER_ID := $(shell id -u)

DC = USER_ID=$(USER_ID) docker compose
DC_RUN = ${DC} run --rm sio_test
DC_EXEC = ${DC} exec sio_test

PHONY: help
.DEFAULT_GOAL := help

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

init: down build install up reset-app-if-needed success-message  ## Initialize environment

build: ## Build services.
	${DC} build $(c)

up: ## Create and start services.
	${DC} up -d $(c)

stop: ## Stop services.
	${DC} stop $(c)

start: ## Start services.
	${DC} start $(c)

down: ## Stop and remove containers and volumes.
	${DC} down -v $(c)

restart: stop start ## Restart services.

console: ## Login in console.
	${DC_EXEC} /bin/bash

install: ## Install dependencies without running the whole application.
	${DC_RUN} composer install

reset-app: ## DROP DATABASE, DELETE MIGRATIONS, apply new migration, start fixtures, and launch test inside the container.
	@echo  "\033[41m\033[97mAre you sure you want to reset the application? This will DROP DATABASE, DELETE MIGRATIONS, apply new migration, start fixtures, and launch test inside the container. (y/N): \033[0m"; \
	read confirm && \
	if [[ $$confirm =~ ^[Yy]$$ ]]; then \
		echo "Deleting migration files..."; \
		${DC_EXEC} rm -f migrations/Version*.php || echo "Migration files not found."; \
		echo "Migration files deleted."; \
		${DC_EXEC} bash -c "php bin/console doctrine:database:drop --force"; \
		${DC_EXEC} bash -c "php bin/console doctrine:database:create"; \
		${DC_EXEC} bash -c "php bin/console make:migration --no-interaction"; \
		${DC_EXEC} bash -c "php bin/console doctrine:migrations:migrate --no-interaction"; \
		${DC_EXEC} bash -c "php -d memory_limit=512M bin/console doctrine:fixtures:load --no-interaction"; \
		${DC_EXEC} bash -c "php bin/phpunit "; \
		echo "Database reset and migrations applied."; \
	else \
		echo "Operation cancelled."; \
	fi
reset-app-if-needed: ## If reset flag = "yes", call reset-app
ifeq ($(reset),yes)
	$(MAKE) reset-app
endif
success-message:
	@echo "You can now access the application at http://localhost:8337"
	@echo "Good luck! 🚀"