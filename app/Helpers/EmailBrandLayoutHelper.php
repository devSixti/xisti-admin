<?php

namespace App\Helpers;

class EmailBrandLayoutHelper
{
    /**
     * @return array<string, string>
     */
    public static function brandConfig(): array
    {
        $brand = config('xisti.brand', []);
        $baseUrl = rtrim((string) config('app.url', 'https://admin.xistiapp.com'), '/');
        if ($baseUrl === '' || str_contains($baseUrl, 'localhost')) {
            $baseUrl = 'https://admin.xistiapp.com';
        }
        $assetVersion = '7';
        $defaultLogo = $baseUrl.'/assets/images/email/xisti-email-logo.png?v='.$assetVersion;
        $configuredLogo = config('xisti.mail.logo_url');
        $logoUrl = is_string($configuredLogo) && trim($configuredLogo) !== ''
            ? trim($configuredLogo)
            : $defaultLogo;

        return [
            'admin_url' => (string) config('xisti.allowed_admin_host', 'admin.xistiapp.com'),
            'public_url' => (string) config('xisti.public_site_url', 'https://www.xistiapp.com'),
            'logo_url' => $logoUrl,
            'pattern_url' => $baseUrl.'/assets/images/email/xisti-email-header-pattern.png?v='.$assetVersion,
            'primary' => (string) ($brand['primary'] ?? '#80FF00'),
            'primary_dark' => '#5CE600',
            'secondary' => (string) ($brand['secondary'] ?? '#681FFF'),
            'footer_bg' => (string) ($brand['background'] ?? '#0B0B0B'),
            'surface' => (string) ($brand['surface'] ?? '#141414'),
            'surface_elevated' => '#1C1C1F',
            'text' => '#F4F4F5',
            'body_text' => '#1A1A1E',
            'body_muted' => '#52525B',
            'muted' => '#A1A1AA',
            'border' => '#E4E4E7',
            'card_bg' => '#FFFFFF',
            'canvas' => '#09090B',
            'mail_site_name' => (string) config('xisti.mail.from_name', 'XISTI'),
            'tagline' => (string) config('xisti.tagline', 'Fácil y Seguro'),
        ];
    }

    public static function ctaButton(string $label, string $url, string $variant = 'primary'): string
    {
        $b = self::brandConfig();
        if ($variant === 'secondary') {
            $bg = $b['secondary'];
            $text = '#FFFFFF';
            $shadow = '0 10px 28px rgba(104,31,255,.35)';
        } else {
            $bg = $b['primary'];
            $text = '#0B0B0B';
            $shadow = '0 10px 28px rgba(128,255,0,.28)';
        }

        return <<<HTML
<table role="presentation" cellspacing="0" cellpadding="0" style="margin:28px auto 10px;">
  <tr>
    <td align="center" style="border-radius:999px;background:{$bg};box-shadow:{$shadow};">
      <a href="{$url}" style="display:inline-block;padding:15px 34px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:15px;font-weight:800;color:{$text};text-decoration:none;letter-spacing:.04em;text-transform:uppercase;">{$label}</a>
    </td>
  </tr>
</table>
HTML;
    }

    public static function divider(): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:26px 0;">
  <tr>
    <td style="height:1px;background:linear-gradient(90deg,transparent 0%,{$b['border']} 18%,{$b['primary']} 50%,{$b['secondary']} 82%,transparent 100%);font-size:0;line-height:0;">&nbsp;</td>
  </tr>
</table>
HTML;
    }

    public static function featureList(string $itemsHtml): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid {$b['border']};border-radius:16px;overflow:hidden;background:#FAFAFA;">
  <tr>
    <td style="padding:6px 0;">
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        {$itemsHtml}
      </table>
    </td>
  </tr>
