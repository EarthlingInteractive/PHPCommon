default: run-unit-tests

.PHONY: \
	clean \
	run-unit-tests \
	test-dependencies

clean:
	rm -rf vendor composer.lock

vendor: composer.lock
	composer install
	touch "$@"

composer.lock:
	composer install

test-dependencies: vendor

run-unit-tests: test-dependencies
	vendor/bin/phpunit --bootstrap vendor/autoload.php test
