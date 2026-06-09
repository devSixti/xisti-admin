#!/usr/bin/env bash
# Apply Wompi credentials to general_settings (MySQL).
# Run on EC2: sudo -u ubuntu bash scripts/apply-wompi-general-settings.sh
# Or locally: ssh ... 'bash -s' < scripts/apply-wompi-general-settings.sh
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
cd "${APP_DIR}"

if [[ ! -f .env ]]; then
  echo "ERROR: ${APP_DIR}/.env not found" >&2
  exit 1
fi

read_env() {
  local key="$1"
  grep -E "^${key}=" .env | cut -d= -f2- | tr -d '"' | tr -d "'"
}

DB_HOST="$(read_env DB_HOST)"
DB_PORT="$(read_env DB_PORT)"
DB_DATABASE="$(read_env DB_DATABASE)"
DB_USERNAME="$(read_env DB_USERNAME)"
DB_PASSWORD="$(read_env DB_PASSWORD)"
DB_PORT="${DB_PORT:-3306}"

: "${WOMPI_MODE:?WOMPI_MODE required (0=sandbox, 1=production)}"
: "${WOMPI_SANDBOX_PUBLIC_KEY:?}"
: "${WOMPI_SANDBOX_PRIVATE_KEY:?}"
: "${WOMPI_SANDBOX_EVENT_KEY:?}"
: "${WOMPI_SANDBOX_INTEGRITY_KEY:?}"
: "${WOMPI_PRODUCTION_PUBLIC_KEY:?}"
: "${WOMPI_PRODUCTION_PRIVATE_KEY:?}"
: "${WOMPI_PRODUCTION_EVENT_KEY:?}"
: "${WOMPI_PRODUCTION_INTEGRITY_KEY:?}"

WOMPI_SANDBOX_BASE_URL="${WOMPI_SANDBOX_BASE_URL:-https://sandbox.wompi.co/v1}"
WOMPI_PRODUCTION_BASE_URL="${WOMPI_PRODUCTION_BASE_URL:-https://production.wompi.co/v1}"

mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" <<SQL
UPDATE general_settings SET
  wompi_mode = ${WOMPI_MODE},
  wompi_sandbox_public_key = '$(printf '%s' "${WOMPI_SANDBOX_PUBLIC_KEY}" | sed "s/'/''/g")',
  wompi_sandbox_private_key = '$(printf '%s' "${WOMPI_SANDBOX_PRIVATE_KEY}" | sed "s/'/''/g")',
  wompi_sandbox_event_key = '$(printf '%s' "${WOMPI_SANDBOX_EVENT_KEY}" | sed "s/'/''/g")',
  wompi_sandbox_integrity_key = '$(printf '%s' "${WOMPI_SANDBOX_INTEGRITY_KEY}" | sed "s/'/''/g")',
  wompi_sandbox_base_url = '$(printf '%s' "${WOMPI_SANDBOX_BASE_URL}" | sed "s/'/''/g")',
  wompi_production_public_key = '$(printf '%s' "${WOMPI_PRODUCTION_PUBLIC_KEY}" | sed "s/'/''/g")',
  wompi_production_private_key = '$(printf '%s' "${WOMPI_PRODUCTION_PRIVATE_KEY}" | sed "s/'/''/g")',
  wompi_production_event_key = '$(printf '%s' "${WOMPI_PRODUCTION_EVENT_KEY}" | sed "s/'/''/g")',
  wompi_production_integrity_key = '$(printf '%s' "${WOMPI_PRODUCTION_INTEGRITY_KEY}" | sed "s/'/''/g")',
  wompi_production_base_url = '$(printf '%s' "${WOMPI_PRODUCTION_BASE_URL}" | sed "s/'/''/g")'
WHERE id = 1;
SQL

mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" -e "
SELECT
  id,
  wompi_mode,
  IF(COALESCE(wompi_sandbox_public_key,'')='','VACIA','OK') AS sandbox_pub,
  IF(COALESCE(wompi_sandbox_private_key,'')='','VACIA','OK') AS sandbox_priv,
  IF(COALESCE(wompi_production_public_key,'')='','VACIA','OK') AS prod_pub,
  IF(COALESCE(wompi_production_private_key,'')='','VACIA','OK') AS prod_priv
FROM general_settings LIMIT 1;
"

echo "==> Wompi general_settings updated."
