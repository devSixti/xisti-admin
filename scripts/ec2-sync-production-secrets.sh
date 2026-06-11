#!/usr/bin/env bash
# Sync production secrets on EC2: XISTI_APP_KEY (.env + general_settings) and optional Wompi.
#
# Run on EC2:
#   cd /var/www/xisti-admin
#   sudo -u ubuntu bash scripts/ec2-sync-production-secrets.sh
#
# With Wompi env file (keys never echoed):
#   WOMPI_ENV_FILE=/path/to/wompi.env APPLY_WOMPI=1 \
#     sudo -u ubuntu bash scripts/ec2-sync-production-secrets.sh
#
# Force a specific app key (must match Site Settings after run):
#   XISTI_APP_KEY='your-secret' sudo -u ubuntu bash scripts/ec2-sync-production-secrets.sh
#
# From laptop via SSH (see scripts/run-ec2-production-bootstrap.sh):
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
APPLY_WOMPI="${APPLY_WOMPI:-0}"
WOMPI_ENV_FILE="${WOMPI_ENV_FILE:-}"
PLACEHOLDER_PATTERN='CHANGE_ME'

cd "${APP_DIR}"

if [[ ! -f .env ]]; then
  echo "ERROR: ${APP_DIR}/.env not found" >&2
  exit 1
fi

read_env() {
  local key="$1"
  grep -E "^${key}=" .env 2>/dev/null | cut -d= -f2- | tr -d '"' | tr -d "'" || true
}

set_env_var() {
  local key="$1"
  local value="$2"
  local escaped
  escaped="$(printf '%s' "${value}" | sed 's/[\/&]/\\&/g')"
  if grep -qE "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${escaped}|" .env
  else
    echo "${key}=${value}" >> .env
  fi
}

is_placeholder_key() {
  local key="$1"
  [[ -z "${key}" ]] && return 0
  [[ "${key}" == *"${PLACEHOLDER_PATTERN}"* ]] && return 0
  return 1
}

resolve_app_key() {
  if [[ -n "${XISTI_APP_KEY:-}" ]] && ! is_placeholder_key "${XISTI_APP_KEY}"; then
    printf '%s' "${XISTI_APP_KEY}"
    return
  fi

  local from_env
  from_env="$(read_env XISTI_APP_KEY)"
  if ! is_placeholder_key "${from_env}"; then
    printf '%s' "${from_env}"
    return
  fi

  openssl rand -base64 48 | tr -d '\n'
}

mysql_esc() {
  printf '%s' "$1" | sed "s/'/''/g"
}

DB_HOST="$(read_env DB_HOST)"
DB_PORT="$(read_env DB_PORT)"
DB_DATABASE="$(read_env DB_DATABASE)"
DB_USERNAME="$(read_env DB_USERNAME)"
DB_PASSWORD="$(read_env DB_PASSWORD)"
DB_PORT="${DB_PORT:-3306}"

APP_KEY="$(resolve_app_key)"
if is_placeholder_key "${APP_KEY}"; then
  echo "ERROR: could not resolve a valid XISTI_APP_KEY" >&2
  exit 1
fi

echo "==> Syncing XISTI_APP_KEY to .env and general_settings (value not printed)"
set_env_var "XISTI_APP_KEY" "${APP_KEY}"

mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" <<SQL
UPDATE general_settings
SET app_key = '$(mysql_esc "${APP_KEY}")'
WHERE id = 1;
SQL

mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" -e "
SELECT id,
  IF(app_key IS NULL OR app_key = '' OR app_key LIKE '%${PLACEHOLDER_PATTERN}%', 'PLACEHOLDER', 'OK') AS app_key_status
FROM general_settings LIMIT 1;
"

