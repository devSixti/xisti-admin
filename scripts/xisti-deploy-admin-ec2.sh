#!/usr/bin/env bash
# Manual production deploy of XISTI admin to EC2 (rsync code + safe artisan cache).
# Skips composer install (prod PHP 8.5 vs lockfile constraints).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SSH_HOST="${XISTI_SSH_HOST:-xisti-ec2}"
APP_DIR="/var/www/xisti-admin"
DEPLOY_USER="${XISTI_DEPLOY_USER:-ubuntu}"

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
  --exclude 'public/assets/images/profile-images/customer/' \
  --exclude 'public/assets/images/profile-images/provider/' \
  "${ROOT}/" "${SSH_HOST}:${APP_DIR}/"

ssh -o BatchMode=yes "$SSH_HOST" "bash -s" <<DEPLOY
set -euo pipefail
cd "${APP_DIR}"
sudo chown -R "${DEPLOY_USER}:www-data" resources app routes config scripts 2>/dev/null || true
php artisan migrate --force || true
APP_DIR="${APP_DIR}" DEPLOY_USER="${DEPLOY_USER}" bash "${APP_DIR}/scripts/ec2-safe-artisan-cache.sh" --reload-php --skip-composer
APP_DIR="${APP_DIR}" API_HOST="admin.xistiapp.com" bash "${APP_DIR}/scripts/ec2-post-deploy-verify.sh"
echo "==> XISTI admin deploy OK"
DEPLOY

echo "==> Running remote smoke tests..."
"${ROOT}/scripts/smoke-test-production.sh"
