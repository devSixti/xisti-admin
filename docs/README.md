# Documentación Appzimo Admin

Backend Laravel para administración y API de las apps móviles Appzimo (transporte / ride-sharing).

## Índice

| # | Documento | Contenido |
|---|-----------|-----------|
| 01 | [Visión y alcance](01-vision-y-alcance.md) | Producto, actores, módulos |
| 02 | [Arquitectura](02-arquitectura.md) | Diagramas, capas, integraciones |
| 03 | [Despliegue AWS](03-despliegue-aws.md) | EC2, Nginx, PHP-FPM, MySQL, SSH |
| 04 | [API móvil](04-api-movil.md) | Endpoints, auth, códigos de respuesta |
| 05 | [Manual administrador](05-manual-administrador.md) | Panel `/admin`, roles, flujos |
| 06 | [Base de datos](06-base-de-datos.md) | Entidades, tablas principales |
| 07 | [CI/CD](07-ci-cd.md) | GitHub Actions, calidad, releases |
| 08 | [Plan seguridad](08-seguridad-plan-remediacion.md) | Hallazgos y remediación (**sin implementar**) |
| 09 | [Alineación EC2 ↔ GitHub](09-alineacion-ec2-github.md) | Divergencias y sincronización |
| 10 | [Integración apps móviles](10-integracion-apps-moviles.md) | Contrato con agente Flutter |
| — | [Coordinación multiagente](../AppZimo-Mobile/docs/09-coordinacion-multiagente.md) | **Canónico** Mobile ↔ Admin (repo Mobile) |

## Diagramas

- [Arquitectura general](diagrams/arquitectura-general.mmd)
- [Flujo reserva de viaje](diagrams/flujo-reserva-viaje.mmd)
- [Flujo autenticación API](diagrams/flujo-auth-api.mmd)

## Repositorios relacionados

| Repo | Uso |
|------|-----|
| `DESZIMO/Appzimo-Admin` | **Canónico** — este proyecto |
| `whitelabelfox-pvt-ltd/app-zimo-fox-drive-v2-clone` | Deploy histórico en EC2 |
| `AppZimo-Mobile` (local) | Apps Flutter cliente/conductor |

## Contacto operativo

- **Admin URL:** https://admin.appzimo.com
- **API base:** `https://admin.appzimo.com/api/`
- **Agente Cursor:** ver [AGENTS.md](../AGENTS.md) en la raíz del repo
