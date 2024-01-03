#!/bin/sh
docker compose exec app php "$@"
exit $?
