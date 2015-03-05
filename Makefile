default: run-tests

.PHONY: \
	clean \
	run-tests \
	test-dependencies

clean:
	rm -rf vendor composer.lock

vendor: composer.lock
	composer install
	touch "$@"

composer.lock:
	composer install

test-dependencies: vendor

run-tests: test-dependencies
	vendor/bin/phpunit --bootstrap vendor/autoload.php test
