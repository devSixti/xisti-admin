#!/usr/bin/env bash
# Install daily EC2 cron for live exchange rate sync.
#
# Usage on EC2 (as ubuntu with sudo):
#   sudo bash scripts/install-ec2-exchange-rates-cron.sh
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
DEPLOY_USER="${DEPLOY_USER:-ubuntu}"
CRON_FILE="/etc/cron.d/xisti-admin-exchange-rates"
SCRIPT="${APP_DIR}/scripts/ec2-sync-exchange-rates-cron.sh"

chmod +x "${SCRIPT}" 2>/dev/null || true

cat > "${CRON_FILE}" <<EOF
# Sync world_currency ratios from free FX API (base COP) — daily 06:00 Bogotá.
0 6 * * * ${DEPLOY_USER} APP_DIR=${APP_DIR} DEPLOY_USER=${DEPLOY_USER} ${SCRIPT}
EOF
chmod 644 "${CRON_FILE}"
echo "Installed ${CRON_FILE}"
