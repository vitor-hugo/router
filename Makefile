help:
	@echo "Please use 'make <target>' where <target> is one of"
	@echo "  start-server  to start the test server"
	@echo "  stop-server   to stop the test server"
	@echo "  test          to perform the tests"
	@echo "  testdox       to perform the tests with testdox"

start-server:
	nohup php -H -S localhost\:8000 -t tests/Server/ > /dev/null 2>&1 &

stop-server:
	@PID=$(shell ps axo pid,command \
	| grep 'tests/Server/' \
	  | grep -v grep \
	  | cut -f 1 -d " "\
	) && [ -n "$$PID" ] && kill $$PID || true

test: start-server
	vendor/bin/phpunit tests --display-errors --display-warnings --display-deprecations
	$(MAKE) stop-server

testdox: start-server
	vendor/bin/phpunit tests --no-progress --testdox --display-errors --display-warnings --display-deprecations
	$(MAKE) stop-server
