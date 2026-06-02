# Documentación XISTI Admin

Backend Laravel para administración y API de las apps móviles **XISTI** (movilidad urbana).

## Índice

| Doc | Contenido |
|-----|-----------|
| [01-vision-y-alcance.md](./01-vision-y-alcance.md) | Alcance del panel y API |
| [02-arquitectura.md](./02-arquitectura.md) | Módulos Laravel |
| [03-despliegue-aws.md](./03-despliegue-aws.md) | EC2, Nginx, SSL |
| [04-api-movil.md](./04-api-movil.md) | Contrato `/api/customer/*` |
| [05-manual-administrador.md](./05-manual-administrador.md) | Uso del panel |
| [06-base-de-datos.md](./06-base-de-datos.md) | Migraciones y seeders |
| [07-ci-cd.md](./07-ci-cd.md) | GitHub Actions → EC2 |
| [14-smoke-test-post-deploy.md](./14-smoke-test-post-deploy.md) | Verificación post-deploy |
| [VEHICLE_ICONS.md](./VEHICLE_ICONS.md) | Iconos de servicios vehiculares |

Documentación operativa del monorepo: [`../../docs/README.md`](../../docs/README.md) y [`../../docs/SETUP_SERVICIOS.md`](../../docs/SETUP_SERVICIOS.md).

## Entornos

| | Valor |
|---|--------|
| Admin URL | https://admin.xistiapp.com |
| API base | https://admin.xistiapp.com/api/ |
| EC2 | `54.159.169.235:987` |
| Ruta app | `/var/www/xisti-admin` |
| Usuario deploy | `ubuntu` |
| Base de datos | `db_xisti_app` |
| Firebase | `xisti-app-ad901` |
| Repo | https://github.com/devSixti/xisti-admin |

## Proyecto hermano

- App móvil Flutter: [`../../xisti-mobile/`](../../xisti-mobile/) — https://github.com/devSixti/xisti-mobile
