# 03 — Despliegue en AWS (EC2)

## Instancia actual (auditoría 2026-05-20)

| Parámetro | Valor |
|-----------|--------|
| Host DNS | `ec2-18-208-4-15.compute-1.amazonaws.com` |
| IP pública | `18.208.4.15` |
| SSH | Puerto **927**, usuario `ubuntu` |
| Clave | `~/.ssh/app-zimo-root-key.pem` |
| Alias SSH local | `ssh zimo-ec2` |
| Ruta aplicación | `/var/www/app-zimo-fox-drive-v2-clone` |
| Usuario dueño código | `appzimodevop` |
| Dominio admin | `admin.appzimo.com` (HTTPS 443) |
| Document root | `.../public` |

## Stack en servidor

```bash
systemctl status nginx php8.2-fpm mysql   # todos active
```

- **PHP:** 8.2-FPM socket `/run/php/php8.2-fpm.sock`
- **Base de datos:** MySQL `127.0.0.1:3306`, BD `db_zemo_app`
- **Sin Docker** en la instancia auditada

## Nginx

Archivo activo: `/etc/nginx/sites-enabled/appzimo`

- SSL: `/var/www/ssl-file-path/appzimo-merge.crt` + `appzimo.key`
- Laravel front controller: `try_files $uri $uri/ /index.php?$query_string`
- phpMyAdmin path: `/data-vault-appzimo` → `/var/www/phpmyadmin-storage/`

## Assets estáticos en Git

| Ruta | Contenido |
|------|-----------|
| `public/assets/front/` | Landing, admin UI (~93 archivos) |
| `public/assets/images/` | Mapas, plantillas email, iconos, categorías de servicio (~72 archivos) |

Las carpetas de **uploads en runtime** (`provider-documents/`, `provider-vehicle-image/`, `report-issue/` salvo `logo/`) están en `.gitignore`.

## Despliegue continuo (CD)

Tras cada **push a `main`** con CI verde, GitHub Actions ejecuta el job `deploy-production` en `.github/workflows/backend.yml`.

Configuración de secretos y prerrequisitos EC2: **`docs/07-ci-cd.md`**.

## Despliegue manual (contingencia)

Si CD falla o aún no hay secretos en GitHub:

```bash
ssh zimo-ec2
sudo su - appzimodevop
cd /var/www/app-zimo-fox-drive-v2-clone
git fetch origin && git checkout main && git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
exit
sudo systemctl reload php8.2-fpm nginx
```

## Variables de entorno producción

Archivo: `/var/www/app-zimo-fox-drive-v2-clone/.env`

Valores observados (sin secretos):

- `APP_ENV=local` ⚠️ debería ser `production` (ver plan seguridad)
- `APP_DEBUG=false`
- `APP_TIMEZONE=America/Bogota`
- `DB_DATABASE=db_zemo_app`
- `QUEUE_CONNECTION=database`

## Checklist post-deploy

- [ ] `curl -sk -o /dev/null -w "%{http_code}" https://admin.appzimo.com/` → 200
- [ ] Login admin funcional
- [ ] `POST /api/customer/app-version-check` responde JSON
- [ ] Logs sin errores: `storage/logs/laravel.log`
- [ ] Migraciones aplicadas: `php artisan migrate:status`


## Conexión desde operador

`~/.ssh/config`:

```
Host zimo-ec2
    HostName ec2-18-208-4-15.compute-1.amazonaws.com
    User ubuntu
    Port 927
    IdentityFile ~/.ssh/app-zimo-root-key.pem
    IdentitiesOnly yes
```

Si aparece `Too many authentication failures`, usar `IdentitiesOnly yes` o `ssh-add -D`.
