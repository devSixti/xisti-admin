# 04 — API para aplicaciones móviles

## Base URL

```
https://admin.appzimo.com/api/
```

Prefijo principal: **`/api/customer/`** (pasajero y conductor comparten rutas; el rol se distingue por campos en `users`).

## Headers habituales

| Header | Uso |
|--------|-----|
| `Content-Type` | `application/json` o `multipart/form-data` (uploads) |
| `select-language` | Código idioma (`en`, `es`, …) — middleware `setLocaleLang` |
| `Accept` | `application/json` |

## Autenticación

Tras login/registro exitoso, incluir en cada endpoint protegido:

```json
{
  "user_id": 123,
  "access_token": "token_generado_servidor"
}
```

Validación: `UserClassApi::checkUserAllow()`.

Login inicial requiere pasar validación de app (`AuthAlertClass`) y `device_token`.

## Endpoints por categoría

### Públicos / semi-públicos (sin token de usuario)

| Método | Ruta | Controlador |
|--------|------|-------------|
| POST | `/api/customer/login` | Auth\LoginController |
| POST | `/api/customer/finger-login` | Auth\LoginController (throttle) |
| POST | `/api/customer/register` | Auth\RegisterController |
| POST | `/api/customer/app-version-check` | CustomerApiController |
| POST | `/api/customer/country-and-currency-list` | CustomerApiController |
| POST | `/api/google-autocomplete-places` | CustomerApiController |
| POST | `/api/google-place-detail` | CustomerApiController |
| POST | `/api/google-route-detail` | CustomerApiController |
| POST | `/api/wompi/webhook` | CustomerApiController |

### Perfil y cuenta

| POST | `/api/customer/edit-profile` |
| POST | `/api/customer/change-password` |
| POST | `/api/customer/logout` |
| POST | `/api/customer/contact-verification` |
| POST | `/api/customer/resend-otp-verification` |
| POST | `/api/customer/remove-account` |
| POST | `/api/customer/customer-details` |
| POST | `/api/customer/update-device-token` |

### Billetera y pagos

| POST | `/api/customer/add-card` |
| POST | `/api/customer/card-list` |
| POST | `/api/customer/add-wallet-balance` |
| POST | `/api/customer/wallet-transaction` |
| POST | `/api/customer/get-wallet-balance` |
| POST | `/api/customer/wallet-transfer` |
| POST | `/api/customer/request-cash-out` |
| POST | `/api/customer/ride-payment` |

### Viajes (pasajero)

| POST | `/api/customer/ride-booking` |
| POST | `/api/customer/cancel-ride` |
| POST | `/api/customer/accept-ride` |
| POST | `/api/customer/get-customer-running-service` |
| POST | `/api/customer/user-ride-history` |
| POST | `/api/customer/ride-pricing` |

### Conductor

| POST | `/api/customer/available-ride-request` |
| POST | `/api/customer/driver-bid` |
| POST | `/api/customer/update-ride-status` |
| POST | `/api/customer/driver-ride-history` |
| POST | `/api/customer/driver-earning` |
| POST | `/api/customer/upload-document` |
| POST | `/api/customer/service-register` |
| POST | `/api/customer/heat-map` |

### Incidencias

Prefijo: `/api/customer/report-issue/`

- `faqs`, `draft`, `update`, `upload-image`, `history`, `chat-photos`, etc.

## Pagos Wompi (WebView)

Rutas web para retorno móvil:

- `GET /payments/wompi/redirect`
- `GET /payments/success`
- `GET /payments/failed`
- `POST /webhook/wompi` (alias web)

## Diagrama auth

Ver [diagrams/flujo-auth-api.mmd](diagrams/flujo-auth-api.mmd)

## Diagrama viaje

Ver [diagrams/flujo-reserva-viaje.mmd](diagrams/flujo-reserva-viaje.mmd)

## Compatibilidad con agente móvil

Al cambiar contratos:

1. Documentar aquí y en `10-integracion-apps-moviles.md`
2. Mantener `message_code` estables o versionar API
3. Probar con app en staging antes de producción

## Listado completo de rutas

En servidor o local:

```bash
php artisan route:list --path=api
```
