#!/bin/bash

php /var/www/apisearch/bin/console apisearch-server:server-configuration --env=prod
php /var/www/apisearch/bin/console apisearch-worker:command-consumer --env=prod --no-debug