</table>
HTML;
    }

    public static function featureItem(string $text): string
    {
        $b = self::brandConfig();

        return <<<HTML
<tr>
  <td style="padding:12px 20px;border-bottom:1px solid {$b['border']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.55;color:{$b['body_text']};">
    <span style="display:inline-block;width:10px;height:10px;border-radius:999px;background:{$b['primary']};margin-right:12px;vertical-align:middle;"></span>
    {$text}
  </td>
</tr>
HTML;
    }

    public static function alertBox(string $text, string $tone = 'warning'): string
    {
        $palette = [
            'warning' => ['bg' => '#FFFBEB', 'border' => '#FCD34D', 'text' => '#92400E'],
            'info' => ['bg' => '#F5F3FF', 'border' => '#C4B5FD', 'text' => '#5B21B6'],
            'success' => ['bg' => '#F0FDF4', 'border' => '#86EFAC', 'text' => '#166534'],
        ];
        $colors = $palette[$tone] ?? $palette['warning'];

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-radius:14px;border:1px solid {$colors['border']};background:{$colors['bg']};">
  <tr>
    <td style="padding:16px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.65;color:{$colors['text']};">
      {$text}
    </td>
  </tr>
</table>
HTML;
    }

    public static function routeCard(): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid {$b['border']};border-radius:16px;overflow:hidden;">
  <tr>
    <td style="padding:18px 20px;background:{$b['footer_bg']};color:{$b['text']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:.16em;text-transform:uppercase;font-weight:700;">
      Detalle del recorrido
    </td>
  </tr>
  <tr>
    <td style="padding:18px 20px;background:#FAFAFA;">
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td width="28" valign="top" style="padding-top:4px;">
            <span style="display:inline-block;width:12px;height:12px;border-radius:999px;background:{$b['primary']};box-shadow:0 0 0 4px rgba(128,255,0,.18);"></span>
          </td>
          <td style="padding-bottom:16px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.55;color:{$b['body_text']};">
            <span style="display:block;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:{$b['body_muted']};margin-bottom:4px;">Origen</span>
            <strong>##pickup_location##</strong>
          </td>
        </tr>
        <tr>
          <td width="28" valign="top" style="padding-top:4px;">
            <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:{$b['secondary']};"></span>
          </td>
          <td style="font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;line-height:1.55;color:{$b['body_text']};">
            <span style="display:block;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:{$b['body_muted']};margin-bottom:4px;">Destino</span>
            <strong>##destination_location##</strong>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML;
    }

    public static function promoBanner(string $code, string $discountText): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:24px 0;border-radius:18px;overflow:hidden;border:1px solid rgba(104,31,255,.25);">
  <tr>
    <td style="padding:0;background:linear-gradient(135deg,{$b['footer_bg']} 0%,#1A1030 55%,{$b['surface']} 100%);">
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td style="height:4px;background:linear-gradient(90deg,{$b['primary']} 0%,{$b['secondary']} 100%);font-size:0;line-height:0;">&nbsp;</td>
        </tr>
        <tr>
          <td style="padding:26px 24px 24px;text-align:center;">
            <p style="margin:0 0 8px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:.2em;text-transform:uppercase;color:{$b['primary']};font-weight:800;">Promoción exclusiva</p>
            <p style="margin:0 0 10px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:34px;font-weight:900;color:#FFFFFF;letter-spacing:.22em;">{$code}</p>
            <p style="margin:0;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:15px;line-height:1.6;color:{$b['muted']};">{$discountText}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML;
    }

    public static function invoiceTable(): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid {$b['border']};border-radius:16px;overflow:hidden;">
  <tr style="background:{$b['footer_bg']};color:{$b['text']};">
    <td style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Concepto</td>
    <td align="right" style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">Valor</td>
  </tr>
  <tr>
    <td style="padding:14px 18px;border-top:1px solid {$b['border']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};">Tarifa del recorrido</td>
    <td align="right" style="padding:14px 18px;border-top:1px solid {$b['border']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};">##ride_fare##</td>
  </tr>
  <tr>
    <td style="padding:14px 18px;border-top:1px solid {$b['border']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};">Descuento promoción</td>
    <td align="right" style="padding:14px 18px;border-top:1px solid {$b['border']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:#16A34A;font-weight:700;">- ##promo_discount##</td>
  </tr>
  <tr>
    <td style="padding:14px 18px;border-top:1px solid {$b['border']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};">Comisión plataforma</td>
    <td align="right" style="padding:14px 18px;border-top:1px solid {$b['border']};font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};">##platform_fee##</td>
  </tr>
  <tr style="background:linear-gradient(90deg,#F4F4F5 0%,#FAFAFA 100%);">
    <td style="padding:16px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:16px;font-weight:900;color:{$b['body_text']};">Total pagado</td>
    <td align="right" style="padding:16px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:18px;font-weight:900;color:{$b['secondary']};">##total_amount##</td>
  </tr>
