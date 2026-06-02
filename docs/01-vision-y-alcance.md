# Visión y alcance — XISTI Admin

## Qué es XISTI Admin

Panel web y API REST para operar **XISTI**, app de movilidad urbana en Colombia (lanzamiento Medellín).

- Panel: `/admin`
- API móvil: `/api/customer/*`
- Pasajero y conductor comparten contrato API

## Alcance del backend

- Gestión de usuarios, conductores, vehículos y documentos
- Reservas, tarifas, negociación, wallet y pagos (Wompi)
- Notificaciones FCM y chat Firebase
- CMS (páginas legales, emails)
- Configuración multi-moneda e idioma

## Fuera de alcance

- Código nativo Flutter (repo `xisti-mobile`)
- Infraestructura IaC (documentada en `docs/SETUP_SERVICIOS.md`)

## Identidad

- Marca: **XISTI** — *Fácil y Seguro*
- Comisión plataforma: 8% + IVA 19%
- Negociación tarifa: pasos de 500 COP
