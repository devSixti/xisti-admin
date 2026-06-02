# API móvil (XISTI)

Base URL:

```
https://admin.xistiapp.com/api/
```

Prefijo cliente/conductor unificado:

```
/api/customer/*
```

## Autenticación de app

Header `Authorization` construido con `app_key` de Site Settings (ver `scripts/generate-app-auth-header.php`).

## Endpoints frecuentes

| Método | Ruta | Uso |
|--------|------|-----|
| POST | `/customer/app-version-check` | Config inicial, social login flags |
| POST | `/customer/login` | Login email/teléfono |
| POST | `/customer/register` | Registro |
| GET | `/customer/general-settings` | Ajustes generales |

Contrato completo: inspeccionar `routes/api.php` y controladores en `app/Http/Controllers/Api/`.

## App móvil

Repositorio: https://github.com/devSixti/xisti-mobile

Coordinación de bugs/features: issues en GitHub por repo.
