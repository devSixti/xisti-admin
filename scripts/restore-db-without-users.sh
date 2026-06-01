#!/usr/bin/env bash
# Restore production DB from pre-fresh backup; keep config/admin, no app users.
# Run on EC2 as root: sudo bash scripts/restore-db-without-users.sh
set -euo pipefail

BACKUP="${BACKUP:-/var/backups/appzimo-pre-fresh-20260521232310/db_zemo_app_full.sql.gz}"
DB="${DB:-db_zemo_app}"

if [ ! -f "$BACKUP" ]; then
  echo "Backup not found: $BACKUP"
  exit 1
fi

USER_TABLES=(
  users
  user_address
  user_courier_service_details
  user_rating
  user_refer_history
  user_ride_booking
  user_ride_way_point_list
  user_running_ride
  user_wallet_transaction
  transport_driver_details
  transport_driver_rating
  provider_bank_details
  provider_documents
  cash_out
  driver_ride_bid_amount
  report_issues
  report_issue_image
  api_log_detail
  topup_wallet
  sessions
  jobs
  cache
  cache_locks
)

echo "==> Safety backup of current DB"
SAFETY="/var/backups/appzimo-before-restore-$(date +%Y%m%d%H%M%S)"
mkdir -p "$SAFETY"
mysqldump --defaults-file=/etc/mysql/debian.cnf "$DB" | gzip > "$SAFETY/${DB}.sql.gz"
echo "Saved: $SAFETY/${DB}.sql.gz"

echo "==> Full restore from $BACKUP"
mysql --defaults-file=/etc/mysql/debian.cnf -e "DROP DATABASE IF EXISTS \`${DB}\`; CREATE DATABASE \`${DB}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
zcat "$BACKUP" | mysql --defaults-file=/etc/mysql/debian.cnf "$DB"

echo "==> Clear registered users / rides / sessions"
mysql --defaults-file=/etc/mysql/debian.cnf "$DB" -e "SET FOREIGN_KEY_CHECKS=0;"
for t in "${USER_TABLES[@]}"; do
  if mysql --defaults-file=/etc/mysql/debian.cnf -N -e "SELECT 1 FROM information_schema.tables WHERE table_schema='${DB}' AND table_name='${t}'" | grep -q 1; then
    mysql --defaults-file=/etc/mysql/debian.cnf "$DB" -e "TRUNCATE TABLE \`${t}\`;"
    echo "  truncated ${t}"
  fi
done
mysql --defaults-file=/etc/mysql/debian.cnf "$DB" -e "SET FOREIGN_KEY_CHECKS=1;"

echo "==> Post-restore migrate (new columns since backup)"
APP="${APP:-/var/www/app-zimo-fox-drive-v2-clone}"
if [[ -x "$APP/scripts/ec2-artisan-migrate.sh" ]]; then
  bash "$APP/scripts/ec2-artisan-migrate.sh" "$APP" appzimodevop
else
  sudo -u appzimodevop bash -lc "cd '$APP' && php artisan config:clear && php artisan migrate --force"
fi
sudo -u appzimodevop bash -lc "cd '$APP' && php artisan config:cache && php artisan view:cache"

mysql --defaults-file=/etc/mysql/debian.cnf -N -e "
SELECT CONCAT('users=', (SELECT COUNT(*) FROM users),
  ' super_admin=', (SELECT COUNT(*) FROM super_admin),
  ' general_settings=', (SELECT COUNT(*) FROM general_settings),
  ' google_login=', (SELECT is_google_login FROM general_settings LIMIT 1)) AS summary;
" "$DB"

echo "==> Done"
