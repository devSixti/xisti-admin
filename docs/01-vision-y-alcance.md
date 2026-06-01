# 01 — Visión y alcance

## Qué es Appzimo Admin

Plataforma **monolítica Laravel 11** que centraliza:

1. **API REST** para apps móviles (pasajero y conductor en el mismo backend).
2. **Panel web de super-administración** (`/admin`) para operaciones, soporte y configuración.
3. **Páginas públicas** (términos, privacidad, eliminación de cuenta, facturas, pagos Wompi).

Producto de mercado: servicio de transporte con reservas, ofertas de conductores (bidding), billetera, referidos, courier, mapa de calor, SOS y reporte de incidencias.

## Actores

| Actor | Canal | Descripción |
|-------|-------|-------------|
| Pasajero (customer) | App móvil | Reserva viajes, paga, califica |
| Conductor (driver) | App móvil | Mismos endpoints bajo prefijo `customer`; flags `is_driver_*` en `users` |
| Super admin | Navegador `/admin` | Gestión global con permisos por módulo |
| City admin | Panel (sub-rol) | Áreas geográficas restringidas |
| Sistemas externos | Webhooks / APIs | Wompi, Firebase, Twilio, Google Maps |

## Módulos funcionales

### API móvil (`/api/customer/*`)

- Autenticación: email/OTP, redes sociales, huella (`finger-login`)
- Perfil, direcciones, billetera, tarjetas (Wompi)
- Reserva y ciclo de vida del viaje (booking → bid → accept → status → payment)
- Modo conductor: documentos, vehículo, ganancias, heat map, cash-out
- Reporte de problemas con chat Firebase
- Utilidades: Google Places/Routes, versión de app, listas país/moneda

### Panel admin (`/admin/*`)

- Dashboard y métricas
- Usuarios (clientes) y conductores (aprobar/rechazar documentos)
- Tipos de vehículo y servicios
- Viajes por estado, mapa “God’s view”, ganancias
- Configuración del sitio, app, push, email, idiomas
- Geo: áreas restringidas, radio de búsqueda, city admins
- SOS, FAQs, incidencias, referidos, cash-out manual
- Moneda mundial, plantillas, soporte

### Web público

- Homepage, legal, FAQ
- Flujo eliminación de cuenta (requisitos tiendas)
- Redirects y webhooks de pago Wompi
- Descarga de facturas PDF

## Stack tecnológico

- PHP **8.2**, Laravel **11.15**
- MySQL 8 (producción), SQLite en CI
- Nginx + PHP-FPM
- Integraciones: Firebase RTDB/FCM, Wompi, Twilio Verify, Google Maps, Socialite, mPDF, Excel

## Fuera de alcance (este repo)

- Código nativo Flutter (repositorio `AppZimo-Mobile`)
- Infraestructura como código (Terraform/CloudFormation) — pendiente de formalizar
- Colas en producción: `QUEUE_CONNECTION=database` sin workers activos en EC2 al momento de la auditoría

## Objetivos del agente de desarrollo

- Mantener documentación y CI actualizados
- Despliegue continuo controlado hacia EC2
- Revisión de PRs y versionado semver en GitHub
- Coordinación de contratos API con agente móvil
