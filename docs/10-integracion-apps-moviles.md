# 10 — Integración con aplicaciones móviles

## Coordinación entre agentes

Protocolo y memoria compartida: **[`AppZimo-Mobile/docs/09-coordinacion-multiagente.md`](../../AppZimo-Mobile/docs/09-coordinacion-multiagente.md)**

Incluye fuentes de verdad, checklist de cambios coordinados, bugs `B1`–`B7` y reglas de sesión paralela en Cursor.

## Contexto

Las apps Flutter viven en el workspace **`AppZimo-Mobile`** (agente separado). Este backend expone **un único contrato API** bajo `/api/customer/*` para:

- App **pasajero** (customer)
- App **conductor** (driver) — mismos endpoints; el rol se determina en servidor por estado del usuario

## URL base compartida

```
https://admin.appzimo.com/api/
```

En desarrollo, el móvil puede apuntar a staging; mantener paridad de rutas.

## Contrato de autenticación

Después de login/registro:

```json
{
  "user_id": "<int>",
  "access_token": "<string>"
}
```

Enviar en **body** de cada POST protegido (no Bearer header estándar hoy).

Códigos `status` documentados en `docs/04-api-movil.md`.

## Endpoints críticos para coordinación

| Flujo móvil | Endpoint backend |
|-------------|------------------|
| Splash / versión | `POST customer/app-version-check` |
| Login OTP/social | `POST customer/login`, `register` |
| Home mapa | `POST customer/home` |
| Reservar viaje | `POST customer/ride-booking` |
| Conductor acepta | `POST customer/accept-ride`, `driver-bid` |
| Estado viaje | `POST customer/get-ride-status`, `update-ride-status` |
| Pago | `POST customer/ride-payment` + WebView Wompi |
| Push device | `POST customer/update-device-token` |
| Chat incidencia | Firebase RTDB + `report-issue/*` |

## Pagos Wompi

Flujo híbrido:

1. API crea intención / URL
2. App abre WebView → rutas web `/payments/*`
3. Webhook `POST /api/wompi/webhook` o `/webhook/wompi`

Coordinar URLs de retorno en builds iOS/Android con `admin.appzimo.com`.

## Firebase

- **FCM:** token en `device_token` del usuario
- **Chat:** paths RTDB gestionados por `FirebaseService` (viaje e incidencias)

Cambios en estructura Firebase requieren **acuerdo bilateral** admin + móvil.

## Google Maps

Proxy backend:

- `google-autocomplete-places`
- `google-place-detail`
- `google-route-detail`

El móvil **no debería** exponer API key de Maps en producción si se usa este patrón.

## Internacionalización

Header `select-language` alineado con constantes en `resources/lang` y tablas `language_*`.

## Versionado API

Hoy no hay prefijo `/v1/`. Cambios breaking requieren:

1. Comunicación con agente móvil
2. `app-version-check` + force update si es necesario
3. Documentar en changelog conjunto

## Checklist cambio coordinado

Ver tabla completa en [`09-coordinacion-multiagente.md`](../../AppZimo-Mobile/docs/09-coordinacion-multiagente.md). Resumen lado Admin:

| Paso | Backend (este agente) |
|------|----------------------|
| 1 | Diseño API + migración |
| 2 | Implementar + actualizar `docs/04-api-movil.md` |
| 3 | Deploy EC2 + `php artisan migrate --force` |
| 4 | Verificar webhooks Wompi / facturas Blade |
| 5 | Entrada en changelog coordinado del PR |

## Trabajo en paralelo

Cuando el usuario indique “coordinar con Mobile”, citar bugs `B1`–`B7` desde `AppZimo-Mobile/docs/08-bugs-criticos-plan.md` y seguir el protocolo del doc 09.

## Repositorios

| Componente | Ubicación |
|----------|-----------|
| Backend | `Appzimo-Admin` (este) |
| Móvil | `AppZimo-Mobile/app-zimo-fox-drive-v2-clone-customer-app` (+ variantes conductor si existen) |
