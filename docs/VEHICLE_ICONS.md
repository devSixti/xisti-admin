# Iconos de vehículos / servicios (mobile)

## Dónde viven

| Capa | Ubicación |
|------|-----------|
| **Admin (CDN del API)** | `public/assets/images/vehicle-service/{icon_name}` |
| **Base de datos** | Tabla `vehicle_services`, columna `icon_name` |
| **API home** | `ServiceCatalogHelper::homeServiceSelect()` → campo `service_icon` (URL completa) |
| **Admin UI** | Transport → Vehicle Services → subir icono en el formulario |

## Servicios 1.0.2 (iconos nuevos)

Cuando actives Expreso / Encomiendas en admin, asigna en `vehicle_services.icon_name`:

| Servicio | Archivo sugerido | Notas |
|----------|------------------|--------|
| Motoratón (viajes, id 5) | `27531520260705.png` (u otro subido en admin) | Icono de producción; **no** `motoraton.png` generado ni `motocarro.png` en BD |
| Motocarro (envíos / encomiendas 1.0.2, id 5) | `motocarro.png` | Solo vía `DeliveryVehicleHelper`; `delivery_variant=motocarro` |
| Bicicleta (envío extra) | `bicycle.png` | Variante `delivery_variant=bicycle`; misma lista en Encomiendas |
| Expreso | `expreso_bus.png` | Modo Expreso |
| Encomiendas | *(vacío en BD)* | La app usa los mismos vehículos que envíos (`delivery_vehicle_options`) |

Los PNG de referencia para diseño están en:

`public/assets/images/vehicle-service/` (repo admin)

Tras subir, limpia caché en app si hace falta (`?v=0.4` ya va en la URL del API).

## Flags admin (v1.0.2 opcional)

Site Settings → **Mobile v1.0.2**:

- `enable_expreso_mobile` — muestra modo Expreso en apps 1.0.2+
- `enable_encomiendas_mobile` — muestra Encomiendas
- `require_courier_package_dimensions_mobile` — obliga peso/medidas en envío

Con todo **desmarcado** (default), el API oculta esos modos y la app 1.0.1 sigue estable.
