#!/bin/bash

appId=$(< /dev/urandom tr -dc a-z0-9 | head -c${1:-12}) && \
indexId=$(< /dev/urandom tr -dc a-z0-9 | head -c${1:-12}) && \
php ./bin/console apisearch-server:create-index $appId $indexId && \
php ./bin/console apisearch-server:generate-basic-tokens $appId