</table>
HTML;
    }

    public static function walletTable(): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:22px 0;border:1px solid {$b['border']};border-radius:16px;overflow:hidden;">
  <tr>
    <td style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};border-bottom:1px solid {$b['border']};">Monto recargado</td>
    <td align="right" style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:16px;font-weight:900;color:{$b['secondary']};border-bottom:1px solid {$b['border']};">##topup_amount##</td>
  </tr>
  <tr>
    <td style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};border-bottom:1px solid {$b['border']};">Saldo actual</td>
    <td align="right" style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:15px;font-weight:800;color:{$b['body_text']};border-bottom:1px solid {$b['border']};">##wallet_balance##</td>
  </tr>
  <tr>
    <td style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_muted']};">Referencia</td>
    <td align="right" style="padding:14px 18px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:14px;color:{$b['body_text']};">##transaction_id##</td>
  </tr>
</table>
HTML;
    }

    public static function brandHeader(): string
    {
        $b = self::brandConfig();
        $logo = htmlspecialchars($b['logo_url'], ENT_QUOTES, 'UTF-8');
        $siteName = htmlspecialchars($b['mail_site_name'], ENT_QUOTES, 'UTF-8');
        $tagline = htmlspecialchars($b['tagline'], ENT_QUOTES, 'UTF-8');
        $accent = $b['primary'];
        $secondary = $b['secondary'];
        $footer = $b['footer_bg'];
        $pattern = htmlspecialchars($b['pattern_url'], ENT_QUOTES, 'UTF-8');
        $publicUrl = htmlspecialchars($b['public_url'], ENT_QUOTES, 'UTF-8');

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td style="height:6px;background:linear-gradient(90deg,{$accent} 0%,{$secondary} 55%,{$accent} 100%);font-size:0;line-height:0;">&nbsp;</td>
  </tr>
  <tr>
    <td background="{$pattern}" style="background-color:{$footer};background-image:url('{$pattern}');background-repeat:no-repeat;background-position:center bottom;background-size:cover;">
      <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td style="padding:30px 24px 26px;text-align:center;">
            <table role="presentation" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto 16px;background:#FFFFFF;border-radius:18px;box-shadow:0 18px 40px rgba(0,0,0,.35);">
              <tr>
                <td style="padding:18px 28px 14px;text-align:center;">
                  <a href="{$publicUrl}" style="text-decoration:none;">
                    <img src="{$logo}" width="260" height="auto" alt="{$siteName}" style="display:block;margin:0 auto;max-width:260px;width:100%;height:auto;border:0;" />
                  </a>
                </td>
              </tr>
            </table>
            <p style="margin:0 0 4px;font-family:Arial Black,Arial,Helvetica,sans-serif;font-size:34px;line-height:1;font-weight:900;letter-spacing:-.05em;color:{$accent};">xisti<span style="color:#FFFFFF;">!</span></p>
            <p style="margin:0 0 14px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:#FFFFFF;font-weight:700;">{$tagline}</p>
            <table role="presentation" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <td style="width:42px;height:3px;background:{$accent};border-radius:999px;font-size:0;line-height:0;">&nbsp;</td>
                <td style="width:8px;font-size:0;line-height:0;">&nbsp;</td>
                <td style="width:42px;height:3px;background:{$secondary};border-radius:999px;font-size:0;line-height:0;">&nbsp;</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML;
    }

    public static function metaChip(string $label, string $value): string
    {
        $b = self::brandConfig();

        return <<<HTML
<td style="padding:8px 10px;">
  <table role="presentation" cellspacing="0" cellpadding="0" style="border:1px solid {$b['border']};border-radius:12px;background:#FAFAFA;min-width:120px;">
    <tr>
      <td style="padding:10px 14px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;">
        <span style="display:block;font-size:10px;letter-spacing:.14em;text-transform:uppercase;color:{$b['body_muted']};margin-bottom:4px;">{$label}</span>
        <strong style="font-size:14px;color:{$b['body_text']};">{$value}</strong>
      </td>
    </tr>
  </table>
</td>
HTML;
    }

    public static function metaChipRow(string $chipsHtml): string
    {
        return <<<HTML
<table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 20px;">
  <tr>{$chipsHtml}</tr>
</table>
HTML;
    }

    public static function wrap(string $greeting, string $bodyHtml, ?string $preheader = null): string
    {
        $b = self::brandConfig();
        $greeting = trim($greeting);
        $accent = $b['primary'];
        $secondary = $b['secondary'];
        $footer = $b['footer_bg'];
        $header = self::brandHeader();
        $preheader = $preheader ?? $b['tagline'];
        $preheader = htmlspecialchars($preheader, ENT_QUOTES, 'UTF-8');
        $publicUrl = $b['public_url'];
        $siteName = $b['mail_site_name'];

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <title>{$siteName}</title>
  <!--[if mso]><style type="text/css">body,table,td{font-family:Arial,Helvetica,sans-serif!important;}</style><![endif]-->
</head>
<body style="margin:0;padding:0;background:{$b['canvas']};">
  <div style="display:none!important;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">{$preheader}</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:{$b['canvas']};">
    <tr>
      <td align="center" style="padding:36px 14px;">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;border-collapse:separate;">
          <tr>
            <td style="border-radius:24px;overflow:hidden;box-shadow:0 28px 70px rgba(0,0,0,.5);background:{$b['card_bg']};border:1px solid rgba(255,255,255,.06);">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="padding:0;background:{$footer};">
                    {$header}
                  </td>
                </tr>
                <tr>
                  <td style="padding:36px 36px 12px;background:{$b['card_bg']};">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                      <tr>
                        <td style="font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:{$b['body_text']};">
                          <p style="margin:0 0 10px;font-size:30px;line-height:1.15;font-weight:900;letter-spacing:-.03em;color:#111111;">{$greeting}</p>
                          <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 24px;">
                            <tr>
                              <td style="width:72px;height:4px;background:linear-gradient(90deg,{$accent} 0%,{$secondary} 100%);border-radius:999px;font-size:0;line-height:0;">&nbsp;</td>
                            </tr>
                          </table>
                          <div style="font-size:16px;line-height:1.8;color:{$b['body_text']};">
                            {$bodyHtml}
                          </div>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:10px 36px 28px;background:{$b['card_bg']};">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-top:1px solid {$b['border']};">
                      <tr>
                        <td style="padding-top:20px;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:12px;line-height:1.75;color:{$b['body_muted']};">
                          Este correo fue enviado por <strong style="color:{$b['body_text']};">{$siteName}</strong>.
                          Si no solicitaste esta acción, ignóralo o escríbenos a
                          <a href="mailto:##site_email##" style="color:{$secondary};text-decoration:none;font-weight:700;">##site_email##</a>.
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td style="padding:28px 36px 32px;background:{$footer};">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                      <tr>
                        <td style="font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:{$b['text']};">
                          <p style="margin:0 0 12px;font-size:16px;font-weight:900;color:{$accent};letter-spacing:.08em;text-transform:uppercase;">{$siteName}</p>
                          <p style="margin:0 0 8px;font-size:13px;line-height:1.65;">
                            <a href="mailto:##site_email##" style="color:{$b['text']};text-decoration:none;">##site_email##</a>
                          </p>
                          <p style="margin:0 0 16px;font-size:13px;line-height:1.65;">
                            <a href="##site_url##" style="color:{$b['text']};text-decoration:none;">##site_url##</a>
                            <span style="color:#52525B;"> · </span>
                            <a href="{$publicUrl}" style="color:{$accent};text-decoration:none;font-weight:800;">Descargar app</a>
                          </p>
                          <table role="presentation" cellspacing="0" cellpadding="0">
                            <tr>
                              <td style="padding-right:12px;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:{$b['muted']};">Movilidad urbana</td>
                              <td style="width:5px;height:5px;border-radius:999px;background:{$accent};font-size:0;line-height:0;">&nbsp;</td>
                              <td style="padding:0 12px;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:{$b['muted']};">Pagos seguros</td>
                              <td style="width:5px;height:5px;border-radius:999px;background:{$secondary};font-size:0;line-height:0;">&nbsp;</td>
                              <td style="padding-left:12px;font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:{$b['muted']};">Soporte 24/7</td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:20px 8px 0;text-align:center;font-family:'Segoe UI',Arial,Helvetica,sans-serif;font-size:11px;line-height:1.65;color:#71717A;">
              © {$siteName} · Movilidad con conductores verificados
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }

    /**
     * @return array<int, array{type: string, title: string, greeting: string, body: string, preheader?: string}>
     */
    public static function templateCatalog(): array
    {
        $ctaApp = self::ctaButton('Abrir XISTI', 'https://www.xistiapp.com');
        $ctaInvoice = self::ctaButton('Descargar factura PDF', '##invoice_download_link##');
        $ctaReset = self::ctaButton('Restablecer contraseña', '##reset_link##');
        $ctaPromo = self::ctaButton('Usar promoción', 'https://www.xistiapp.com');
        $invoice = self::invoiceTable();
        $promo = self::promoBanner('##promo_code##', '##promo_description##');
        $route = self::routeCard();
        $wallet = self::walletTable();
        $features = self::featureList(
            self::featureItem('Solicita recorridos en segundos')
            .self::featureItem('Paga en efectivo, tarjeta o billetera')
            .self::featureItem('Califica cada experiencia desde la app')
        );

        return [
            [
                'type' => 'customer_signup',
                'title' => 'Bienvenida — Registro de usuario',
                'preheader' => 'Tu cuenta XISTI está lista. Solicita tu primer recorrido.',
                'greeting' => '¡Bienvenido, ##user_name##!',
                'body' => '<p>Tu registro en <strong>##mail_site_name##</strong> se completó correctamente. Ya puedes moverte por la ciudad con conductores verificados, pagos seguros y seguimiento en tiempo real.</p>'.$features.$ctaApp,
            ],
            [
                'type' => 'password_reset_request',
                'title' => 'Recuperación de contraseña',
                'preheader' => 'Restablece tu contraseña de XISTI de forma segura.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Recibimos una solicitud para restablecer la contraseña de tu cuenta XISTI.</p>'
                    .self::metaChipRow(
                        self::metaChip('Validez', '60 minutos').self::metaChip('Seguridad', 'Enlace único')
                    )
                    .self::alertBox('Si no solicitaste este cambio, ignora este correo. Tu contraseña actual seguirá activa.', 'warning')
                    .$ctaReset
                    .'<p style="font-size:13px;color:#71717A;margin-top:18px;">Enlace alternativo:<br><a href="##reset_link##" style="color:#681FFF;word-break:break-all;text-decoration:none;font-weight:700;">##reset_link##</a></p>',
            ],
            [
                'type' => 'ride_invoice_email',
                'title' => 'Factura de recorrido',
                'preheader' => 'Tu comprobante del recorrido ##ride_id## está listo.',
                'greeting' => 'Comprobante de pago',
                'body' => '<p>Hola <strong>##user_name##</strong>, aquí está el resumen de tu recorrido.</p>'
                    .self::metaChipRow(
                        self::metaChip('Recorrido', '##ride_id##').self::metaChip('Fecha', '##date_time##')
                    )
                    .$route
                    .self::metaChipRow(
                        self::metaChip('Conductor', '##driver_name##').self::metaChip('Total', '##total_amount##')
                    )
                    .$invoice.$ctaInvoice,
            ],
            [
                'type' => 'promo_code_offer',
                'title' => 'Promoción especial',
                'preheader' => 'Tienes un descuento exclusivo en tu próximo recorrido.',
                'greeting' => '¡Tenemos algo para ti, ##user_name##!',
                'body' => '<p>Aprovecha esta promoción por tiempo limitado en <strong>##mail_site_name##</strong>.</p>'.$promo.'<p>Válido hasta <strong>##promo_expiry##</strong>. Aplica el código en la app al solicitar tu recorrido.</p>'.$ctaPromo,
            ],
            [
                'type' => 'wallet_topup_receipt',
                'title' => 'Recarga de billetera',
                'preheader' => 'Confirmación de recarga en tu billetera XISTI.',
                'greeting' => 'Recarga confirmada',
                'body' => '<p>Hola <strong>##user_name##</strong>, tu billetera fue recargada exitosamente y ya puedes usar el saldo en tu próximo recorrido.</p>'.$wallet.$ctaApp,
            ],
            [
                'type' => 'new_order_placed-transport',
                'title' => 'Recorrido solicitado',
                'preheader' => 'Estamos buscando un conductor para ti.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Recibimos tu solicitud en <strong>##mail_site_name##</strong>. Te avisaremos en cuanto un conductor acepte el recorrido.</p>'.$route.self::alertBox('Puedes seguir el estado del viaje en tiempo real desde la app.', 'info').$ctaApp,
            ],
            [
                'type' => 'your_request_rejected/canceled',
                'title' => 'Recorrido cancelado o rechazado',
                'preheader' => 'Tu recorrido fue cancelado. Puedes intentar de nuevo.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Lamentamos informarte que tu recorrido fue cancelado o rechazado por <strong>##driver_name##</strong>. No se realizó ningún cobro.</p>'
                    .self::alertBox('Puedes solicitar un nuevo recorrido cuando quieras. Estamos listos para ayudarte.', 'info')
                    .$ctaApp,
            ],
            [
                'type' => 'request_completed',
                'title' => 'Recorrido completado',
                'preheader' => 'Gracias por viajar con XISTI.',
                'greeting' => '¡Gracias por tu viaje, ##user_name##!',
                'body' => '<p>Tu recorrido <strong>##ride_id##</strong> finalizó correctamente. Cuéntanos cómo fue tu experiencia — tu opinión nos ayuda a mejorar cada día.</p>'.$ctaApp,
            ],
            [
                'type' => 'account_blocked_-_customer',
                'title' => 'Cuenta suspendida — Pasajero',
                'preheader' => 'Aviso importante sobre tu cuenta XISTI.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Tu perfil de pasajero fue suspendido por actividad no permitida en la plataforma.</p>'
                    .self::alertBox('Si crees que es un error, escríbenos a <strong>##site_email##</strong> y nuestro equipo revisará tu caso.', 'warning'),
            ],
            [
                'type' => 'driver_new_ride_request_–_transport',
                'title' => 'Nueva solicitud de recorrido — Conductor',
                'preheader' => 'Tienes una nueva solicitud de recorrido.',
                'greeting' => 'Hola ##driver_name##,',
                'body' => '<p>Nueva solicitud de <strong>##user_name##</strong> lista para aceptar.</p>'.$route.'<p><strong>Hora estimada:</strong> ##pickup_time_location##</p>'.$ctaApp,
            ],
            [
                'type' => 'driver_ride_completed_-_transport',
                'title' => 'Recorrido completado — Conductor',
                'preheader' => 'Tu recorrido fue marcado como completado.',
                'greeting' => 'Hola ##driver_name##,',
                'body' => '<p>El recorrido <strong>##ride_id##</strong> se completó el <strong>##date_time##</strong>. El valor se reflejará según la liquidación de tu billetera.</p>',
            ],
            [
                'type' => 'driver_account_block',
                'title' => 'Cuenta suspendida — Conductor',
                'preheader' => 'Aviso sobre tu cuenta de conductor.',
                'greeting' => 'Hola ##driver_name##,',
                'body' => '<p>Tu perfil de conductor fue suspendido temporalmente. Contacta soporte en <strong>##site_email##</strong> si necesitas ayuda.</p>',
            ],
            [
                'type' => 'admin_new_user_signup',
                'title' => 'Nuevo usuario registrado',
                'preheader' => 'Un nuevo usuario se registró en XISTI.',
                'greeting' => 'Hola administrador,',
                'body' => '<p>El usuario <strong>##user_name##</strong> se registró en ##mail_site_name##. Revisa su perfil en el panel de administración cuando quieras.</p>',
            ],
            [
                'type' => 'admin_new__ride_request_-_transport',
                'title' => 'Nuevo recorrido — Alerta admin',
                'preheader' => 'Nuevo recorrido en la plataforma.',
                'greeting' => 'Hola administrador,',
                'body' => '<p>El conductor <strong>##driver_name##</strong> recibió una solicitud de <strong>##user_name##</strong>.</p>'.$route.'<p><strong>Hora:</strong> ##pickup_time_location##</p>',
            ],
            [
                'type' => 'driver_account_approve',
                'title' => 'Cuenta de conductor aprobada',
                'preheader' => '¡Ya puedes conducir con XISTI!',
                'greeting' => '¡Felicitaciones, ##driver_name##!',
                'body' => '<p>Tu cuenta de conductor en <strong>##mail_site_name##</strong> fue aprobada. Ya puedes recibir solicitudes, generar ingresos y construir tu reputación en la plataforma.</p>'.$ctaApp,
            ],
            [
                'type' => 'new_issue_reported_-_to_issue_creator',
                'title' => 'Reporte recibido',
                'preheader' => 'Recibimos tu reporte en XISTI.',
                'greeting' => 'Hola ##created_by##,',
                'body' => '<p>Recibimos tu reporte y nuestro equipo lo está revisando.</p>'
                    .self::featureList(
                        self::featureItem('<strong>Ticket:</strong> ##ticket_id##')
                        .self::featureItem('<strong>Fecha:</strong> ##created_on##')
                        .self::featureItem('<strong>Categoría:</strong> ##issue_category##')
                        .self::featureItem('<strong>Descripción:</strong> ##issue_description##')
                    ),
            ],
            [
                'type' => 'new_issue_reported_-_to_admin',
                'title' => 'Nuevo reporte — Admin',
                'preheader' => 'Nuevo ticket de soporte en XISTI.',
                'greeting' => 'Hola administrador,',
                'body' => '<p>Nuevo reporte de <strong>##creator_type##</strong> ##created_by##.</p>'
                    .self::featureList(
                        self::featureItem('<strong>Ticket:</strong> ##ticket_id##')
                        .self::featureItem('<strong>Fecha:</strong> ##created_on##')
                        .self::featureItem('<strong>Categoría:</strong> ##issue_category##')
                    )
                    .'<p style="margin-top:18px;"><a href="##link##" style="color:#681FFF;font-weight:800;text-decoration:none;">Ver ticket en el panel →</a></p>',
            ],
            [
                'type' => 'reported_issue_resolved_-_to_issue_creator',
                'title' => 'Reporte resuelto',
                'preheader' => 'Tu ticket fue resuelto.',
                'greeting' => 'Hola ##created_by##,',
                'body' => '<p>Tu reporte <strong>##ticket_id##</strong> fue resuelto el <strong>##resolved_on##</strong>. Gracias por ayudarnos a mejorar ##mail_site_name##.</p>'
                    .self::alertBox('Si el problema persiste, responde a este correo o abre un nuevo ticket desde la app.', 'success'),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function sampleMergeData(): array
    {
        return [
            '##user_name##' => 'Jerónimo',
            '##driver_name##' => 'Carlos M.',
            '##created_by##' => 'Jerónimo',
            '##creator_type##' => 'pasajero',
            '##ride_id##' => 'XST-20481',
            '##pickup_location##' => 'Centro Comercial Santafé, Medellín',
            '##destination_location##' => 'El Poblado, Cra 43A',
            '##pickup_time_location##' => '6 jul 2026 · 2:30 PM',
            '##date_time##' => '6 jul 2026 · 3:05 PM',
            '##ticket_id##' => 'TKT-90821',
            '##created_on##' => '6 jul 2026',
            '##resolved_on##' => '6 jul 2026',
            '##issue_category##' => 'Pago / billetera',
            '##issue_description##' => 'Consulta sobre recarga no reflejada.',
            '##link##' => 'https://admin.xistiapp.com',
            '##reset_link##' => 'https://admin.xistiapp.com/reset-password/sample-token',
            '##invoice_download_link##' => 'https://admin.xistiapp.com/ride-invoice/sample',
            '##ride_fare##' => '$ 24.500',
            '##promo_discount##' => '$ 4.000',
            '##platform_fee##' => '$ 1.960',
            '##total_amount##' => '$ 22.460',
            '##promo_code##' => 'XISTI20',
            '##promo_description##' => '20% de descuento en tu próximo recorrido',
            '##promo_expiry##' => '31 jul 2026',
            '##topup_amount##' => '$ 50.000',
            '##wallet_balance##' => '$ 73.200',
            '##transaction_id##' => 'WMP-8849201',
            '##mail_site_name##' => 'XISTI',
            '##site_email##' => 'soporte@xistiapp.com',
            '##site_url##' => 'https://www.xistiapp.com',
        ];
    }
}
