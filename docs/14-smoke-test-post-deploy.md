# Smoke test post-deploy (AppZimo)

Ejecutar en **~30 min** tras cada deploy de Admin (EC2) y build móvil QA.

## Pre-requisitos

- APK/IPA desde GitHub Actions (rama `main`)
- Acceso admin: `https://admin.appzimo.com`
- Usuario pasajero y conductor de prueba (Colombia +57)

## 1. Configuración API

```bash
curl -s -X POST https://admin.appzimo.com/api/customer/app-version-check \
  -H "Content-Type: application/json" \
  -d '{"app_version":"1.0.2","device_type":1}' | jq '.fare_negotiation_step, .vat_rate_on_commission'
```

Esperado: `fare_negotiation_step` = **500**, IVA = **19**.

## 2. Auth Google + OTP (misma cuenta)

1. Login Google → completar teléfono en perfil.
2. Logout → login OTP con el **mismo** número.
3. Debe entrar a la **misma** cuenta (no cuenta nueva).

## 3. Conductor en línea + wallet

1. Conductor con wallet **0** → toggle **En línea** → debe funcionar (sin error servidor).
2. Recarga Wompi **&lt; 13.000 COP** → rechazada.
3. Recarga **≥ 13.000** → aceptada; volver en línea → lista de viajes carga.

## 4. Tarifa negociable (B3)

1. Reservar viaje → oferta ± debe mover **500 COP** por paso (no 1 COP).

## 5. Envíos / courier (B1)

1. Servicio envíos → teléfono destinatario **10 dígitos** sin error API.

## 6. Borrado cuenta + wallet

1. Usuario con saldo wallet → eliminar cuenta.
2. En BD: `user_wallet_transaction` con `subject_code` **19**, saldo **0**.

## 7. Report issue (auth)

1. Durante viaje → abrir reporte / FAQs → debe exigir token válido (no 500).

## SQL verificación rápida (EC2)

Ver `scripts/verify-prod-settings.sql`.
