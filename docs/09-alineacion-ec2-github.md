# 09 — Alineación EC2 ↔ GitHub

## Estado detectado (auditoría 2026-05-20)

| Aspecto | GitHub (`DESZIMO/Appzimo-Admin`) | EC2 producción |
|---------|----------------------------------|----------------|
| Remote git | `DESZIMO/Appzimo-Admin` | `whitelabelfox-pvt-ltd/app-zimo-fox-drive-v2-clone` |
| Rama | `main` | `master` (config remota) |
| `composer.lock` MD5 | `7adfccef6aa19f36bced17549caf809c` | `92834c90e05f452f6e77dbc37e6e9508` |
| `artisan` MD5 | `6e7aba8d5aef2be9a74dc65a7bc0aa3d` | **Igual** |
| Laravel | 11.15.0 | 11.15.0 |
| Carpeta `zimo/` duplicada | Eliminada en canónico | No existe en EC2 |
| CI GitHub Actions | Sí (`backend.yml`) | N/A |
| `Api\Auth\ResetPasswordController` | **Añadido** en canónico | **Ausente** |
| Documentación `docs/` | Completa | No desplegada (solo código PHP) |

**Conclusión:** El código aplicación es la misma base (mismo `artisan`), pero **dependencias y parches divergen**. GitHub está **adelantado** en limpieza, CI y fix de controlador.

## Qué está alineado

- Ruta servidor `/var/www/app-zimo-fox-drive-v2-clone` ≡ estructura Laravel raíz del repo
- Nginx → `public/` correcto
- PHP 8.2, MySQL, mismos módulos funcionales
- Versión framework idéntica

## Qué NO está alineado

1. **Origen git** distinto (riesgo de forks desincronizados).
2. **composer.lock** diferente → paquetes/vendor pueden variar.
3. **Parches locales** solo en GitHub (ResetPassword, docs, pipeline).
4. **`.env` producción** no gestionado por repo (correcto), pero `APP_ENV=local` incorrecto.

## Plan de sincronización recomendado

### Fase 1 — Inventario (completado)

- [x] Auditoría EC2 vía SSH
- [x] Auditoría repo local
- [x] Comparación checksums

### Fase 2 — Unificar remoto EC2 (pendiente operación)

En EC2 como `appzimodevop`:

```bash
cd /var/www/app-zimo-fox-drive-v2-clone
git remote set-url origin git@github.com:DESZIMO/Appzimo-Admin.git
git fetch origin
git checkout -B main origin/main   # tras backup branch master
composer install --no-dev --optimize-autoloader
php artisan migrate --force
```

**Antes:** backup BD + tarball `/var/www/app-zimo-fox-drive-v2-clone`.

### Fase 3 — Validación

- Smoke tests admin + API `app-version-check`
- Comparar `composer.lock` MD5 local vs servidor
- Revisar `storage/logs` 24h

### Fase 4 — Proceso ongoing

- Todo merge a `main` → tag → deploy EC2
- Dejar de pushear a `whitelabelfox` fork salvo espejo automático

## Archivos solo en GitHub (no afectan runtime)

- `docs/**`
- `AGENTS.md`
- `.github/workflows/backend.yml`
- `pint.json`

## Archivos eliminados del canónico (limpieza)

- `zimo/**` — duplicado completo (~25MB)
- DOCX pipeline movidos a `docs/archive/`

## Registro de cambios del agente (esta sesión)

| Cambio | Impacto EC2 tras deploy |
|--------|-------------------------|
| Eliminar `zimo/` | Ninguno (no existía en EC2) |
| Añadir `ResetPasswordController` API | Fix endpoint change-password |
| Pipeline CI mejorado | Solo GitHub |
| Documentación | Ninguno en runtime |
