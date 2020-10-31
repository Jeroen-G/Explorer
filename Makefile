# Default to showing help section
info: intro help

intro:
	@echo ""
	@echo "🗺️  Explorer"
	@echo ""

# ===========================
# Main commands
# ===========================

# Dependencies
install: intro do-composer-install do-assets-install
reset: intro do-clean install

# Tests
tests: intro do-test-phpunit do-test-report
mutation: intro do-test-infection

# Development
pre-commit: intro do-lint-staged-files do-commit-intro
codestyle: intro do-cs-ecs
codestyle-fix: intro do-cs-ecs-fix

# ===========================
# Overview of commands
# ===========================

help:
	@echo "\n=== Make commands ===\n"
	@echo "Dependencies"
	@echo "    make install                   Make the project ready for development."
	@echo "    make reset                     Reinstall backend and frontend dependencies."
	@echo "\nTests"
	@echo "    make tests                     Run phpunit tests."
	@echo "    make mutations                 Run the infection mutation tests."
	@echo "\nDevelopment"
	@echo "    make codestyle                 Check if the codestyle is OK."
	@echo "    make codestyle-fix             Check and fix your messy codestyle."

# ===========================
# Recipes
# ===========================

# Dependencies
do-composer-install:
	@echo "\n=== Installing composer dependencies ===\n"\
	COMPOSER_MEMORY_LIMIT=-1 composer install

do-assets-install:
	@echo "\n=== Installing npm dependencies ===\n"
	npm install

# Development
do-commit-intro:
	@echo "\n=== Let's ship it! ===\n"

do-lint-staged-files:
	@node_modules/.bin/lint-staged

do-cs-ecs:
	./vendor/bin/ecs check --config=easy-coding-standard.php

do-cs-ecs-fix:
	./vendor/bin/ecs check --fix --config=easy-coding-standard.php

do-clean:
	@echo "\n=== 🧹 Cleaning up ===\n"
	@rm -rf src/vendor/*
	@rm -rf src/node_modules/*

# Tests
do-test-phpunit:
	@echo "\n=== Running unit tests ===\n"
	vendor/bin/phpunit --coverage-html ./report

do-test-infection:
	@echo "\n=== Running unit tests ===\n"
	vendor/bin/infection --threads=4 --min-covered-msi=100

do-test-report:
	@echo "\n=== Click the link below to see the test coverage report ===\n"
	@echo "report/index.html"
