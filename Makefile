.PHONY: lint test phpstan clean reinstall install

# Default target
qa: lint test phpstan

# Check code style and run static analysis
lint:
	vendor/bin/php-cs-fixer fix --diff --verbose

# Run PHPStan
phpstan:
	vendor/bin/phpstan analyse -c phpstan.dist.neon --memory-limit=2G

# Run tests
test:
	symfony php bin/phpunit

# Reinstall the project
reinstall: composer
	symfony console doctrine:database:drop --force --if-exists
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate --no-interaction
	symfony console doctrine:fixtures:load --no-interaction
	symfony console cache:clear
	symfony console cache:warmup

# Build the project
build: composer docker

composer:
	composer install

# Start the Docker containers
docker:
	docker compose up -d --force-recreate

# Full reinstall with Docker
reinstall-docker: docker reinstall

# Install the project (single command)
install: 
	make reinstall-docker 
	symfony serve -d

