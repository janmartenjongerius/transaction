SRC := $(shell find src -name '*.php')
EXAMPLES := $(shell find examples -name '*.phpt')

.PHONY: examples
examples: $(EXAMPLES) $(SRC) vendor
	$(PHP) vendor/bin/phpunit --testsuite=integration
