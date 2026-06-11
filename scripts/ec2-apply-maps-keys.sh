#!/usr/bin/env bash
# Apply Google Maps browser + server keys to general_settings on EC2.
#
# Usage on EC2:
#   MAP_BROWSER_KEY=... MAP_SERVER_KEY=... bash scripts/ec2-apply-maps-keys.sh
#
# From laptop:
#   MAP_BROWSER_KEY=... MAP_SERVER_KEY=... ./scripts/run-ec2-apply-maps-keys.sh
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
cd "${APP_DIR}"

: "${MAP_BROWSER_KEY:?MAP_BROWSER_KEY required}"
: "${MAP_SERVER_KEY:?MAP_SERVER_KEY required}"

read_env() {
  grep -E "^${1}=" .env 2>/dev/null | cut -d= -f2- | tr -d '"' | tr -d "'" || true
}

DB_HOST="$(read_env DB_HOST)"
DB_PORT="$(read_env DB_PORT)"
DB_DATABASE="$(read_env DB_DATABASE)"
DB_USERNAME="$(read_env DB_USERNAME)"
DB_PASSWORD="$(read_env DB_PASSWORD)"
DB_PORT="${DB_PORT:-3306}"

mysql_esc() { printf '%s' "$1" | sed "s/'/''/g"; }

mysql -h"${DB_HOST}" -P"${DB_PORT}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" "${DB_DATABASE}" <<SQL
UPDATE general_settings SET
  map_key = '$(mysql_esc "${MAP_BROWSER_KEY}")',
  server_map_key = '$(mysql_esc "${MAP_SERVER_KEY}")'
WHERE id = 1;
SQL

if grep -qE '^GOOGLE_MAPS_SERVER_KEY=' .env; then
  sed -i "s|^GOOGLE_MAPS_SERVER_KEY=.*|GOOGLE_MAPS_SERVER_KEY=${MAP_SERVER_KEY}|" .env
else
  echo "GOOGLE_MAPS_SERVER_KEY=${MAP_SERVER_KEY}" >> .env
fi

sudo -u "${DEPLOY_USER:-ubuntu}" bash -lc "cd '${APP_DIR}' && php artisan config:cache"

APP_URL="$(read_env APP_URL)"
APP_URL="${APP_URL:-https://admin.xistiapp.com}"

echo "==> Verifying map proxy"
HTTP_CODE="$(curl -sS -o /tmp/xisti-map-test.json -w '%{http_code}' -X POST "${APP_URL}/api/google-map" \
  -H 'Content-Type: application/json' \
  -d '{"url":"https://maps.googleapis.com/maps/api/geocode/json?latlng=6.2442,-75.5812&key="}')"
STATUS="$(php -r 'echo json_decode(file_get_contents("/tmp/xisti-map-test.json"), true)["status"] ?? "?";' 2>/dev/null || echo '?')"
echo "map proxy HTTP=${HTTP_CODE} status=${STATUS}"
if [[ "${STATUS}" != "OK" && "${STATUS}" != "1" ]]; then
  echo "WARN: Maps may still fail — check API restrictions and enabled APIs in GCP." >&2
fi
echo "==> Maps keys applied."
