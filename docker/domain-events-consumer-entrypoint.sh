#!/bin/bash

php /var/www/apisearch/bin/console cache:warmup --env=prod --no-debug --no-interaction
php /var/www/apisearch/bin/console apisearch-server:server-configuration --env=prod --no-debug --no-interaction
php /var/www/apisearch/bin/console apisearch-worker:domain-events-consumer --env=prod --no-debug --no-interaction
