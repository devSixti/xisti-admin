# Roadmap API/Admin v1.0.2

Documento completo: `AppZimo-Mobile/docs/15-roadmap-v1.0.2-stage-main.md`

## Cambios Admin/API (esta entrega)

- Wallet: debito comisión al cobro efectivo (`WalletSettlementHelper`)
- Topup presets Colombia 13000/26000/39000
- Envíos visibles para conductores de transporte activo
- PDF facturas conductor/pasajero

Migración: `2026_05_25_120000_set_colombia_topup_wallet_presets.php`

## Compatibilidad tiendas 1.0.1 (temporal)

Mientras la app en stores sigue en **1.0.1**, el backend no exige peso/dimensiones de envío:

- `App\Helpers\AppMobileSettingsHelper::REQUIRE_COURIER_PACKAGE_DIMENSIONS = false`

Al publicar mobile **1.0.2**, cambiar a `true` y desplegar Admin.
