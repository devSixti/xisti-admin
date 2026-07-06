# Resend — XISTI transactional mail

All outbound app emails (signup, rides, admin alerts, issues, etc.) are sent **from**:

| Field | Value |
|-------|--------|
| From address | `noreply@xistiapp.com` |
| From name | `XISTI` |
| Provider | [Resend](https://resend.com) |

Inbound support / admin copies still go to `soporte@xistiapp.com` via `general_settings.send_receive_email`.

## Production `.env`

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=noreply@xistiapp.com
MAIL_FROM_NAME=XISTI
RESEND_API_KEY=re_xxxxxxxx
```

Use `MAIL_MAILER=failover` to try Resend first, then SMTP from `general_settings`.

## DNS (Resend dashboard)

Verify domain `xistiapp.com` and add SPF, DKIM, and DMARC records Resend provides.

## Code path

1. `NotificationClass::sendMail()` queues `AutoMail`
2. `TransactionalMailHelper` detects Resend and does **not** require Gmail SMTP fields in DB
3. `AutoMail` sets `From: noreply@xistiapp.com` via `MAIL_FROM_ADDRESS`

## Smoke test

```bash
php artisan config:clear
# Trigger welcome email on signup or use admin email template test
```

Confirm delivery in Resend → Emails with sender `noreply@xistiapp.com`.

## ZIMO reference

See `Appzimo-Admin/docs/RESEND-STAGING.md` (`noreply@zimoapp.com`).
