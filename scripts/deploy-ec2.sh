#!/usr/bin/env bash
# Despliegue xisti-admin en EC2 (ejecutar EN EL SERVIDOR, no en Zimo).
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
BRANCH="${BRANCH:-main}"

cd "$APP_DIR"
git fetch origin
git checkout "$BRANCH"
git pull origin "$BRANCH"

composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo chown -R www-data:www-data storage bootstrap/cache
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

echo "Deploy XISTI admin complete."
