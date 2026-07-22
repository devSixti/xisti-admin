#!/usr/bin/env bash
# Rebuild Laravel bootstrap cache safely on production EC2.
#
# Prevents the #1 production outage: dev-only providers (Collision, Sail, etc.)
# cached while vendor was installed with composer --no-dev.
#
# Usage on EC2:
#   APP_DIR=/var/www/xisti-admin DEPLOY_USER=ubuntu \
#     ./scripts/ec2-safe-artisan-cache.sh --reload-php
#
# Env:
#   APP_DIR          Application root (required)
#   DEPLOY_USER      Unix user that owns the app (required)
#   PHP_FPM_SERVICE  e.g. php8.5-fpm (optional, auto-detected)
#   SKIP_COMPOSER    Set to 1 to skip composer install --no-dev
set -euo pipefail

APP_DIR="${APP_DIR:-}"
DEPLOY_USER="${DEPLOY_USER:-}"
RELOAD_PHP=0
SKIP_COMPOSER=0

DEV_PROVIDER_MARKERS=(
  'NunoMaduro\\Collision'
  'Laravel\\Sail\\'
  'Barryvdh\\LaravelIdeHelper'
  'Spatie\\LaravelIgnition'
)

usage() {
  echo "Usage: APP_DIR=... DEPLOY_USER=... $0 [--reload-php] [--skip-composer]" >&2
  exit 1
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --reload-php) RELOAD_PHP=1; shift ;;
    --skip-composer) SKIP_COMPOSER=1; shift ;;
    -h|--help) usage ;;
    *) echo "Unknown option: $1" >&2; usage ;;
  esac
done

if [[ -z "${APP_DIR}" || -z "${DEPLOY_USER}" ]]; then
  echo "ERROR: APP_DIR and DEPLOY_USER are required." >&2
  usage
fi

if [[ ! -d "${APP_DIR}" ]]; then
  echo "ERROR: APP_DIR not found: ${APP_DIR}" >&2
  exit 1
fi

validate_bootstrap_cache() {
  local cache_dir="${APP_DIR}/bootstrap/cache"
  local file marker

  for file in packages.php services.php; do
    [[ -f "${cache_dir}/${file}" ]] || continue
    for marker in "${DEV_PROVIDER_MARKERS[@]}"; do
      if grep -q "${marker}" "${cache_dir}/${file}" 2>/dev/null; then
        echo "ERROR: dev-only provider (${marker}) found in bootstrap/cache/${file}" >&2
        return 1
      fi
    done
  done

  echo "==> bootstrap cache validated (no dev-only providers)"
}

detect_php_fpm_service() {
  if [[ -n "${PHP_FPM_SERVICE:-}" ]]; then
    printf '%s' "${PHP_FPM_SERVICE}"
    return
  fi
  for svc in php8.5-fpm php8.2-fpm php8.3-fpm php-fpm; do
    if systemctl is-active --quiet "${svc}" 2>/dev/null; then
      printf '%s' "${svc}"
      return
    fi
  done
}

fix_runtime_permissions() {
  sudo chown -R "${DEPLOY_USER}:www-data" "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
  sudo chmod -R ug+rwX "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
  if [[ -d "${APP_DIR}/public/assets/images" ]]; then
    sudo chown -R "${DEPLOY_USER}:www-data" "${APP_DIR}/public/assets/images"
    sudo find "${APP_DIR}/public/assets/images" -type d -exec chmod 2775 {} \;
    sudo find "${APP_DIR}/public/assets/images" -type f -exec chmod 664 {} \;
  fi
}

echo "==> Safe artisan cache — ${APP_DIR} (user: ${DEPLOY_USER})"

sudo -u "${DEPLOY_USER}" bash -lc "
  set -euo pipefail
  cd '${APP_DIR}'

  if [[ '${SKIP_COMPOSER}' != '1' ]]; then
    composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs
  fi

  rm -f bootstrap/cache/config.php bootstrap/cache/packages.php bootstrap/cache/services.php
  php artisan config:clear 2>/dev/null || true
  php artisan package:discover --ansi
  php artisan config:cache
  php artisan view:cache
  rm -f bootstrap/cache/routes-v7.php bootstrap/cache/routes.php 2>/dev/null || true
  php artisan --version
"

validate_bootstrap_cache
fix_runtime_permissions

if [[ "${RELOAD_PHP}" == "1" ]]; then
  SVC="$(detect_php_fpm_service || true)"
  if [[ -n "${SVC}" ]]; then
    echo "==> reloading ${SVC} + nginx"
    sudo systemctl reload "${SVC}" nginx
  else
    echo "WARNING: could not detect php-fpm service; reload nginx only" >&2
    sudo systemctl reload nginx 2>/dev/null || true
  fi
fi

echo "==> Safe artisan cache complete"
