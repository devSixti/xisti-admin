#!/usr/bin/env bash
# Manual production deploy of XISTI admin to EC2 (rsync code + artisan cache).
# Skips composer install (prod PHP 8.5 vs lockfile constraints).
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
  --exclude 'storage' \
  --exclude 'bootstrap/cache/*.php' \
  --exclude 'composer.phar' \
  --exclude 'composer-setup.php' \
  --exclude '.cursor' \
  "${ROOT}/" "${SSH_HOST}:${APP_DIR}/"

ssh -o BatchMode=yes "$SSH_HOST" "bash -s" <<'DEPLOY'
set -euo pipefail
cd /var/www/xisti-admin
rm -f bootstrap/cache/config.php bootstrap/cache/packages.php bootstrap/cache/services.php
php artisan package:discover --ansi >/dev/null
php artisan migrate --force || true
php artisan config:clear
php artisan cache:clear
php artisan config:cache
sudo systemctl reload php8.5-fpm nginx 2>/dev/null || true
echo "==> XISTI admin deploy OK — markets $(php artisan tinker --execute=\"echo config('markets.version');\")"
DEPLOY
