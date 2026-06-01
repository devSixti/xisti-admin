# 08 — Plan de remediación de seguridad

> **Estado:** Documentado para implementación futura.  
> **No implementar** sin aprobación explícita del responsable de seguridad / producto.

Auditoría basada en repositorio `Appzimo-Admin` y instancia EC2 (mayo 2026).

## Resumen ejecutivo

| Severidad | Cantidad aprox. | Temas |
|-----------|-----------------|-------|
| Crítica | 3 | Secretos en repo, credenciales Firebase |
| Alta | 5 | APP_ENV, phpMyAdmin, auth API, OAuth hardcoded |
| Media | 4 | Colas, headers, rutas duplicadas, SG loader |
| Baja | 3 | Hardening SSH, dependencias, tests |

---

## CRÍTICO — C1: Cuenta de servicio Firebase en repositorio

**Hallazgo:** `config/firebase-cloud-messaging.php` contiene `private_key` completo y metadatos de service account.

**Riesgo:** Acceso a FCM, RTDB y recursos del proyecto GCP `app-zimo`.

**Remediación planificada:**

1. Revocar/rotar clave en Google Cloud Console **inmediatamente** al implementar.
2. Mover credenciales a variables de entorno o AWS Secrets Manager.
3. Referenciar `env('FIREBASE_CREDENTIALS_PATH')` o JSON base64 en `.env` (no en git).
4. Añadir `config/firebase-cloud-messaging.php` a `.gitignore` y usar `.example`.
5. Escaneo historial git (`git filter-repo` o BFG) si el repo fue público.

**Esfuerzo:** 4–8 h | **Prioridad:** P0

---

## CRÍTICO — C2: OAuth Facebook/Google en código

**Hallazgo:** `config/services.php` incluye `client_id` y `client_secret` en texto plano (Facebook y Google).

**Riesgo:** Suplantación OAuth, acceso a datos de login social.

**Remediación:**

1. Rotar secretos en Meta Developer y Google Cloud.
2. Usar solo `env('FACEBOOK_CLIENT_SECRET')`, etc.
3. Validar redirect URIs estrictos en consolas OAuth.

**Esfuerzo:** 2–4 h | **Prioridad:** P0

---

## CRÍTICO — C3: Divergencia de repositorios (supply chain)

**Hallazgo:** EC2 despliega desde `whitelabelfox-pvt-ltd/app-zimo-fox-drive-v2-clone`; desarrollo en `DESZIMO/Appzimo-Admin`. `composer.lock` distinto.

**Riesgo:** Parches de seguridad solo en un lado; deploy inconsistente.

**Remediación:**

1. Declarar un único repo canónico (DESZIMO).
2. Reconfigurar `origin` en EC2 y proceso de deploy.
3. Diff completo antes de primer sync.

**Esfuerzo:** 1 día | **Prioridad:** P0

---

## ALTO — A1: `APP_ENV=local` en producción EC2

**Hallazgo:** `.env` en servidor con `APP_ENV=local`, `APP_DEBUG=false`.

**Riesgo:** Comportamientos de desarrollo, fuga de información en errores si debug se activa por error.

**Remediación:** `APP_ENV=production`, `APP_DEBUG=false`, revisar `LOG_LEVEL=warning`.

---

## ALTO — A2: phpMyAdmin expuesto en Nginx

**Hallazgo:** Ruta `/data-vault-appzimo` en vhost SSL hacia `phpmyadmin-storage`.

**Riesgo:** Acceso a BD si credenciales débiles o vulnerabilidades phpMyAdmin.

**Remediación:**

1. Restringir por IP/VPN o eliminar de producción.
2. Usar túnel SSH para administración DB.
3. RDS con acceso privado en arquitectura objetivo.

---

## ALTO — A3: Autenticación API por token plano

**Hallazgo:** `user_id` + `access_token` en body sin expiración estándar OAuth2; Sanctum instalado pero no usado.

**Riesgo:** Token replay, sin rotación ni scopes.

**Remediación (fase 2):**

1. Migrar a Sanctum o JWT con expiración.
2. HTTPS obligatorio (ya en admin.appzimo.com).
3. Rate limiting global API.

---

## ALTO — A4: Controlador API change-password ausente (pre-fix)

**Hallazgo:** Ruta referenciaba `Api\Auth\ResetPasswordController` inexistente en EC2 y repo antiguo.

**Estado repo canónico:** Corregido con implementación nueva (pendiente deploy EC2).

**Remediación deploy:** Incluir en próximo release a producción.

---

## ALTO — A5: SSH puerto no estándar sin hardening documentado

**Hallazgo:** Puerto 927, clave PEM; múltiples claves en agente local del operador.

**Remediación:**

1. `IdentitiesOnly yes` (ya documentado).
2. Security Group: solo IPs oficina/VPN.
3. Deshabilitar password auth; usar `AllowUsers ubuntu appzimodevop`.
4. Considerar AWS SSM Session Manager.

---

## MEDIO — M1: Colas sin workers

**Hallazgo:** `QUEUE_CONNECTION=database` sin `queue:work` en systemd.

**Riesgo:** Jobs de notificación/email no procesados.

**Remediación:** Supervisor + `php artisan queue:work` o Horizon.

---

## MEDIO — M2: SourceGuardian en `AuthAlertClass`

**Hallazgo:** Binario ofuscado; dependencia de loader `ixed.8.2.lin`.

**Riesgo:** Caja negra en validación de app; dificulta auditoría y CI local.

**Remediación:** Negociar fuente o documentar contrato de licencia WhiteLabelFox.

---

## MEDIO — M3: Ruta duplicada `cancel-ride`

**Hallazgo:** Dos `Route::post('/cancel-ride', ...)` en `api.php`.

**Remediación:** Eliminar duplicado y test de regresión.

---

## MEDIO — M4: Permisos archivo `.pem` y `.env`

**Hallazgo:** Clave local estuvo en `644`; `.env` en servidor legible por grupo app.

**Remediación:** `chmod 600` en claves y `.env`; usuario mínimo para PHP-FPM.

---

## BAJO — B1: Dependencias

**Acción:** `composer audit` periódico en CI (paso futuro).

---

## BAJO — B2: Headers seguridad Nginx

**Acción:** Añadir HSTS, `X-Frame-Options`, `CSP` gradual.

---

## BAJO — B3: Cobertura de tests

**Acción:** Ampliar PHPUnit en endpoints críticos.

---

## Cronograma sugerido (implementación futura)

| Fase | Semana | Items |
|------|--------|-------|
| 1 | 1 | C1, C2 rotación secretos + env |
| 2 | 2 | C3 alineación EC2, A1, A4 deploy |
| 3 | 3 | A2 phpMyAdmin, A5 SSH/SG |
| 4 | 4+ | A3 auth, M1 colas, B1–B3 |

---

## Checklist pre-release (para el agente)

- [ ] Sin secretos nuevos en diff
- [ ] `composer audit` limpio o riesgos aceptados documentados
- [ ] Migraciones revisadas
- [ ] CI verde en GitHub
- [ ] Changelog de seguridad si hay C/A fixes