if [[ "${APPLY_WOMPI}" == "1" ]]; then
  if [[ -z "${WOMPI_ENV_FILE}" || ! -f "${WOMPI_ENV_FILE}" ]]; then
    echo "ERROR: APPLY_WOMPI=1 requires readable WOMPI_ENV_FILE" >&2
    exit 1
  fi
  echo "==> Applying Wompi keys from ${WOMPI_ENV_FILE}"
  # shellcheck disable=SC1090
  set -a
  source "${WOMPI_ENV_FILE}"
  set +a
  export WOMPI_MODE="${wompi_mode:-1}"
  export WOMPI_SANDBOX_PUBLIC_KEY="${wompi_sandbox_public_key:-}"
  export WOMPI_SANDBOX_PRIVATE_KEY="${wompi_sandbox_private_key:-}"
  export WOMPI_SANDBOX_EVENT_KEY="${wompi_sandbox_event_key:-}"
  export WOMPI_SANDBOX_INTEGRITY_KEY="${wompi_sandbox_integrity_key:-}"
  export WOMPI_PRODUCTION_PUBLIC_KEY="${wompi_production_public_key:-}"
  export WOMPI_PRODUCTION_PRIVATE_KEY="${wompi_production_private_key:-}"
  export WOMPI_PRODUCTION_EVENT_KEY="${wompi_production_event_key:-}"
  export WOMPI_PRODUCTION_INTEGRITY_KEY="${wompi_production_integrity_key:-}"
  bash "${APP_DIR}/scripts/apply-wompi-general-settings.sh"
fi

echo "==> Caching Laravel config"
sudo -u "${DEPLOY_USER:-ubuntu}" bash -lc "cd '${APP_DIR}' && php artisan config:cache"

APP_URL="$(read_env APP_URL)"
APP_URL="${APP_URL:-https://admin.xistiapp.com}"
AUTH_HEADER="$(php "${APP_DIR}/scripts/generate-app-auth-header.php" "${APP_KEY}" --raw)"

echo "==> Verifying app-version-check"
VERSION_JSON="$(curl -fsS -X POST "${APP_URL}/api/customer/app-version-check" \
  -H "Content-Type: application/json" \
  -d '{"app_type":"1","app_version":"1.0.0","device_type":"android","login_device":"1"}')"

php -r '
$j = json_decode(file_get_contents("php://stdin"), true);
if (!is_array($j) || ($j["status"] ?? 0) != 1) { fwrite(STDERR, "app-version-check failed\n"); exit(1); }
$key = (string)($j["app_key"] ?? "");
if ($key === "" || str_contains($key, "CHANGE_ME")) { fwrite(STDERR, "app_key still placeholder in API\n"); exit(1); }
echo "app-version-check OK\n";
echo "  enable_encomiendas_mobile=" . ($j["enable_encomiendas_mobile"] ?? "?") . "\n";
echo "  enable_expreso_mobile=" . ($j["enable_expreso_mobile"] ?? "?") . "\n";
echo "  admin_commission_percent=" . ($j["admin_commission_percent"] ?? "?") . "\n";
' <<<"${VERSION_JSON}"

echo "==> Verifying Authorization header (login pre-check)"
LOGIN_JSON="$(curl -fsS -X POST "${APP_URL}/api/customer/login" \
  -H "Content-Type: application/json" \
  -H "Authorization: ${AUTH_HEADER}" \
  -d '{"login_type":"email","contact_number":"3001234567","select_country_code":"+57","select_currency":"COP","select_language":"es","device_token":"smoke-test","login_device":"1"}')"

php -r '
$j = json_decode(file_get_contents("php://stdin"), true);
if (!is_array($j)) { fwrite(STDERR, "login response invalid\n"); exit(1); }
$code = (int)($j["message_code"] ?? -1);
// 0 = user flow ok (OTP path), 9 = validation — both mean auth header accepted
if (in_array($code, [0, 1, 2, 9], true)) {
  echo "authorization header OK (message_code=${code})\n";
  exit(0);
}
if (($j["status"] ?? 0) === 0 && str_contains((string)($j["message"] ?? ""), "Authorization")) {
  fwrite(STDERR, "authorization rejected\n");
  exit(1);
}
echo "authorization header OK\n";
' <<<"${LOGIN_JSON}"

echo ""
echo "==> Done. Register Wompi webhook if not yet:"
echo "    ${APP_URL}/api/wompi/webhook"
echo "    (alias: ${APP_URL}/webhook/wompi)"
