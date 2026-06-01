# XISTI Admin

Backend Laravel 11 para **XISTI App**: panel `/admin` + API `/api/customer/*`.

Derivado del motor Fox Drive, con **autorización de app y FCM reimplementados** (sin dependencia SourceGuardian).

## Inicio rápido (local)

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configurar DB en .env
php artisan migrate --seed
php artisan serve
```

Admin local: `http://127.0.0.1:8000/admin`  
Credencial seed: ver `database/seeders/SuperAdminSeeder.php` (cambiar en producción).

## Documentación

- Índice general: [`../docs/README.md`](../docs/README.md)
- **Guía completa de servicios (Firebase, GCP, Wompi, Twilio, DNS, tiendas):** [`../docs/SETUP_SERVICIOS.md`](../docs/SETUP_SERVICIOS.md)

## Proyecto hermano

- App móvil: [`../xisti-mobile/`](../xisti-mobile/)

## Repositorio

https://github.com/devSixti/xisti-admin

## Zimo

Este repo **no** despliega ni altera `admin.appzimo.com`. Instancia y base de datos separadas.
