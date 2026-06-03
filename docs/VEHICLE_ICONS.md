# Iconos de vehículos / servicios (mobile)

## Medios activos XISTI (por ahora)

Solo **Moto (id 3)** y **Carro (id 1)** en:

- Viajes (modo `transport`)
- Envíos y encomiendas (`delivery_vehicle_options`)
- Registro de conductor (`vehicle-service-list`)

**Motoratón (id 5)** está oculto en API y registro hasta nueva decisión de producto.

| vehicle_service_id | Etiqueta |
|--------------------|----------|
| 3 | Moto |
| 1 | Carro |

Constante: `DeliveryVehicleHelper::PASSENGER_ACTIVE_VEHICLE_SERVICE_IDS`

## Dónde se aplica

| Capa | Ubicación |
|------|-----------|
| API home | `filterHomeServiceRows()` + `deliveryOptionsForApi()` |
| Reservas | `UserClassApi` rechaza `service_id` 5 y transport ≠ 1,3 |
| Conductores | `DriverVehicleHelper::EXCLUDED_SERVICE_IDS` incluye 5 |
| Caché iconos | `ICON_CACHE_VERSION` (bump al cambiar assets) |

## Otros modos

| Servicio | Notas |
|----------|--------|
| Expreso | `enable_expreso_mobile` — ver `docs/EXPRESO.md` |
| Encomiendas | Mismos vehículos que envíos (Moto, Carro) |

Assets en `public/assets/images/vehicle-service/`.
