#!/usr/bin/env bash
# Configure XISTI to accept Wompi webhooks forwarded from ZIMO router.
# ZIMO keeps the Wompi dashboard URL; events with XISTI-WALLET-* or xistiapp.com redirect forward here.
#
# On XISTI EC2:
#   WOMPI_FORWARD_SECRET='shared-secret' bash scripts/ec2-sync-wompi-router.sh
#
# On ZIMO EC2 (.env) must also have:
#   WOMPI_XISTI_WEBHOOK_URL=https://admin.xistiapp.com/api/wompi/webhook
#   WOMPI_WEBHOOK_FORWARD_SECRET=<same shared-secret>
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
cd "${APP_DIR}"

if [[ -z "${WOMPI_FORWARD_SECRET:-}" ]]; then
  WOMPI_FORWARD_SECRET="$(openssl rand -base64 32 | tr -d '\n')"
  echo "Generated WOMPI_FORWARD_SECRET (save for ZIMO .env):"
  echo "${WOMPI_FORWARD_SECRET}"
fi

set_env_var() {
  local key="$1" value="$2"
  if grep -qE "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${value}|" .env
  else
    echo "${key}=${value}" >> .env
  fi
}

set_env_var "WOMPI_WEBHOOK_FORWARD_SECRET" "${WOMPI_FORWARD_SECRET}"
set_env_var "APP_URL" "https://admin.xistiapp.com"

sudo -u "${DEPLOY_USER:-ubuntu}" bash -lc "cd '${APP_DIR}' && php artisan config:cache"

echo "==> XISTI Wompi forward secret configured."
echo "==> ZIMO must use:"
echo "    WOMPI_XISTI_WEBHOOK_URL=https://admin.xistiapp.com/api/wompi/webhook"
echo "    WOMPI_WEBHOOK_FORWARD_SECRET=${WOMPI_FORWARD_SECRET}"
