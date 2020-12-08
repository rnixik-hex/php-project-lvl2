#!/usr/bin/make

SHELL = /bin/bash

CURRENT_RUN_USER_ID := $(shell id -u)
CURRENT_RUN_USER_NAME := "${USER}"

.PHONY: tests

docker-build:
	docker build -t local-php-project-lvl1 --build-arg "RUN_USER_ID=${CURRENT_RUN_USER_ID}" --build-arg "RUN_USER_NAME=${CURRENT_RUN_USER_NAME}" -f ./Dockerfile .

docker-prepare:
	make docker-build
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` local-php-project-lvl1 make install

docker-run:
	make docker-build
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` local-php-project-lvl1 bash

install:
	composer install

validate:
	composer validate

lint:
	composer phpcs
	composer phpstan

tests:
	vendor/bin/phpunit tests

docker-asciinema-auth:
	mkdir -p "${HOME}/.config/asciinema"
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` -v "${HOME}/.config/asciinema:${HOME}/.config/asciinema" local-php-project-lvl1 asciinema auth

docker-asciinema-rec:
	docker run --rm -it -v `pwd`:`pwd` -w `pwd` -v "${HOME}/.config/asciinema:${HOME}/.config/asciinema" local-php-project-lvl1 asciinema rec -c "/bin/bash"
