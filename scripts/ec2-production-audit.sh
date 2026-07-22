#!/usr/bin/env bash
# Production audit: smoke tests + bootstrap cache + recent Laravel errors.
#
# Usage:
#   ./scripts/ec2-production-audit.sh
#   API_BASE=https://admin.xistiapp.com ./scripts/ec2-production-audit.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

API_BASE="${API_BASE:-https://admin.xistiapp.com}"
APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
LOG_FILE="${LOG_FILE:-${APP_DIR}/storage/logs/laravel-$(date +%Y-%m-%d).log}"

echo "Production audit — ${API_BASE}"
echo "================================"

if [[ -x "${ROOT}/scripts/ec2-validate-bootstrap-cache.sh" && -d "${APP_DIR}/bootstrap/cache" ]]; then
  "${ROOT}/scripts/ec2-validate-bootstrap-cache.sh" "${APP_DIR}" && echo "  PASS: bootstrap cache"
else
  echo "  SKIP: bootstrap cache (local run or missing APP_DIR)"
fi

API_BASE="${API_BASE}" "${ROOT}/scripts/smoke-test-production.sh"
API_BASE="${API_BASE}" REQUESTS=100 CONCURRENCY=20 MAX_ERROR_RATE=1.0 "${ROOT}/scripts/api-stress-test.sh"

if [[ -f "${LOG_FILE}" ]]; then
  RECENT_500="$(grep -E "production\.(ERROR|CRITICAL)" "${LOG_FILE}" 2>/dev/null | grep -c 'HTTP 500\|Collision\|translator' || true)"
  echo "Recent critical log matches today: ${RECENT_500}"
  if [[ "${RECENT_500}" -gt 5 ]]; then
    echo "WARN: elevated critical errors in ${LOG_FILE}" >&2
  fi
fi

echo "Audit complete"
