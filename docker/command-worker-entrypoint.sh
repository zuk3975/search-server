#!/bin/bash

exec php /var/www/apisearch/bin/console apisearch-worker:command-consumer --env=prod
