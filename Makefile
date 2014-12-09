default: run-tests

.PHONY: \
	clean \
	run-tests

clean:
	rm -rf vendor composer.lock

vendor: composer.lock
	composer install
	touch "$@"

composer.lock:
	composer install

run-tests: vendor
	vendor/bin/phpunit --bootstrap vendor/autoload.php test
