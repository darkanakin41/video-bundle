install:
	composer install
	
cs:
	./vendor/bin/php-cs-fixer fix --verbose

cs_dry_run:
	./vendor/bin/php-cs-fixer fix --verbose --dry-run

test:
	./vendor/bin/phpunit --coverage-text --coverage-html ./coverage

test_debug:
	./vendor/bin/phpunit --coverage-text --coverage-html ./coverage --group=debug
