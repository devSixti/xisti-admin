#!/usr/bin/env bash
# Manual production deploy to AWS EC2 (when GitHub Actions billing blocks CD).
# Usage: ./scripts/manual-deploy-ec2.sh
# Requires: env EC2_SSH_HOST, EC2_SSH_PORT, EC2_SSH_KEY (or SSH config Host xisti-ec2)
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

EC2_HOST="${EC2_SSH_HOST:-54.159.169.235}"
EC2_PORT="${EC2_SSH_PORT:-987}"
EC2_USER="${EC2_SSH_USER:-ubuntu}"
EC2_KEY="${EC2_SSH_KEY:-${HOME}/.ssh/id_rsa}"
SSH_TARGET="${EC2_USER}@${EC2_HOST}"
SSH_OPTS=(-i "${EC2_KEY}" -p "${EC2_PORT}" -o IdentitiesOnly=yes)

if [[ -f "${HOME}/.ssh/config" ]] && grep -q '^Host xisti-ec2' "${HOME}/.ssh/config" 2>/dev/null; then
  SSH_CMD=(ssh -o BatchMode=yes xisti-ec2)
  SCP_CMD=(scp -o BatchMode=yes xisti-ec2)
else
  SSH_CMD=(ssh "${SSH_OPTS[@]}" "${SSH_TARGET}")
  SCP_CMD=(scp "${SSH_OPTS[@]}")
fi

RELEASE_TAR="$(mktemp /tmp/xisti-release.XXXXXX.tar.gz)"
trap 'rm -f "${RELEASE_TAR}"' EXIT

echo "==> Branch: $(git branch --show-current) @ $(git rev-parse --short HEAD)"
echo "==> Building release tarball..."
tar czf "${RELEASE_TAR}" \
  --exclude=.git \
  --exclude=vendor \
  --exclude=node_modules \
  --exclude=.env \
  --exclude=storage/logs \
  --exclude=storage/framework/cache/data \
  --exclude=storage/framework/sessions \
  --exclude=storage/framework/views \
  --exclude=database/database.sqlite \
  .

echo "==> Uploading to EC2..."
if [[ -f "${HOME}/.ssh/config" ]] && grep -q '^Host xisti-ec2' "${HOME}/.ssh/config" 2>/dev/null; then
  cat "${RELEASE_TAR}" | ssh -o BatchMode=yes xisti-ec2 'cat > /tmp/xisti-release.tar.gz'
else
  "${SCP_CMD[@]}" "${RELEASE_TAR}" "${SSH_TARGET}:/tmp/xisti-release.tar.gz"
fi

echo "==> Installing on EC2..."
"${SSH_CMD[@]}" 'bash -s' <<'DEPLOY_EOF'
set -euo pipefail
APP_DIR="/var/www/xisti-admin"
DEPLOY_USER="ubuntu"
CD_STAGING="/tmp/xisti-cd-staging"

rm -rf "${CD_STAGING}"
mkdir -p "${CD_STAGING}"
TAR_PATH="/tmp/xisti-release.tar.gz"
tar xzf "${TAR_PATH}" -C "${CD_STAGING}"
rm -f "${TAR_PATH}"

sudo -u "${DEPLOY_USER}" rsync -av \
  --exclude '.env' \
  --exclude 'storage' \
  --exclude 'vendor' \
  --exclude '.git' \
  --exclude 'node_modules' \
  "${CD_STAGING}/" "${APP_DIR}/"

sudo -u "${DEPLOY_USER}" bash -lc "
  set -euo pipefail
  cd '${APP_DIR}'
  composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs
"

bash "${APP_DIR}/scripts/ec2-artisan-migrate.sh" "${APP_DIR}" "${DEPLOY_USER}"

sudo chown -R "${DEPLOY_USER}:www-data" "${APP_DIR}/resources" "${APP_DIR}/app" 2>/dev/null || true

APP_DIR="${APP_DIR}" DEPLOY_USER="${DEPLOY_USER}" bash "${APP_DIR}/scripts/ec2-safe-artisan-cache.sh" --reload-php --skip-composer

sudo bash "${APP_DIR}/scripts/install-ec2-exchange-rates-cron.sh" 2>/dev/null || true
sudo bash "${APP_DIR}/scripts/install-ec2-healthcheck-cron.sh" 2>/dev/null || true
sudo -u "${DEPLOY_USER}" bash -lc "cd '${APP_DIR}' && php artisan config:clear && php artisan currency:sync-live-rates" || true

if [ -d "${APP_DIR}/public/assets/images" ]; then
  sudo chown -R "${DEPLOY_USER}:www-data" "${APP_DIR}/public/assets/images"
  sudo find "${APP_DIR}/public/assets/images" -type d -exec chmod 2775 {} \;
  sudo find "${APP_DIR}/public/assets/images" -type f -exec chmod 664 {} \;
fi
rm -rf "${CD_STAGING}"
APP_DIR="${APP_DIR}" API_HOST="admin.xistiapp.com" bash "${APP_DIR}/scripts/ec2-post-deploy-verify.sh"
echo "==> Deploy OK"
DEPLOY_EOF

echo "==> Done."
"${ROOT}/scripts/smoke-test-production.sh"
