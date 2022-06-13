#!/bin/sh

set -e

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then

    # update application cache
    php artisan optimize

    # run only migrations
    php artisan migrate --seed --force
    while [ $? -ne 0 ]; do
        sleep 10
        php artisan migrate --seed --force
    done

    php-fpm -D &&  nginx -g "daemon off;"

elif [ "$role" = "queue" ]; then

    echo "Running queue server..."

    php artisan horizon

elif [ "$role" = "scheduler" ]; then

    while [ true ]
    do
      php artisan schedule:run
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi
