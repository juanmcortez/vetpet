.PHONY: help up up-all down install fresh test lint shell logs wait-db build-images

export UID := $(shell id -u)
export GID := $(shell id -g)

DOCKER_COMPOSE := docker compose
CORE_SERVICES := postgres redis mailpit
APP_SERVICES := nginx app horizon scheduler node

help: ## Show this help
	@awk 'BEGIN {FS = ":.*## "; printf "Usage: make <target>\n\nTargets:\n"} /^[a-zA-Z0-9_.-]+:.*## / {printf "  %-12s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: install ## Bootstrap and start the core development stack
	$(DOCKER_COMPOSE) up -d $(CORE_SERVICES) $(APP_SERVICES)

up-all: install ## Bootstrap and start core + storage + search profiles
	$(DOCKER_COMPOSE) --profile storage --profile search up -d

down: ## Stop all services (including optional profiles)
	$(DOCKER_COMPOSE) --profile storage --profile search down --remove-orphans

install: ensure-env build-images ## Install dependencies, app key, and run migrations
	$(DOCKER_COMPOSE) up -d $(CORE_SERVICES)
	@$(MAKE) wait-db
	$(DOCKER_COMPOSE) run --rm --no-deps app composer install --no-interaction --prefer-dist
	$(DOCKER_COMPOSE) run --rm --no-deps node npm ci
	@if ! grep -q '^APP_KEY=base64:' .env; then \
		$(DOCKER_COMPOSE) run --rm --no-deps app php artisan key:generate --force; \
	fi
	$(DOCKER_COMPOSE) run --rm app php artisan migrate --force

ensure-env:
	@test -f .env || cp .env.example .env

build-images:
	$(DOCKER_COMPOSE) build app horizon scheduler

wait-db:
	@echo "Waiting for PostgreSQL..."
	@until $(DOCKER_COMPOSE) exec -T postgres pg_isready -U "$${DB_USERNAME:-vetpet}" -d "$${DB_DATABASE:-vetpet}" >/dev/null 2>&1; do sleep 1; done

fresh: ## Reset the database and run seeders
	$(DOCKER_COMPOSE) exec -T app php artisan migrate:fresh --seed --force

test: ## Run the Pest test suite (builds frontend assets first)
	$(DOCKER_COMPOSE) run --rm --no-deps node npm run build
	$(DOCKER_COMPOSE) run --rm --no-deps app php artisan test

lint: ## Run PHP and JavaScript linters
	$(DOCKER_COMPOSE) run --rm --no-deps app composer lint:check
	$(DOCKER_COMPOSE) run --rm --no-deps node npm run check

shell: ## Open a shell in the app container
	$(DOCKER_COMPOSE) exec app bash

logs: ## Follow logs for service s= (example: make logs s=app)
	@if [ -z "$(s)" ]; then \
		echo "Usage: make logs s=<service>"; \
		$(DOCKER_COMPOSE) ps --services; \
		exit 1; \
	fi
	$(DOCKER_COMPOSE) logs -f $(s)
