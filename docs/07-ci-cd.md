# CI/CD (XISTI Admin)

Repositorio: https://github.com/devSixti/xisti-admin

Workflow: `.github/workflows/backend.yml`

## Pipeline

1. **CI** — PHPUnit en SQLite, `route:list`, autoload check.
2. **CD** (solo `push` a `main`) — tarball → SCP → EC2 `/var/www/xisti-admin` → `composer install --no-dev` → `scripts/ec2-artisan-migrate.sh` → cache config/views → reload PHP-FPM + Nginx.

## Variables de entorno CD

| Variable | Valor |
|----------|--------|
| `APP_DIR` | `/var/www/xisti-admin` |
| `DEPLOY_USER` | `ubuntu` |
| `CD_STAGING` | `/tmp/xisti-cd-staging` |

## Migraciones

El script `scripts/ec2-artisan-migrate.sh` ejecuta seeders XISTI incluyendo:

- `WorldCurrencySeeder`, `LanguageListsSeeder`
- `XistiEnableSocialLoginSeeder`
- `PageSettingsSeeder`, `EmailTemplatesSeeder`, `VehicleServicesSeeder`
- `XistiPurgeLegacyBrandingSeeder`

Usa credenciales de `/etc/mysql/debian.cnf` o secrets GitHub:

| Secret | Uso |
|--------|-----|
| `EC2_DB_MIGRATE_USERNAME` | Usuario MySQL con permiso `ALTER` (migraciones DDL) |
| `EC2_DB_MIGRATE_PASSWORD` | Contraseña del usuario migrate |

Si faltan, el script usa `DB_USERNAME` del `.env` de la app (p. ej. `xisti`) y las migraciones DDL pueden fallar.

## Deploy manual alternativo

`./scripts/manual-deploy-ec2.sh` — misma lógica que CD sin GitHub Actions.
