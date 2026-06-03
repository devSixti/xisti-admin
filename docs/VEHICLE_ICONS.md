# Iconos de vehículos / servicios (mobile)

## Dónde viven

| Capa | Ubicación |
|------|-----------|
| **Admin (CDN del API)** | `public/assets/images/vehicle-service/{icon_name}` |
| **Base de datos** | Tabla `vehicle_services`, columna `icon_name` |
| **API home** | `ServiceCatalogHelper::homeServiceSelect()` → campo `service_icon` (URL completa) |
| **Envíos / Encomiendas** | `DeliveryVehicleHelper::deliveryOptionsForApi()` → `delivery_vehicle_options` |
| **Admin UI** | Transport → Vehicle Services → subir icono en el formulario |

## Envíos y encomiendas (pasajero)

Solo **tres** medios vía API (sin motocarro ni bicicleta):

| vehicle_service_id | Etiqueta | delivery_variant |
|--------------------|----------|------------------|
| 3 | Moto | — |
| 1 | Carro | — |
| 5 | Motoratón | `motoraton` |

Icono id 5: usar PNG de Motoratón en BD (`27531520260705.png` o subido en admin). `DeliveryVehicleHelper::ICON_CACHE_VERSION` invalida caché en apps.

## Otros modos

| Servicio | Archivo sugerido | Notas |
|----------|------------------|--------|
| Motoratón (viajes, id 5) | `27531520260705.png` (u otro subido en admin) | **No** `motocarro.png` en `icon_name` de BD |
| Expreso | `expreso_bus.png` | Modo Expreso (`enable_expreso_mobile`) |
| Encomiendas | *(vacío en BD)* | Misma lista que envíos (`delivery_vehicle_options`) |

Los PNG de referencia están en `public/assets/images/vehicle-service/`.

## Flags admin (mobile)

Site Settings → **Mobile v1.0.2**:

- `enable_expreso_mobile` — modo Expreso (ver `docs/EXPRESO.md`)
- `enable_encomiendas_mobile` — Encomiendas
- `require_courier_package_dimensions_mobile` — peso/medidas en envío

Con flags desmarcados, el API oculta esos modos.
