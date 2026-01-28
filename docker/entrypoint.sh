#!/bin/bash
set -e

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    cd /var/www/html && composer install --no-interaction --optimize-autoloader
fi

exec "$@"
