# Integración apps móviles (XISTI)

App Flutter: https://github.com/devSixti/xisti-mobile

## API

```
https://admin.xistiapp.com/api/customer/
```

Package Dart: `app_xisti`  
Android/iOS: `com.app.xisti`

## Configuración inicial

La app llama `app-version-check` al arrancar y cachea:

- Flags de login social
- Lista de monedas e idiomas
- `app_key` para header Authorization
- Servicios habilitados (encomiendas sí, expreso no)

## Firebase

Proyecto: `xisti-app-ad901`  
Topics FCM: `XistiUser`, `XistiDriver`

## OAuth redirects

Coordinar URLs de retorno en Google/Facebook consoles con dominio `admin.xistiapp.com`.
