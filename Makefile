help:
	@echo "Please use 'make <target>' where <target> is one of"
	@echo "  start-server  to start the test server"
	@echo "  stop-server   to stop the test server"
	@echo "  test          to perform the tests"
	@echo "  testdox       to perform the tests with testdox"

start-server:
	php -S localhost\:8000 -t tests/Integration/Server/ &

stop-server:
	@PID=$(shell ps axo pid,command \
	| grep 'tests/Integration/Server/' \
	  | grep -v grep \
	  | cut -f 1 -d " "\
	) && [ -n "$$PID" ] && kill $$PID || true

test: start-server
	vendor/bin/phpunit tests --display-errors --display-warnings --display-deprecations
	$(MAKE) stop-server

testdox: start-server
	vendor/bin/phpunit tests --no-progress --testdox --display-errors --display-warnings --display-deprecations
	$(MAKE) stop-server
