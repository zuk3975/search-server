#!/bin/bash

rm -Rf /var/www/apisearch/var/cache
php /var/www/apisearch/bin/console cache:warmup --env=prod
php /var/www/apisearch/bin/console apisearch-server:server-configuration --env=prod
php /var/www/apisearch/bin/server 0.0.0.0:8200 --api
