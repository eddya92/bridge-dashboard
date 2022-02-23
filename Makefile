.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: up
up: docker-compose.yml
up: export UID = $(id -u)
up: export GID = $(id -g)
up: ## Docker compose up
	docker-compose up -d

.PHONY: down
down: ## Docker compose down
	docker-compose down

.PHONY: status
status: ## Docker compose process list
	docker-compose ps
