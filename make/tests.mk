SRC := $(shell find src -name '*.php')
TESTS := $(shell find tests -name '*.php')

tests: $(SRC) $(TESTS) vendor
	$(PHP) vendor/bin/phpunit --testsuite=unit

.PHONY: tests

COVERAGE = coverage

$(COVERAGE): $(SRC) $(TESTS) vendor
	$(PHP) vendor/bin/phpunit --testsuite=unit --coverage-html=$@ && touch $@

clean-coverage:
	rm -rf $(COVERAGE)

clean:: clean-coverage
