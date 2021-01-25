#!/bin/sh

set -e

/usr/bin/composer dump-env prod --empty
bin/console cache:warmup

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
