# 05 — Manual del administrador (panel web)

## Acceso

| Campo | Valor |
|-------|--------|
| URL | https://admin.appzimo.com/admin/login |
| Credenciales | Usuarios en tabla `super_admin` (gestionados por IT; no documentar passwords aquí) |

Tras login exitoso → redirección a **Dashboard** (`/admin/dashboard`).

## Roles y permisos

El menú lateral se construye dinámicamente según:

- `admin_module` — módulos del sistema
- `admin_permission` / `admin_category_permission` — qué puede ver/hacer cada admin
- Middleware `adminrole` bloquea rutas no autorizadas

**City admin:** restricción geográfica vía `admin_area_list` y `area_id` en registros.

## Módulos del panel (guía operativa)

### Dashboard (`/admin/dashboard`)

Resumen operativo: viajes, usuarios, métricas agregadas (según configuración del despliegue).

### Clientes (`/admin/customer-list`)

- Listar, crear, editar, bloquear usuarios
- Ver historial de viajes y transacciones de billetera
- Ajustar saldo manual de wallet
- Reseñas de usuarios: aprobar/eliminar

### Conductores / proveedores (Transport)

| Ruta típica | Función |
|-------------|---------|
| `/admin/transport-provider-list` | Listado conductores |
| Documentos | Aprobar/rechazar licencias, SOAT, etc. |
| `/admin/vehicle-type-list` | Tipos de vehículo |
| Servicios de vehículo | Tarifas, courier, descripción |
| Viajes por estado | Pendiente, activo, completado, cancelado |
| `/admin/transport-earings-reports` | Reportes de ganancias |
| Cash-out | Solicitudes de retiro de conductores |
| God's view | Mapa en tiempo real de conductores/viajes |
| Heat map | Demanda geográfica |

### Configuración

| Sección | Descripción |
|---------|-------------|
| Site setting | Comisiones, moneda, flags de funciones (hail ride, toll, etc.) |
| App version | Versión mínima iOS/Android, force update |
| Push notification | Campañas masivas FCM |
| Email templates | Plantillas transaccionales |
| Language | Constantes i18n para apps |
| Support pages | CMS páginas legales/informativas |
| World currency | Monedas soportadas |

### Geografía

- **Restricted areas** — zonas donde no hay servicio
- **City areas** — polígonos de ciudad
- **Search radius** — distancia búsqueda conductores
- **City admins** — administradores locales

### Seguridad y soporte

- **SOS** — contactos de emergencia por configuración
- **Report issue** — tickets de usuarios, chat admin vía Firebase
- **FAQs** — preguntas frecuentes in-app
- **Referral** — programa de referidos

### Herramientas

- **Test mail** — envío de prueba SMTP
- **Change password** — cambio contraseña admin propio
- **Order-wise chat** — historial chat por `order_id` / viaje

## Flujos operativos frecuentes

### Aprobar un conductor nuevo

1. Transport → lista conductores no verificados
2. Revisar documentos subidos (`provider_documents`)
3. Aprobar cada documento requerido
4. Cambiar estado conductor a activo

### Gestionar un viaje problemático

1. Buscar viaje por ID en listados de rides
2. Ver detalle (ruta, precio, estados)
3. Si hay incidencia → módulo Report issue
4. Opcional: chat Firebase desde panel

### Configurar comisión o tarifa

1. Site setting / Service settings
2. Vehicle services → editar tarifas base, tiempo, oferta máxima
3. Guardar; apps leen en próximo `home` o booking

### Enviar notificación masiva

1. Push notification → crear mensaje
2. Segmentación según implementación en formulario
3. Envío vía FCM

## Cerrar sesión

`/admin/logout/{admin_id}`

## Páginas públicas relacionadas

Los enlaces legales que ven los usuarios en la app suelen servirse desde:

- `/terms-and-conditions`
- `/privacy-policy`
- `/faq`

Editables desde Support pages en admin.

## Solución de problemas admin

| Síntoma | Acción |
|---------|--------|
| 419 / sesión expirada | Volver a login; middleware `revalidate` |
| Menú incompleto | Revisar permisos del admin en BD |
| Mapa no carga | API key Google en `general_settings` |
| Chat vacío | Credenciales Firebase y reglas RTDB |

## Capturas y capacitación

Se recomienda añadir capturas en `docs/assets/` en futuras iteraciones del manual.
