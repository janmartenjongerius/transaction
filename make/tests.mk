SRC := $(shell find src -name '*.php')
TESTS := $(shell find tests -name '*.php')

tests: $(SRC) $(TESTS) vendor
	$(PHP) vendor/bin/phpunit

.PHONY: tests

COVERAGE = coverage

$(COVERAGE): $(SRC) $(TESTS) vendor
	$(PHP) vendor/bin/phpunit --coverage-html=$@ && touch $@

clean-coverage:
	rm -rf $(COVERAGE)

clean:: clean-coverage

ci: $(SRC) $(TESTS) composer.json | vendor $(PHP)
	$(PHP) vendor/bin/grumphp run --no-interaction
