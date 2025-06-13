.PHONY: lint test cs-fix phpstan clean reinstall install

# Default target
qa: lint test phpstan

# Check code style and run static analysis
lint: cs-fix

# Run CS-Fixer
cs-fix:
	vendor/bin/php-cs-fixer fix --diff --verbose

# Run PHPStan
phpstan:
	vendor/bin/phpstan analyse -c phpstan.dist.neon --memory-limit=2G

# Run tests
test:
	symfony php bin/phpunit

# Clean project files
clean:
	rm -rf vendor/
	rm -rf var/cache/*
	rm -rf var/log/*
	rm -rf .phpunit.cache/
	rm -f .env.local
	rm -f .env.test.local

# Reinstall the project
reinstall: clean
	composer install --optimize-autoloader
	symfony console doctrine:database:drop --force --if-exists
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate --no-interaction
	symfony console doctrine:fixtures:load --no-interaction
	symfony console cache:clear
	symfony console cache:warmup

# Build the project
build: composer docker

composer:
	composer install --optimize-autoloader

# Start the Docker containers
docker:
	docker compose down -v
	docker compose up -d --build

# Full reinstall with Docker
reinstall-docker: clean docker reinstall

# Install the project (single command)
install: 
	make reinstall-docker 
	symfony serve -d

