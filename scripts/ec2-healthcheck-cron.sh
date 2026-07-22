#!/usr/bin/env bash
# Production healthcheck + auto-heal for Laravel bootstrap cache corruption.
# Install on EC2 (example every 5 min):
#   */5 * * * * APP_DIR=/var/www/xisti-admin DEPLOY_USER=ubuntu \
#     API_HOST=admin.xistiapp.com /var/www/xisti-admin/scripts/ec2-healthcheck-cron.sh
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
DEPLOY_USER="${DEPLOY_USER:-ubuntu}"
API_HOST="${API_HOST:-admin.xistiapp.com}"
LOG_FILE="${LOG_FILE:-/var/log/xisti-admin-healthcheck.log}"
SAFE_CACHE="${APP_DIR}/scripts/ec2-safe-artisan-cache.sh"

log() {
  echo "$(date -u +'%Y-%m-%dT%H:%M:%SZ') $*" | tee -a "${LOG_FILE}" 2>/dev/null || echo "$(date -u +'%Y-%m-%dT%H:%M:%SZ') $*"
}

CODE="$(curl -sS -o /dev/null -w '%{http_code}' --connect-timeout 10 \
  -X POST -H "Host: ${API_HOST}" -H "Content-Type: application/json" \
  -d '{"app_type":"1","app_version":"1.0.0","device_type":"android","login_device":"1"}' \
  "http://127.0.0.1/api/customer/app-version-check" 2>/dev/null || echo 000)"

if [[ "${CODE}" != "500" && "${CODE}" != "000" ]]; then
  exit 0
fi

log "ALERT app-version-check HTTP ${CODE} — attempting safe cache rebuild"

if [[ ! -x "${SAFE_CACHE}" ]]; then
  log "ERROR missing ${SAFE_CACHE}"
  exit 1
fi

if APP_DIR="${APP_DIR}" DEPLOY_USER="${DEPLOY_USER}" "${SAFE_CACHE}" --reload-php --skip-composer; then
  CODE2="$(curl -sS -o /dev/null -w '%{http_code}' --connect-timeout 10 \
    -X POST -H "Host: ${API_HOST}" -H "Content-Type: application/json" \
    -d '{"app_type":"1","app_version":"1.0.0","device_type":"android","login_device":"1"}' \
    "http://127.0.0.1/api/customer/app-version-check" 2>/dev/null || echo 000)"
  log "RECOVERED app-version-check HTTP ${CODE2}"
  exit 0
fi

log "ERROR safe cache rebuild failed"
exit 1
