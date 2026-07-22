#!/usr/bin/env bash
# Install EC2 cron healthcheck (auto-heal bootstrap cache on HTTP 500).
#
# Usage on EC2 (as ubuntu with sudo):
#   sudo bash scripts/install-ec2-healthcheck-cron.sh
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
DEPLOY_USER="${DEPLOY_USER:-ubuntu}"
API_HOST="${API_HOST:-admin.xistiapp.com}"
CRON_FILE="/etc/cron.d/xisti-admin-healthcheck"
SCRIPT="${APP_DIR}/scripts/ec2-healthcheck-cron.sh"

if [[ ! -x "${SCRIPT}" ]]; then
  chmod +x "${APP_DIR}/scripts/ec2-healthcheck-cron.sh" \
    "${APP_DIR}/scripts/ec2-safe-artisan-cache.sh" \
    "${APP_DIR}/scripts/ec2-validate-bootstrap-cache.sh" \
    "${APP_DIR}/scripts/ec2-post-deploy-verify.sh" 2>/dev/null || true
fi

cat > "${CRON_FILE}" <<EOF
# Auto-heal XISTI admin API when bootstrap cache breaks (HTTP 500).
*/5 * * * * ${DEPLOY_USER} APP_DIR=${APP_DIR} DEPLOY_USER=${DEPLOY_USER} API_HOST=${API_HOST} ${SCRIPT} >/dev/null 2>&1
EOF
chmod 644 "${CRON_FILE}"
echo "Installed ${CRON_FILE}"
