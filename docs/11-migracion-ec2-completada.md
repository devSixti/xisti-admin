# 11 — Migración EC2 completada (2026-05-21)

## Resumen

Producción en `https://admin.appzimo.com` quedó desplegada con el código canónico **DESZIMO/Appzimo-Admin** (rsync + migraciones SQL), conservando:

- `.env` de producción
- `storage/` (uploads, logs)
- Base de datos `db_zemo_app` (sin `migrate:fresh`)

## Backup

| Artefacto | Ruta en EC2 |
|-----------|-------------|
| `.env` | `/var/backups/appzimo-migration-20260521230939/.env` |
| Código (tar) | `/var/backups/appzimo-migration-20260521230939/app-code.tar.gz` |
| MySQL (intento) | `/home/appzimodevop/db_backup_pre_migration.sql.gz` |

## Cambios aplicados en servidor

1. Sincronización de código desde repo local → `/tmp/appzimo-admin-deploy` → `/var/www/app-zimo-fox-drive-v2-clone`
2. `composer install --no-dev`
3. Migraciones `2026_05_21_*` aplicadas vía MySQL admin (`debian-sys-maint`)
4. `ResetPasswordController` presente (API `change-password`)
5. Permisos `storage` / `bootstrap/cache` corregidos
6. `config:cache` + `view:cache` (sin `route:cache` — rutas duplicadas legacy en `web.php`)

## Git en EC2

```bash
git remote set-url origin git@github.com:DESZIMO/Appzimo-Admin.git
```

**Deploy key** generada para `appzimodevop` (añadir en GitHub → Settings → Deploy keys → Read):

```
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIIpy1dFXm/Pn/hlrM6F0u03dBbM0rxW3Jfm5sVQAg+Rh appzimodevop@ip-172-31-23-113
```

SSH config en servidor (`~/.ssh/config` de `appzimodevop`):

```
Host github.com-deszimo
    HostName github.com
    User git
    IdentityFile ~/.ssh/deszimo_deploy
    IdentitiesOnly yes
```

**CD (desde 2026-05-22):** el pipeline ya no hace `git pull` en EC2; sube el código con `scp-action` + `rsync`. La deploy key solo hace falta si quieres `git fetch` manual en el servidor.

Tras añadir la deploy key (opcional), probar:

```bash
sudo -u appzimodevop ssh -T git@github.com-deszimo
sudo -u appzimodevop git -C /var/www/app-zimo-fox-drive-v2-clone fetch origin
```

## CD GitHub Actions — secretos requeridos

| Secret | Valor |
|--------|--------|
| `EC2_HOST` | `ec2-18-208-4-15.compute-1.amazonaws.com` |
| `EC2_SSH_PORT` | `927` |
| `EC2_SSH_USER` | `ubuntu` |
| `EC2_SSH_PRIVATE_KEY` | PEM `app-zimo-root-key.pem` |
| `EC2_DEPLOY_USER` | `appzimodevop` |
| `EC2_DEPLOY_PATH` | `/var/www/app-zimo-fox-drive-v2-clone` |

Variable opcional: `EC2_GIT_SSH_HOST` = `github.com-deszimo`

Las migraciones en CD usan `scripts/ec2-artisan-migrate.sh` (credenciales de `/etc/mysql/debian.cnf` o secrets `EC2_DB_MIGRATE_*`). El `.env` de la app sigue usando `zimo_restricted_user` sin `ALTER`.

## Base de datos limpia (2026-05-21 23:23 UTC)

Se recreó `db_zemo_app` desde cero con migraciones + seeders completos.

| Backup previo | `/var/backups/appzimo-pre-fresh-20260521232310/db_zemo_app_full.sql.gz` |
| Módulos admin | 66 (`admin_module`) |
| Configuración | `general_settings` seed |

**Importante:** Se perdieron datos de producción anteriores (usuarios, viajes, conductores). Restaurar solo desde el backup anterior si hace falta.

## Admin login (BD limpia)

- URL: `https://admin.appzimo.com/admin/login`
- Email: `admin@appzimo.com` (rol 1, SuperAdminSeeder)
- Contraseña inicial: la definida en `database/seeders/SuperAdminSeeder.php` (cambiar tras primer acceso)

## Smoke tests post-migración

- [x] `/` → 200
- [x] `/admin/login` → 200
- [x] `/up` → 200
- [x] `super_admin` → 1 fila
- [x] Migraciones `2026_05_21_*` → Ran

## Pendiente opcional

- Añadir deploy key en GitHub para `git pull` desde CD
- Configurar secretos de CD en el repo
- Corregir nombres de ruta duplicados en `web.php` para habilitar `route:cache`
- `APP_ENV=production` en `.env` (hoy `local`)
