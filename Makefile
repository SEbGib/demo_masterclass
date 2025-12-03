.PHONY: help up down restart logs status build install \
        shell-api shell-app test test-api test-app \
        db-migrate db-reset db-fixtures \
        issues issue lint lint-api lint-app

# Colors
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

## Help
help: ## Show this help
	@echo ''
	@echo 'Usage:'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-15s${RESET} %s\n", $$1, $$2}' $(MAKEFILE_LIST)

## Docker commands
up: ## Start all services
	docker compose up -d
	@echo ""
	@echo "${GREEN}Services started!${RESET}"
	@echo "  API:      http://localhost:8000/api"
	@echo "  Frontend: http://localhost:5173"
	@echo "  Database: localhost:5432"

down: ## Stop all services
	docker compose down

restart: ## Restart all services
	docker compose restart

logs: ## Show logs (follow mode)
	docker compose logs -f

logs-api: ## Show API logs only
	docker compose logs -f api nginx

logs-app: ## Show frontend logs only
	docker compose logs -f app

status: ## Show services status
	docker compose ps

build: ## Rebuild all containers
	docker compose build --no-cache

## Installation
install: ## First time setup
	@echo "${YELLOW}Building containers...${RESET}"
	docker compose build
	@echo "${YELLOW}Starting services...${RESET}"
	docker compose up -d
	@echo "${YELLOW}Installing API dependencies...${RESET}"
	docker compose exec api composer install
	@echo "${YELLOW}Installing frontend dependencies...${RESET}"
	docker compose exec app pnpm install
	@echo "${YELLOW}Running database migrations...${RESET}"
	$(MAKE) db-migrate
	@echo ""
	@echo "${GREEN}Installation complete!${RESET}"
	@echo "  API:      http://localhost:8000/api"
	@echo "  Frontend: http://localhost:5173"

## Development shells
shell-api: ## Open shell in API container
	docker compose exec api sh

shell-app: ## Open shell in frontend container
	docker compose exec app sh

shell-db: ## Open PostgreSQL shell
	docker compose exec database psql -U demo demo_masterclass

## Testing
test: test-api test-app ## Run all tests

test-api: ## Run API tests (PHPUnit)
	docker compose exec api ./vendor/bin/phpunit

test-app: ## Run frontend tests (Vitest)
	docker compose exec app pnpm test

test-watch: ## Run frontend tests in watch mode
	docker compose exec app pnpm test:watch

## Database
db-migrate: ## Run database migrations
	docker compose exec api php bin/console doctrine:migrations:migrate --no-interaction

db-diff: ## Generate migration from entity changes
	docker compose exec api php bin/console doctrine:migrations:diff

db-reset: ## Reset database (drop, create, migrate)
	docker compose exec api php bin/console doctrine:database:drop --force --if-exists
	docker compose exec api php bin/console doctrine:database:create
	docker compose exec api php bin/console doctrine:migrations:migrate --no-interaction
	@echo "${GREEN}Database reset complete!${RESET}"

db-fixtures: ## Load fixtures
	docker compose exec api php bin/console doctrine:fixtures:load --no-interaction

## Linting
lint: lint-api lint-app ## Run all linters

lint-api: ## Lint API code
	docker compose exec api ./vendor/bin/phpstan analyze src --level=5 || true

lint-app: ## Lint frontend code
	docker compose exec app pnpm lint

## GitHub Issues
issues: ## List GitHub issues
	gh issue list --limit 15

issue: ## View a specific issue (usage: make issue ID=1)
	gh issue view $(ID)

## Cache
cache-clear: ## Clear API cache
	docker compose exec api php bin/console cache:clear

## Local development (without Docker)
local-api: ## Start API locally (requires PHP/Symfony CLI)
	cd api && symfony server:start --port=8000

local-app: ## Start frontend locally (requires Node/pnpm)
	cd app && pnpm dev

## Utilities
api-routes: ## List API routes
	docker compose exec api php bin/console debug:router --show-controllers

api-entity: ## Create a new entity (usage: make api-entity NAME=Task)
	docker compose exec api php bin/console make:entity $(NAME)
