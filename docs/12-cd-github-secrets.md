# 12 — CD GitHub Actions: secretos y clave SSH

## Error `git@github.com: Permission denied`

El CD **no debe hacer `git pull` en EC2**. El workflow actual sube un `.tar.gz` desde GitHub Actions y hace `rsync` al servidor (sin clave de deploy en GitHub).

Si ves ese error, el run usó un workflow antiguo. Re-ejecuta el workflow en la rama `main` más reciente.

## Secretos del repositorio (Settings → Secrets → Actions)

| Secret | Valor correcto |
|--------|----------------|
| `EC2_HOST` | `ec2-18-208-4-15.compute-1.amazonaws.com` |
| `EC2_SSH_PORT` | `927` |
| `EC2_SSH_USER` | `ubuntu` |
| `EC2_SSH_PRIVATE_KEY` | **Contenido completo** del archivo local `~/.ssh/app-zimo-root-key.pem` (no `app-zimo.pem`) |
| `EC2_DEPLOY_USER` | `appzimodevop` |
| `EC2_DEPLOY_PATH` | `/var/www/app-zimo-fox-drive-v2-clone` (referencia; el script usa la ruta fija del workflow) |

### Cómo copiar la clave PEM al secret

En tu máquina:

```bash
cat ~/.ssh/app-zimo-root-key.pem
```

Pega **todo** el bloque (incluye `-----BEGIN ... KEY-----` y `-----END ... KEY-----`) en el secret `EC2_SSH_PRIVATE_KEY`. Sin espacios extra al inicio/final.

## Probar SSH local (misma clave que GitHub)

```bash
ssh -i ~/.ssh/app-zimo-root-key.pem -p 927 ubuntu@ec2-18-208-4-15.compute-1.amazonaws.com
```

Si esto falla, el secret en GitHub también fallará.

## Deploy key (opcional, solo si vuelves a usar `git pull` en EC2)

Clave pública en el servidor (`appzimodevop`):

```bash
ssh zimo-ec2 'sudo -u appzimodevop cat ~/.ssh/deszimo_deploy.pub'
```

Añadir en GitHub → **DESZIMO/Appzimo-Admin** → Settings → Deploy keys → Read-only.

Host SSH en el servidor: `github.com-deszimo` (ver `~/.ssh/config` de `appzimodevop`).
