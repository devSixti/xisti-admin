# Agente XISTI Admin (Backend Laravel)

Panel administrativo y API REST para **XISTI** (pasajero + conductor).

## Stack

- Laravel 11, PHP 8.2, MySQL 8
- Autorización de app: `App\Services\AppAuthorizationService` (sin SourceGuardian)
- Push: `FcmAccessTokenService` + Firebase FCM HTTP v1
- Pagos: Wompi (Colombia)

## API móvil

Base: `https://admin.xistiapp.com/api/customer/`

## Autorización de app (login/registro)

La app envía header `Authorization` derivado de `app_key` (ver `scripts/generate-app-auth-header.php`).

## Comandos

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
./vendor/bin/phpunit
php scripts/generate-app-auth-header.php
```

## Despliegue

Ver `../docs/SETUP_SERVICIOS.md`.

## Importante

- Colocar `service-account.json` en `storage/app/firebase/` (no commitear).
- Configurar `XISTI_APP_KEY` en `.env` y en Admin → Site Settings → app_key (deben coincidir).
