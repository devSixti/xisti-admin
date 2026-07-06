#!/usr/bin/env bash
# From laptop: configure Resend on EC2 and send showcase emails.
#
#   RESEND_API_KEY=re_xxx ./scripts/run-ec2-mail-test.sh jeronimorestrepo48@gmail.com
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
EMAIL="${1:-jeronimorestrepo48@gmail.com}"
RESEND_API_KEY="${RESEND_API_KEY:-}"

if [[ -z "${RESEND_API_KEY}" ]]; then
  echo "ERROR: export RESEND_API_KEY=re_xxxx first" >&2
  exit 1
fi

SSH=(ssh -o BatchMode=yes xisti-ec2)

echo "==> Configure Resend on EC2"
"${SSH[@]}" "cd /var/www/xisti-admin && sudo -u ubuntu env RESEND_API_KEY='${RESEND_API_KEY}' bash scripts/ec2-configure-resend.sh"

echo "==> Send showcase emails to ${EMAIL}"
"${SSH[@]}" "cd /var/www/xisti-admin && sudo -u ubuntu php artisan xisti:send-mail-test '${EMAIL}' --seed"

echo "==> Done. Check inbox (and spam) for [XISTI Test] messages."
