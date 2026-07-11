#!/usr/bin/env bash
# Manual production deploy of XISTI admin to EC2 (rsync + artisan).
# Usage: ./scripts/xisti-deploy-admin-ec2.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SSH_HOST="${XISTI_SSH_HOST:-xisti-ec2}"
APP_DIR="/var/www/xisti-admin"
DEPLOY_USER="ubuntu"

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

ssh -o BatchMode=yes "$SSH_HOST" "bash -s" <<DEPLOY
set -euo pipefail
APP_DIR='${APP_DIR}'
sudo rsync -a --delete \
  --exclude '.env' \
  --exclude 'storage' \
  --exclude 'vendor' \
  --exclude '.git' \
  /tmp/xisti-admin-deploy/ "\${APP_DIR}/"
sudo chown -R www-data:www-data "\${APP_DIR}/app" "\${APP_DIR}/config" "\${APP_DIR}/routes" "\${APP_DIR}/database" "\${APP_DIR}/tests" 2>/dev/null || true
cd "\${APP_DIR}"
sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || \
  sudo composer install --no-dev --optimize-autoloader --no-interaction
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan view:cache || true
sudo systemctl reload php8.2-fpm nginx 2>/dev/null || sudo systemctl reload php*-fpm nginx 2>/dev/null || true
rm -rf /tmp/xisti-admin-deploy
echo '==> XISTI admin deploy OK'
DEPLOY
