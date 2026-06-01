# 02 — Arquitectura técnica

## Vista de componentes

Ver diagrama: [diagrams/arquitectura-general.mmd](diagrams/arquitectura-general.mmd)

## Capas de la aplicación

```
┌─────────────────────────────────────────────────────────┐
│  Presentación                                           │
│  • routes/web.php → Blade admin + páginas públicas      │
│  • routes/api.php → JSON para móvil                     │
└───────────────────────────┬─────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────┐
│  Controladores (app/Http/Controllers)                   │
│  • Api\CustomerApiController, Api\Transport\UserController │
│  • AdminController, TransportController                 │
└───────────────────────────┬─────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────┐
│  Dominio / servicios                                    │
│  • UserClassApi, AdminClass, NotificationClass, TokenClassApi │
│  • FirebaseService                                      │
│  • AuthAlertClass (SourceGuardian — licencia app)         │
└───────────────────────────┬─────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────┐
│  Persistencia (Eloquent Models + 83 migrations)       │
│  MySQL: users, user_ride_booking, transport_*, etc.     │
└─────────────────────────────────────────────────────────┘
```

## Autenticación dual

### Panel admin

- Guard: `auth:admin` → modelo `Admin` / tabla `super_admin`
- Sesión PHP + cookies
- Middleware `adminrole`: menú dinámico desde `admin_module`, `admin_permission`
- Middleware `revalidate`: evita caché del navegador en formularios sensibles

### API móvil

- **No usa Sanctum** en producción (aunque el paquete está instalado)
- Cada request autenticado envía `user_id` + `access_token`
- Validación en `UserClassApi::checkUserAllow()`
- Login protegido por `AuthAlertClass::checkAuthorizationApp()` (binario SourceGuardian)

### Códigos de estado JSON (API)

| status | Significado |
|--------|-------------|
| 0 | Error / validación |
| 1 | Éxito |
| 2 | Registro pendiente / no verificado |
| 3 | Usuario bloqueado |
| 4 | Token no coincide |
| 5 | Usuario no encontrado |

## Middleware relevante

| Alias | Función |
|-------|---------|
| `setLocaleLang` | Idioma por header `select-language` o sesión |
| `adminrole` | Permisos admin |
| `revalidate` | Headers no-cache |
| `throttle` | Rate limit en `finger-login` |

CSRF excluido para: `/webhook/wompi`

## Integraciones externas

| Servicio | Uso | Configuración |
|----------|-----|---------------|
| Firebase RTDB | Chat por viaje / incidencias | `FirebaseService`, reglas vía API |
| Firebase FCM | Push masivos y transaccionales | `NotificationClass` |
| Wompi | Tarjetas, recarga wallet, webhooks | `general_settings`, columnas en `card_details` |
| Twilio Verify | OTP registro / cambio contacto | `TokenClassApi` |
| Google Maps | Autocomplete, detalle lugar, rutas | `CustomerApiController` |
| Socialite | Login y borrado cuenta social | `config/services.php` |

## Estructura de directorios clave

```
app/
├── Classes/          # Lógica de negocio compartida
├── Http/Controllers/
│   ├── Api/          # Móvil
│   └── Auth/         # Login admin
├── Models/           # 47 modelos Eloquent
├── Services/         # FirebaseService
└── Jobs/             # Jobs de cola (si se activan workers)
config/               # Incluye constantes de estado de viaje
database/migrations/  # 83 archivos
resources/views/admin/  # Panel Blade
public/               # Document root Nginx
routes/
├── api.php
└── web.php
```

## Health check

Laravel 11 expone `GET /up` (bootstrap routing).

## Limitaciones conocidas

- Código `AuthAlertClass` ofuscado: requiere loader SourceGuardian en PHP del servidor
- Ruta API `POST /change-password` referenciaba controlador inexistente (corregido en repo canónico; ver doc 09)
- `cancel-ride` definido dos veces en `routes/api.php` (última definición gana)

## Rendimiento y escalado (actual)

- Single EC2 con PHP-FPM pool `www`
- MySQL local en la misma instancia
- Sin Redis en `.env` de producción observado
- Colas en BD sin worker systemd/supervisor activo

Próximos pasos de arquitectura (recomendados, no implementados): ALB, RDS, ElastiCache, workers Horizon, secretos en AWS Secrets Manager.
