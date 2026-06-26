#!/usr/bin/env bash
# Instala y arranca el worker de cola Laravel (correos asíncronos) en XISTI EC2.
set -euo pipefail

APP_DIR="/var/www/xisti-admin"
CONF_SRC="${APP_DIR}/scripts/supervisor/xisti-queue-worker.conf"
CONF_DST="/etc/supervisor/conf.d/xisti-queue-worker.conf"

sudo apt-get install -y supervisor 2>/dev/null || true
sudo cp "${CONF_SRC}" "${CONF_DST}"
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart xisti-queue-worker:* || sudo supervisorctl start xisti-queue-worker:*
sudo supervisorctl status xisti-queue-worker:*

echo "==> Queue worker XISTI activo. Logs: ${APP_DIR}/storage/logs/queue-worker.log"
