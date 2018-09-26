#!/bin/bash

rm -Rf /var/www/apisearch/var/cache
php /var/www/apisearch/bin/console cache:warmup --env=prod --no-debug --no-interaction
php /var/www/apisearch/bin/console apisearch-server:server-configuration --env=prod --no-debug --no-interaction
php /var/www/apisearch/bin/console apisearch-worker:commands-consumer --env=prod --no-debug --no-interaction
