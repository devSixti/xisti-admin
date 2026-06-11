#!/usr/bin/env bash
# Run ec2-sync-production-secrets.sh on production EC2 from your laptop.
#
# Usage:
#   ./scripts/run-ec2-production-bootstrap.sh
#   APPLY_WOMPI=1 WOMPI_ENV_FILE=../credentials/wompi-from-zimo-general-settings.env \
#     ./scripts/run-ec2-production-bootstrap.sh
#
# Requires SSH to EC2 (Host xisti-ec2 in ~/.ssh/config or EC2_SSH_* env vars).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT}"

EC2_HOST="${EC2_SSH_HOST:-54.159.169.235}"
EC2_PORT="${EC2_SSH_PORT:-987}"
EC2_USER="${EC2_SSH_USER:-ubuntu}"
EC2_KEY="${EC2_SSH_KEY:-${HOME}/.ssh/id_rsa}"
APP_DIR="${APP_DIR:-/var/www/xisti-admin}"
APPLY_WOMPI="${APPLY_WOMPI:-0}"
WOMPI_ENV_FILE="${WOMPI_ENV_FILE:-}"

if [[ -f "${HOME}/.ssh/config" ]] && grep -q '^Host xisti-ec2' "${HOME}/.ssh/config" 2>/dev/null; then
  SSH_CMD=(ssh xisti-ec2)
  SCP_TARGET="xisti-ec2"
  SCP_CMD=(scp)
else
  SSH_CMD=(ssh -i "${EC2_KEY}" -p "${EC2_PORT}" -o IdentitiesOnly=yes "${EC2_USER}@${EC2_HOST}")
  SCP_TARGET="${EC2_USER}@${EC2_HOST}"
  SCP_CMD=(scp -i "${EC2_KEY}" -P "${EC2_PORT}" -o IdentitiesOnly=yes)
fi

REMOTE_WOMPI=""
if [[ "${APPLY_WOMPI}" == "1" ]]; then
  if [[ -z "${WOMPI_ENV_FILE}" || ! -f "${WOMPI_ENV_FILE}" ]]; then
    echo "ERROR: APPLY_WOMPI=1 requires WOMPI_ENV_FILE pointing to a readable file" >&2
    exit 1
  fi
  REMOTE_WOMPI="/tmp/xisti-wompi-$$.env"
  "${SCP_CMD[@]}" "${WOMPI_ENV_FILE}" "${SCP_TARGET}:${REMOTE_WOMPI}"
fi

echo "==> Uploading bootstrap script to EC2..."
"${SCP_CMD[@]}" "${ROOT}/scripts/ec2-sync-production-secrets.sh" "${SCP_TARGET}:/tmp/ec2-sync-production-secrets.sh"

REMOTE_ENV=()
REMOTE_ENV+=("APP_DIR=${APP_DIR}")
REMOTE_ENV+=("APPLY_WOMPI=${APPLY_WOMPI}")
if [[ -n "${REMOTE_WOMPI}" ]]; then
  REMOTE_ENV+=("WOMPI_ENV_FILE=${REMOTE_WOMPI}")
fi
if [[ -n "${XISTI_APP_KEY:-}" ]]; then
  REMOTE_ENV+=("XISTI_APP_KEY=${XISTI_APP_KEY}")
fi

echo "==> Running bootstrap on EC2..."
"${SSH_CMD[@]}" bash -s <<EOF
set -euo pipefail
chmod +x /tmp/ec2-sync-production-secrets.sh
${REMOTE_ENV[*]} bash /tmp/ec2-sync-production-secrets.sh
rm -f /tmp/ec2-sync-production-secrets.sh ${REMOTE_WOMPI}
EOF

echo "==> Remote bootstrap finished."
