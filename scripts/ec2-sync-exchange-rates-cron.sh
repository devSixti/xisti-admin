#!/usr/bin/env bash
# Daily live FX sync (COP base) via artisan currency:sync-live-rates.
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
DEPLOY_USER="${DEPLOY_USER:-ubuntu}"
LOG="${APP_DIR}/storage/logs/exchange-rates-cron.log"

{
  echo "==> $(date -Is) exchange rate sync start"
  sudo -u "${DEPLOY_USER}" bash -lc "cd '${APP_DIR}' && php artisan currency:sync-live-rates"
  echo "==> $(date -Is) exchange rate sync OK"
} >> "${LOG}" 2>&1
