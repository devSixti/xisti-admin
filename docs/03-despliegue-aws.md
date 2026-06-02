# Despliegue AWS (XISTI)

## Servidor producción

| Campo | Valor |
|-------|--------|
| Host | `54.159.169.235` |
| SSH port | `987` |
| Usuario | `ubuntu` |
| Ruta aplicación | `/var/www/xisti-admin` |
| Dominio admin | `admin.xistiapp.com` |
| PHP-FPM | `php8.5-fpm` (ajustar según servidor) |

## Deploy automático

Push a `main` en https://github.com/devSixti/xisti-admin dispara `.github/workflows/backend.yml`.

Secrets requeridos en GitHub → Settings → Secrets:

| Secret | Descripción |
|--------|-------------|
| `EC2_HOST` | IP o hostname EC2 |
| `EC2_SSH_PORT` | Puerto SSH (987) |
| `EC2_SSH_USER` | `ubuntu` |
| `EC2_SSH_PRIVATE_KEY` | Clave PEM |
| `EC2_DB_MIGRATE_USERNAME` | Usuario MySQL con ALTER (opcional) |
| `EC2_DB_MIGRATE_PASSWORD` | Password migrate user |

## Deploy manual

```bash
cd xisti-admin
./scripts/manual-deploy-ec2.sh
```

## Post-deploy en servidor

```bash
cd /var/www/xisti-admin
sudo -u ubuntu bash scripts/ec2-artisan-migrate.sh /var/www/xisti-admin ubuntu
sudo -u ubuntu php artisan config:cache
sudo -u ubuntu php artisan view:cache
sudo systemctl reload php8.5-fpm nginx
```

## Smoke test

Ver [14-smoke-test-post-deploy.md](./14-smoke-test-post-deploy.md).
