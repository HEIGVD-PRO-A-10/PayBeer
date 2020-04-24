user := $(shell id -u)
group := $(shell id -g)
dc := USER_ID=$(user) GROUP_ID=$(group) docker-compose
dr := $(dc) run --rm
de := docker-compose exec
sy := $(de) php bin/console

.DEFAULT_GOAL := help
.PHONY: help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: up
up: ## Lance les conteneurs
	$(dc) up -d

.PHONY: build
build: ## Lance les conteneurs
	$(dc) build

.PHONY: dep
dep: ## Install les dépendances PHP
	$(de) php composer install

.PHONY: stop
stop: ## Stop les conteneurs
	$(dc) stop

.PHONY: clean
clean: ## Stop et supprime les conteneurs
	$(dc) down

.PHONY: migrate
migrate: ## Migration des bases de données
	$(sy) doctrine:migrations:migrate

.PHONY: fixtures
fixtures: ## Rempli la base de donnée avec de fausses données
	$(sy) doctrine:fixtures:load

.PHONY: mysql
mysql: ## MySQL en ligne de commande
	$(de) db mysql -uroot -proot

.PHONY: api
api: ## Génére la documentation de l'API
	vendor/bin/openapi --format json --output ./public/api/api.json ./swagger/swagger.php src

.PHONY: deploy
deploy:
	$(de) php dep deploy

.PHONY: dump-secrets
dump-secrets:
	$(sy) secrets:decrypt-to-local --force
