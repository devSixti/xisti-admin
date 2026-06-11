#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
: "${MAP_BROWSER_KEY:?}"
: "${MAP_SERVER_KEY:?}"
SCP_TARGET="${XISTI_SSH_HOST:-xisti-ec2}"
scp "${ROOT}/scripts/ec2-apply-maps-keys.sh" "${SCP_TARGET}:/tmp/ec2-apply-maps-keys.sh"
ssh "${SCP_TARGET}" "chmod +x /tmp/ec2-apply-maps-keys.sh && MAP_BROWSER_KEY='${MAP_BROWSER_KEY}' MAP_SERVER_KEY='${MAP_SERVER_KEY}' bash /tmp/ec2-apply-maps-keys.sh && rm -f /tmp/ec2-apply-maps-keys.sh"
