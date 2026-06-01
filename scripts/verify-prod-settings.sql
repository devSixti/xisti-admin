-- Verificación post-migración en producción (MySQL)
SELECT fare_negotiation_step, vat_rate_on_commission, driver_cancel_until_status, driver_min_amount
FROM general_settings LIMIT 1;

SELECT migration FROM migrations
WHERE migration LIKE '2026_05_%'
ORDER BY migration;

SHOW COLUMNS FROM transport_driver_details LIKE 'vehicle_image_%';
SHOW COLUMNS FROM transport_driver_details LIKE 'accept_%';
SHOW COLUMNS FROM vehicle_services LIKE 'service_mode';

SELECT COUNT(*) AS eligibility_rows FROM vehicle_type_service_eligibility;
