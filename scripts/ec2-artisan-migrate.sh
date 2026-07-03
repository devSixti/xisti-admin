#!/usr/bin/env bash
# Run Laravel migrations on EC2 with a MySQL user that has ALTER (DDL).
# App .env typically uses xisti_restricted_user (DML only); CD must not use that for migrate.
set -euo pipefail

APP_DIR="${1:-/var/www/xisti-admin}"
DEPLOY_USER="${2:-ubuntu}"

if [[ ! -d "${APP_DIR}" ]]; then
  echo "ERROR: APP_DIR not found: ${APP_DIR}" >&2
  exit 1
fi

export_db_migrate_credentials() {
  if [[ -n "${DB_MIGRATE_USERNAME:-}" && -n "${DB_MIGRATE_PASSWORD:-}" ]]; then
    export DB_USERNAME="${DB_MIGRATE_USERNAME}"
    export DB_PASSWORD="${DB_MIGRATE_PASSWORD}"
    echo "==> migrate: using DB_MIGRATE_* credentials from environment"
    return 0
  fi

  local debian_cnf="/etc/mysql/debian.cnf"
  if [[ -f "${debian_cnf}" ]] && sudo test -r "${debian_cnf}"; then
    local user pass
    user="$(sudo awk '/^user\s*=/ {print $3; exit}' "${debian_cnf}")"
    pass="$(sudo awk '/^password\s*=/ {print $3; exit}' "${debian_cnf}")"
    if [[ -n "${user}" && -n "${pass}" ]]; then
      export DB_USERNAME="${user}"
      export DB_PASSWORD="${pass}"
      echo "==> migrate: using MySQL credentials from ${debian_cnf} (${DB_USERNAME})"
      return 0
    fi
  fi

  echo "ERROR: no privileged DB credentials (debian.cnf or DB_MIGRATE_* secrets)" >&2
  return 1
}

load_db_credentials_from_env_file() {
  local env_file="${APP_DIR}/.env"
  if [[ ! -f "${env_file}" ]]; then
    return 1
  fi

  local user pass
  user="$(grep -E '^DB_USERNAME=' "${env_file}" | tail -1 | cut -d= -f2- | xargs)"
  pass="$(grep -E '^DB_PASSWORD=' "${env_file}" | tail -1 | cut -d= -f2- | xargs)"
  if [[ -n "${user}" && -n "${pass}" ]]; then
    export DB_USERNAME="${user}"
    export DB_PASSWORD="${pass}"
    echo "==> migrate: using DB credentials from ${env_file} (${DB_USERNAME})"
    return 0
  fi

  return 1
}

if ! export_db_migrate_credentials; then
  if ! load_db_credentials_from_env_file; then
    echo "ERROR: could not resolve DB credentials for migrate" >&2
    exit 1
  fi
  echo "WARNING: migrate is using app .env DB user; DDL may fail if user lacks ALTER." >&2
  echo "WARNING: set GitHub secrets EC2_DB_MIGRATE_USERNAME and EC2_DB_MIGRATE_PASSWORD (or fix /etc/mysql/debian.cnf)." >&2
fi

# sudo strips the parent environment unless variables are passed explicitly.
sudo -u "${DEPLOY_USER}" \
  DB_USERNAME="${DB_USERNAME}" \
  DB_PASSWORD="${DB_PASSWORD}" \
  bash -lc "
    set -euo pipefail
    cd '${APP_DIR}'
    rm -f bootstrap/cache/config.php 2>/dev/null || true
    rm -f bootstrap/cache/packages.php bootstrap/cache/services.php 2>/dev/null || true
    php artisan config:clear
    php artisan package:discover --ansi 2>/dev/null || true
    php artisan migrate --force
    php artisan db:seed --class=WorldCurrencySeeder --force
    php artisan db:seed --class=LanguageListsSeeder --force
    php artisan db:seed --class=XistiEnableSocialLoginSeeder --force
    php artisan db:seed --class=XistiPurgeLegacyBrandingSeeder --force
    php artisan db:seed --class=PageSettingsSeeder --force
    php artisan db:seed --class=EmailTemplatesSeeder --force
    php artisan db:seed --class=VehicleServicesSeeder --force
    php artisan db:seed --class=VehicleCommissionRateSeeder --force
    php artisan db:seed --class=AdminModuleSeeder --force
    php artisan db:seed --class=BrandedEmailTemplatesSeeder --force
    php artisan db:seed --class=AdminRbacMatrixSeeder --force
    php artisan db:seed --class=AdminRbacMockUsersSeeder --force
    php artisan db:seed --class=FarePricingRuleSeeder --force
  "
