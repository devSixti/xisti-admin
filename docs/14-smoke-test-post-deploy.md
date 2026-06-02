# Smoke test post-deploy (XISTI)

Ejecutar tras cada deploy a producción o staging.

## Precondiciones

- Admin accesible: `https://admin.xistiapp.com` (o IP `http://54.159.169.235` si DNS pendiente)
- `.env` con Firebase, Twilio y Wompi configurados según entorno

## Checks API

```bash
curl -s -X POST https://admin.xistiapp.com/api/customer/app-version-check \
  -H "Content-Type: application/json" \
  -d '{"app_version":"1.0.2","device_type":"android"}' | jq .
```

Verificar en respuesta:

- `is_google_login`, `is_facebook_login` según configuración
- `enable_encomiendas_mobile = 1`
- `enable_expreso_mobile = 0`
- `currency_list` no vacío (COP presente)

## Checks admin

- Login en `/admin/login`
- Site Settings muestra XISTI / Medellín
- Vehicle services: Taxi, Moto, Courier activos; Rickshaw inactivo

## Checks mobile (manual)

- Icono y splash XISTI
- Onboarding urbano Medellín
- Login social visible si flags activos
- Moneda COP seleccionable
