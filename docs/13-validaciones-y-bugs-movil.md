# Validaciones API y bugs críticos (admin → app móvil)

## Configuración en Admin

**Site Setting → General Setting** (`/admin/site-setting`):

| Campo | Uso en app |
|-------|------------|
| Paso negociación tarifa (COP) | `fare_negotiation_step` — bug **B3** (±1000, no ±1) |
| IVA sobre comisión (%) | `vat_rate_on_commission` — factura conductor **B6** |
| Conductor puede cancelar hasta estado | `driver_can_cancel_until_status` — **B4** (default `3` = llegó) |

**Service Setting**: `admin_commission` (%) — comisión plataforma en facturas.

**Vehicle Services**: `display_order` y `service_mode` — orden home **B2**.

## APIs que exponen la config

- `POST /api/customer/app-version-check`
- `POST /api/customer/home`
- `POST /api/customer/login` (respuesta perfil)
- `POST /api/customer/ride-pricing`

Campos: `fare_negotiation_step`, `vat_rate_on_commission`, `admin_commission_percent`, `driver_can_cancel_until_status`.

## Validaciones servidor (anti-abuso)

| Campo | Regla |
|-------|--------|
| Teléfono CO | 10 dígitos (`message_code` 385) |
| Placa carro | 3 letras + 3 números (`371`) |
| Placa moto | ABC12D o ABC123 |
| Documento | 6–10 dígitos (`387`) |
| Courier destinatario | teléfono CO, nombre/descripción longitud máxima |
| Oferta COP | múltiplo de `fare_negotiation_step` (`388`) |

## Bugs del plan — qué cubre el admin

| Bug | Admin/API | Falta en móvil |
|-----|-----------|----------------|
| B3 ±1000 | Sí | Leer `fare_negotiation_step` en UI oferta |
| B4 cancelar | API `driver_can_cancel` + cancel estado ≤3 | Mostrar botón si `driver_can_cancel==1` |
| B6 facturas | PDF + JSON comisión/IVA | Mostrar líneas en sheet |
| B2 orden | `display_order` | — |
| B1 courier | Campos en booking/detail | UI campos reserva |
| B5 ruta live | `google-route-detail` existe | Recalcular polyline en stream |
| B7 validaciones | Reglas API | Alinear `validator.dart` |
