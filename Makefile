include composer.mk

SRC := $(shell find src -name '*.php')

EXAMPLES_SRC := $(wildcard examples/*.php)
EXAMPLES := $(patsubst %.php,%.php.d,$(EXAMPLES_SRC))

examples: $(EXAMPLES)

examples/%.d: $(EXAMPLES_SRC) $(SRC) | $(COMPOSER_AUTOLOAD) $(PHP)
	$(PHP) examples/$* && touch $@

clean-examples:
	rm -rf $(EXAMPLES)

clean:: clean-examples

TESTS := $(shell find tests -name '*.php')

tests: $(SRC) $(TESTS) vendor
	$(PHP) vendor/bin/phpunit

.PHONY: tests

COVERAGE : coverage

$(COVERAGE): $(SRC) $(TESTS) vendor
	$(PHP) vendor/bin/phpunit --coverage-html=$@ && touch $@

clean-coverage:
	rm -rf $(COVERAGE)

clean:: clean-coverage
