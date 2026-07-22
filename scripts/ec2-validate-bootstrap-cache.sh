#!/usr/bin/env bash
# Fail if bootstrap cache references dev-only Laravel providers.
#
# Usage: ./scripts/ec2-validate-bootstrap-cache.sh [/var/www/xisti-admin]
set -euo pipefail

APP_DIR="${1:-${APP_DIR:-/var/www/xisti-admin}}"
CACHE_DIR="${APP_DIR}/bootstrap/cache"

DEV_PROVIDER_MARKERS=(
  'NunoMaduro\\Collision'
  'Laravel\\Sail\\'
  'Barryvdh\\LaravelIdeHelper'
  'Spatie\\LaravelIgnition'
)

if [[ ! -d "${CACHE_DIR}" ]]; then
  echo "ERROR: missing ${CACHE_DIR}" >&2
  exit 1
fi

for file in packages.php services.php; do
  [[ -f "${CACHE_DIR}/${file}" ]] || continue
  for marker in "${DEV_PROVIDER_MARKERS[@]}"; do
    if grep -q "${marker}" "${CACHE_DIR}/${file}" 2>/dev/null; then
      echo "ERROR: dev-only provider (${marker}) in bootstrap/cache/${file}" >&2
      exit 1
    fi
  done
done

echo "bootstrap cache OK"
