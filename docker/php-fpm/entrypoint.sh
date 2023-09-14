#!/bin/sh
set -e

make build-dev
make migrate
make post-deploy

exec "$@"
