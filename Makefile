default: run-tests

.PHONY: run-tests

vendor: composer.lock
	composer install
	touch "$@"

composer.lock:
	composer install

run-tests: vendor
	vendor/bin/phpunit --bootstrap vendor/autoload.php test
