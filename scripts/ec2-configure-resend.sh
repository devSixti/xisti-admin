#!/usr/bin/env bash
# Configure XISTI production mail to use Resend + noreply@xistiapp.com
#
# Usage (on EC2 or via SSH):
#   RESEND_API_KEY=re_xxxx sudo -u ubuntu bash scripts/ec2-configure-resend.sh
#
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
cd "${APP_DIR}"

if [[ ! -f .env ]]; then
  echo "ERROR: ${APP_DIR}/.env not found" >&2
  exit 1
fi

RESEND_API_KEY="${RESEND_API_KEY:-}"
if [[ -z "${RESEND_API_KEY}" ]]; then
  echo "ERROR: set RESEND_API_KEY (from https://resend.com/api-keys)" >&2
  exit 1
fi

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

echo "==> Configuring Resend transactional mail"
set_env_var "MAIL_MAILER" "resend"
set_env_var "MAIL_FROM_ADDRESS" "noreply@xistiapp.com"
set_env_var "MAIL_FROM_NAME" "XISTI"
set_env_var "RESEND_API_KEY" "${RESEND_API_KEY}"
set_env_var "XISTI_MAIL_FROM_ADDRESS" "noreply@xistiapp.com"
set_env_var "XISTI_MAIL_FROM_NAME" "XISTI"

sudo -u "${DEPLOY_USER:-ubuntu}" bash -lc "
  set -euo pipefail
  cd '${APP_DIR}'
  php artisan config:clear
"
APP_DIR="${APP_DIR}" DEPLOY_USER="${DEPLOY_USER:-ubuntu}" bash "${APP_DIR}/scripts/ec2-safe-artisan-cache.sh" --skip-composer
sudo -u "${DEPLOY_USER:-ubuntu}" bash -lc "
  set -euo pipefail
  cd '${APP_DIR}'
  php artisan db:seed --class=BrandedEmailTemplatesSeeder --force
"

echo "==> Resend configured. Smoke test:"
echo "    php artisan xisti:send-mail-test you@example.com"
