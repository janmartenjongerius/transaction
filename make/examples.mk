SRC := $(shell find src -name '*.php')

EXAMPLES_SRC := $(wildcard examples/*.php)
EXAMPLES := $(patsubst %.php,%.php.d,$(EXAMPLES_SRC))

examples: $(EXAMPLES)

examples/%.d: $(EXAMPLES_SRC) $(SRC) | $(COMPOSER_AUTOLOAD) $(PHP)
	$(PHP) examples/$* && touch $@

clean-examples:
	rm -rf $(EXAMPLES)

clean:: clean-examples
