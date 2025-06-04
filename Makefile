.PHONY: composer-install help install restart bash start stop
.DEFAULT_GOAL := help

# Docker command variables
DOCKER_ROOT = docker exec -t --user root $(shell docker ps --filter name=les_echo_app -q)
DOCKER_ROOT_I = docker exec -ti --user root $(shell docker ps --filter name=les_echo_app -q)

# Color variables for output
GREEN = \033[32m
YELLOW = \033[33m
BLUE = \033[34m
RESET = \033[0m

# Commands to manage the project
bash: ## Enter container as root
	$(DOCKER_ROOT_I) bash

composer-install: ## Run composer install
	$(DOCKER_ROOT) composer install

install: start composer-install ## Install dependencies

start: ## Start the project
	COMPOSE_PROJECT_NAME="les_echo" docker compose -f docker-compose.yml up -d --build

stop: ## Stop the project
	COMPOSE_PROJECT_NAME="les_echo" docker compose -f docker-compose.yml down

restart: stop start ## Restart the project

# Help command to display available targets
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
