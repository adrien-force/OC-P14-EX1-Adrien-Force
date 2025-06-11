.PHONY: lint test cs-fix phpstan

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

# Build the project

build: composer docker

composer:
	composer install --optimize-autoloader

# Start the Docker containers
docker:
	docker compose up -d

