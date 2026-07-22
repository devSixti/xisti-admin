#!/usr/bin/env bash
# Quick post-deploy verification on EC2 (run on the server).
#
# Usage:
#   APP_DIR=... API_HOST=admin.xistiapp.com ./scripts/ec2-post-deploy-verify.sh
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
API_HOST="${API_HOST:-admin.xistiapp.com}"
FAIL=0

fail() { echo "  FAIL: $*" >&2; FAIL=$((FAIL + 1)); }
pass() { echo "  PASS: $*"; }

echo "Post-deploy verify — ${API_HOST}"
echo "================================"

if [[ -x "${APP_DIR}/scripts/ec2-validate-bootstrap-cache.sh" ]]; then
  if "${APP_DIR}/scripts/ec2-validate-bootstrap-cache.sh" "${APP_DIR}"; then
    pass "bootstrap cache"
  else
    fail "bootstrap cache contains dev-only providers"
  fi
fi

for path in /api/customer/app-version-check /api/customer/home /api/customer/country-and-currency-list; do
  CODE="$(curl -sS -o /dev/null -w '%{http_code}' --connect-timeout 10 \
    -X POST -H "Host: ${API_HOST}" -H "Content-Type: application/json" \
    -d '{}' "http://127.0.0.1${path}" 2>/dev/null || echo 000)"
  if [[ "${CODE}" == "500" || "${CODE}" == "000" ]]; then
    fail "${path} HTTP ${CODE}"
  else
    pass "${path} HTTP ${CODE}"
  fi
done

echo "================================"
if [[ "${FAIL}" -gt 0 ]]; then
  echo "Verification failed (${FAIL} checks)" >&2
  exit 1
fi
echo "Verification OK"
