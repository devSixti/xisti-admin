#!/usr/bin/env bash
# Manual production deploy of XISTI admin to EC2 (rsync + artisan).
# Usage: ./scripts/xisti-deploy-admin-ec2.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SSH_HOST="${XISTI_SSH_HOST:-xisti-ec2}"
APP_DIR="/var/www/xisti-admin"

echo "==> Local: $ROOT @ $(git -C "$ROOT" rev-parse --short HEAD 2>/dev/null || echo no-git)"
echo "==> Syncing to ${SSH_HOST}:${APP_DIR}"

rsync -az --delete \
  -e "ssh -o BatchMode=yes -o IdentitiesOnly=yes" \
  --exclude '.git' \
  --exclude 'vendor' \
  --exclude 'node_modules' \
  --exclude '.env' \
  --exclude 'storage/logs' \
  --exclude 'storage/framework/cache/data' \
  --exclude 'storage/framework/sessions' \
  --exclude 'storage/framework/views' \
  --exclude 'bootstrap/cache/config.php' \
  --exclude 'composer.phar' \
  --exclude 'composer-setup.php' \
  --exclude '.cursor' \
  "${ROOT}/" "${SSH_HOST}:/tmp/xisti-admin-deploy/"

ssh -o BatchMode=yes "$SSH_HOST" "bash -s" <<'DEPLOY'
set -euo pipefail
APP_DIR='/var/www/xisti-admin'
rsync -a --delete \
  --exclude '.env' \
  --exclude 'storage' \
  --exclude 'vendor' \
  --exclude '.git' \
  /tmp/xisti-admin-deploy/ "${APP_DIR}/"
cd "${APP_DIR}"
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
rm -f bootstrap/cache/config.php
php artisan config:cache
php artisan view:cache || true
sudo systemctl reload php8.5-fpm nginx 2>/dev/null || sudo systemctl reload php*-fpm nginx 2>/dev/null || true
rm -rf /tmp/xisti-admin-deploy
echo "==> XISTI admin deploy OK — markets version: $(php -r "echo (require 'config/markets.php')['version'] ?? '?';")"
DEPLOY
