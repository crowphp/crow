COMPONENT := crowphp
CODE_CONTAINER := php
APP_ROOT := /var/www/crowphp

all: dev logs

dev: kill-others
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml up -d
	@sleep 2

enter:
	@./ops/scripts/enter.sh ${COMPONENT} $(s)

.PHONY: build
build:
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml build

build-nocache:
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml build --no-cache

kill:
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml kill
kill-others:
	@userId=$$(id -u) groupId=$$(id -g) docker ps -q | xargs -r docker kill

nodev:
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml kill
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml rm -f
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml down --remove-orphans

logs:
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml logs -f $(s)

ps:
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml ps

tests: unit integration acceptance

restart:
	@userId=$$(id -u) groupId=$$(id -g) docker-compose -p ${COMPONENT} -f ops/docker/docker-compose.yml restart $(s)