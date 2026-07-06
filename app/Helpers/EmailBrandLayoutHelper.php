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

        return [
            'admin_url' => (string) config('xisti.allowed_admin_host', 'admin.xistiapp.com'),
            'public_url' => (string) config('xisti.public_site_url', 'https://www.xistiapp.com'),
            'logo_url' => 'https://admin.xistiapp.com/assets/images/email-temp-images/mail-logo.png?v=3',
            'primary' => (string) ($brand['primary'] ?? '#39FF14'),
            'secondary' => (string) ($brand['secondary'] ?? '#9333EA'),
            'footer_bg' => (string) ($brand['background'] ?? '#0B0B0B'),
            'surface' => (string) ($brand['surface'] ?? '#141414'),
            'text' => '#E8E8E8',
            'body_text' => '#1F1F1F',
            'muted' => '#6B7280',
            'mail_site_name' => (string) config('xisti.mail.from_name', 'XISTI'),
            'tagline' => (string) config('xisti.tagline', 'Fácil y Seguro'),
        ];
    }

    public static function ctaButton(string $label, string $url, string $variant = 'primary'): string
    {
        $b = self::brandConfig();
        $bg = $variant === 'secondary' ? $b['secondary'] : $b['primary'];
        $text = $variant === 'secondary' ? '#FFFFFF' : '#0B0B0B';

        return <<<HTML
<table role="presentation" cellspacing="0" cellpadding="0" style="margin:24px auto 8px;">
  <tr><td align="center" style="border-radius:999px;background:{$bg};">
    <a href="{$url}" style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:700;color:{$text};text-decoration:none;letter-spacing:.02em;">{$label}</a>
  </td></tr>
</table>
HTML;
    }

    public static function promoBanner(string $code, string $discountText): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border-radius:12px;overflow:hidden;border:2px dashed {$b['secondary']};background:linear-gradient(135deg,#faf5ff 0%,#f0fdf4 100%);">
  <tr><td style="padding:20px 24px;text-align:center;">
    <p style="margin:0 0 6px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:{$b['secondary']};font-weight:700;">Promoción exclusiva</p>
    <p style="margin:0 0 10px;font-size:28px;font-weight:800;color:#111;letter-spacing:.18em;">{$code}</p>
    <p style="margin:0;font-size:15px;color:{$b['body_text']};">{$discountText}</p>
  </td></tr>
</table>
HTML;
    }

    public static function invoiceTable(): string
    {
        $b = self::brandConfig();

        return <<<HTML
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:20px 0;border:1px solid #E5E7EB;border-radius:12px;overflow:hidden;">
  <tr style="background:{$b['surface']};color:{$b['text']};">
    <td style="padding:12px 16px;font-size:13px;font-weight:700;">Concepto</td>
    <td align="right" style="padding:12px 16px;font-size:13px;font-weight:700;">Valor</td>
  </tr>
  <tr><td style="padding:12px 16px;border-top:1px solid #E5E7EB;">Tarifa del recorrido</td><td align="right" style="padding:12px 16px;border-top:1px solid #E5E7EB;">##ride_fare##</td></tr>
  <tr><td style="padding:12px 16px;border-top:1px solid #E5E7EB;">Descuento promoción</td><td align="right" style="padding:12px 16px;border-top:1px solid #E5E7EB;color:#16a34a;">- ##promo_discount##</td></tr>
  <tr><td style="padding:12px 16px;border-top:1px solid #E5E7EB;">Comisión plataforma</td><td align="right" style="padding:12px 16px;border-top:1px solid #E5E7EB;">##platform_fee##</td></tr>
  <tr style="background:#F9FAFB;">
    <td style="padding:14px 16px;font-size:16px;font-weight:800;">Total pagado</td>
    <td align="right" style="padding:14px 16px;font-size:16px;font-weight:800;color:{$b['secondary']};">##total_amount##</td>
  </tr>
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
        $logo = $b['logo_url'];
        $preheader = $preheader ?? $b['tagline'];
        $preheader = htmlspecialchars($preheader, ENT_QUOTES, 'UTF-8');

        return <<<HTML
<div style="margin:0;padding:0;background:#f4f4f6;font-family:'Segoe UI',Arial,Helvetica,sans-serif;">
  <span style="display:none!important;max-height:0;max-width:0;opacity:0;overflow:hidden;">{$preheader}</span>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:linear-gradient(180deg,#eef2ff 0%,#f4f4f6 40%);padding:32px 12px;">
    <tr><td align="center">
      <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(17,24,39,.12);">
        <tr><td style="padding:0;background:linear-gradient(135deg,{$footer} 0%,{$b['surface']} 55%,{$secondary} 140%);">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
            <tr><td style="height:5px;background:{$accent};font-size:0;line-height:0;">&nbsp;</td></tr>
            <tr><td style="padding:28px 24px 20px;text-align:center;">
              <img src="{$logo}" alt="{$b['mail_site_name']}" style="max-height:56px;width:auto;" />
              <p style="margin:14px 0 0;font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:{$accent};font-weight:700;">{$b['tagline']}</p>
            </td></tr>
          </table>
        </td></tr>
        <tr><td style="padding:28px 32px 12px;color:{$b['body_text']};font-size:15px;line-height:1.7;">
          <p style="margin:0 0 18px;font-size:22px;font-weight:800;color:#111;letter-spacing:-.02em;">{$greeting}</p>
          {$bodyHtml}
        </td></tr>
        <tr><td style="padding:8px 32px 28px;color:{$b['muted']};font-size:12px;line-height:1.6;">
          <p style="margin:0;">Este correo fue enviado por <strong style="color:#111;">{$b['mail_site_name']}</strong>. Si no solicitaste esta acción, ignóralo o escríbenos a <a href="mailto:##site_email##" style="color:{$secondary};text-decoration:none;">##site_email##</a>.</p>
        </td></tr>
        <tr><td style="padding:22px 32px;background:{$footer};color:{$b['text']};font-size:13px;line-height:1.55;">
          <p style="margin:0 0 8px;font-weight:800;color:{$accent};font-size:14px;">{$b['mail_site_name']}</p>
          <p style="margin:0 0 6px;"><a href="mailto:##site_email##" style="color:{$b['text']};text-decoration:none;">##site_email##</a></p>
          <p style="margin:0 0 6px;"><a href="##site_url##" style="color:{$b['text']};text-decoration:none;">##site_url##</a></p>
          <p style="margin:14px 0 0;font-size:11px;color:#9CA3AF;">Movilidad urbana · Pagos seguros · Soporte 24/7</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</div>
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

        return [
            [
                'type' => 'customer_signup',
                'title' => 'Bienvenida — Registro de usuario',
                'preheader' => 'Tu cuenta XISTI está lista. Solicita tu primer recorrido.',
                'greeting' => '¡Bienvenido, ##user_name##!',
                'body' => '<p>Tu registro en <strong>##mail_site_name##</strong> se completó correctamente. Ya puedes moverte por la ciudad con conductores verificados, pagos seguros y seguimiento en tiempo real.</p><ul style="margin:16px 0;padding-left:20px;"><li>Solicita recorridos en segundos</li><li>Paga en efectivo, tarjeta o billetera</li><li>Califica cada experiencia</li></ul>'.$ctaApp,
            ],
            [
                'type' => 'password_reset_request',
                'title' => 'Recuperación de contraseña',
                'preheader' => 'Restablece tu contraseña de XISTI de forma segura.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Recibimos una solicitud para restablecer la contraseña de tu cuenta XISTI. El enlace es válido por <strong>60 minutos</strong>.</p><p style="padding:14px 16px;background:#FEF3C7;border-radius:10px;color:#92400E;font-size:14px;">Si no solicitaste este cambio, ignora este correo. Tu contraseña actual seguirá activa.</p>'.$ctaReset.'<p style="font-size:13px;color:#6B7280;">Enlace alternativo:<br><a href="##reset_link##" style="color:#9333EA;word-break:break-all;">##reset_link##</a></p>',
            ],
            [
                'type' => 'ride_invoice_email',
                'title' => 'Factura de recorrido',
                'preheader' => 'Tu comprobante del recorrido ##ride_id## está listo.',
                'greeting' => 'Comprobante de pago',
                'body' => '<p>Hola <strong>##user_name##</strong>, aquí está el resumen de tu recorrido <strong>##ride_id##</strong> del <strong>##date_time##</strong>.</p><p><strong>Origen:</strong> ##pickup_location##<br><strong>Destino:</strong> ##destination_location##<br><strong>Conductor:</strong> ##driver_name##</p>'.$invoice.$ctaInvoice,
            ],
            [
                'type' => 'promo_code_offer',
                'title' => 'Promoción especial',
                'preheader' => 'Tienes un descuento exclusivo en tu próximo recorrido.',
                'greeting' => '¡Tenemos algo para ti, ##user_name##!',
                'body' => '<p>Aprovecha esta promoción por tiempo limitado en <strong>##mail_site_name##</strong>.</p>'.$promo.'<p>Válido hasta <strong>##promo_expiry##</strong>. Aplica en la app al solicitar tu recorrido.</p>'.$ctaPromo,
            ],
            [
                'type' => 'wallet_topup_receipt',
                'title' => 'Recarga de billetera',
                'preheader' => 'Confirmación de recarga en tu billetera XISTI.',
                'greeting' => 'Recarga confirmada',
                'body' => '<p>Hola <strong>##user_name##</strong>, tu billetera fue recargada exitosamente.</p><table role="presentation" width="100%" style="margin:16px 0;border:1px solid #E5E7EB;border-radius:12px;"><tr><td style="padding:14px 16px;">Monto recargado</td><td align="right" style="padding:14px 16px;font-weight:800;color:#9333EA;">##topup_amount##</td></tr><tr><td style="padding:14px 16px;border-top:1px solid #E5E7EB;">Saldo actual</td><td align="right" style="padding:14px 16px;font-weight:700;">##wallet_balance##</td></tr><tr><td style="padding:14px 16px;border-top:1px solid #E5E7EB;">Referencia</td><td align="right" style="padding:14px 16px;">##transaction_id##</td></tr></table>'.$ctaApp,
            ],
            [
                'type' => 'new_order_placed-transport',
                'title' => 'Recorrido solicitado',
                'preheader' => 'Estamos buscando un conductor para ti.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Recibimos tu solicitud en <strong>##mail_site_name##</strong>. Te avisaremos cuando un conductor acepte.</p><p><strong>Origen:</strong> ##pickup_location##<br><strong>Destino:</strong> ##destination_location##</p>'.$ctaApp,
            ],
            [
                'type' => 'your_request_rejected/canceled',
                'title' => 'Recorrido cancelado o rechazado',
                'preheader' => 'Tu recorrido fue cancelado. Puedes intentar de nuevo.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Lamentamos informarte que tu recorrido fue cancelado o rechazado por <strong>##driver_name##</strong>. No se realizó ningún cobro.</p>'.$ctaApp,
            ],
            [
                'type' => 'request_completed',
                'title' => 'Recorrido completado',
                'preheader' => 'Gracias por viajar con XISTI.',
                'greeting' => '¡Gracias por tu viaje, ##user_name##!',
                'body' => '<p>Tu recorrido <strong>##ride_id##</strong> finalizó correctamente. Cuéntanos cómo fue tu experiencia desde la app.</p>'.$ctaApp,
            ],
            [
                'type' => 'account_blocked_-_customer',
                'title' => 'Cuenta suspendida — Pasajero',
                'preheader' => 'Aviso importante sobre tu cuenta XISTI.',
                'greeting' => 'Hola ##user_name##,',
                'body' => '<p>Tu perfil de pasajero fue suspendido por actividad no permitida. Si crees que es un error, escríbenos a <strong>##site_email##</strong>.</p>',
            ],
            [
                'type' => 'driver_new_ride_request_–_transport',
                'title' => 'Nueva solicitud de recorrido — Conductor',
                'preheader' => 'Tienes una nueva solicitud de recorrido.',
                'greeting' => 'Hola ##driver_name##,',
                'body' => '<p>Nueva solicitud de <strong>##user_name##</strong>.</p><p><strong>Origen:</strong> ##pickup_location##<br><strong>Destino:</strong> ##destination_location##<br><strong>Hora:</strong> ##pickup_time_location##</p>'.$ctaApp,
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
                'body' => '<p>Tu perfil de conductor fue suspendido. Contacta soporte en <strong>##site_email##</strong> si necesitas ayuda.</p>',
            ],
            [
                'type' => 'admin_new_user_signup',
                'title' => 'Nuevo usuario registrado',
                'preheader' => 'Un nuevo usuario se registró en XISTI.',
                'greeting' => 'Hola administrador,',
                'body' => '<p>El usuario <strong>##user_name##</strong> se registró en ##mail_site_name##. Revisa su perfil en el panel de administración.</p>',
            ],
            [
                'type' => 'admin_new__ride_request_-_transport',
                'title' => 'Nuevo recorrido — Alerta admin',
                'preheader' => 'Nuevo recorrido en la plataforma.',
                'greeting' => 'Hola administrador,',
                'body' => '<p>El conductor <strong>##driver_name##</strong> recibió una solicitud de <strong>##user_name##</strong>.</p><p><strong>Origen:</strong> ##pickup_location##<br><strong>Destino:</strong> ##destination_location##<br><strong>Hora:</strong> ##pickup_time_location##</p>',
            ],
            [
                'type' => 'driver_account_approve',
                'title' => 'Cuenta de conductor aprobada',
                'preheader' => '¡Ya puedes conducir con XISTI!',
                'greeting' => '¡Felicitaciones, ##driver_name##!',
                'body' => '<p>Tu cuenta de conductor en <strong>##mail_site_name##</strong> fue aprobada. Ya puedes recibir solicitudes y generar ingresos.</p>'.$ctaApp,
            ],
            [
                'type' => 'new_issue_reported_-_to_issue_creator',
                'title' => 'Reporte recibido',
                'preheader' => 'Recibimos tu reporte en XISTI.',
                'greeting' => 'Hola ##created_by##,',
                'body' => '<p>Recibimos tu reporte.</p><p><strong>Ticket:</strong> ##ticket_id##<br><strong>Fecha:</strong> ##created_on##<br><strong>Categoría:</strong> ##issue_category##<br><strong>Descripción:</strong> ##issue_description##</p>',
            ],
            [
                'type' => 'new_issue_reported_-_to_admin',
                'title' => 'Nuevo reporte — Admin',
                'preheader' => 'Nuevo ticket de soporte en XISTI.',
                'greeting' => 'Hola administrador,',
                'body' => '<p>Nuevo reporte de <strong>##creator_type##</strong> ##created_by##.</p><p><strong>Ticket:</strong> ##ticket_id##<br><strong>Fecha:</strong> ##created_on##<br><strong>Categoría:</strong> ##issue_category##</p><p><a href="##link##" style="color:#9333EA;">Ver en el panel</a></p>',
            ],
            [
                'type' => 'reported_issue_resolved_-_to_issue_creator',
                'title' => 'Reporte resuelto',
                'preheader' => 'Tu ticket fue resuelto.',
                'greeting' => 'Hola ##created_by##,',
                'body' => '<p>Tu reporte <strong>##ticket_id##</strong> fue resuelto el <strong>##resolved_on##</strong>. Gracias por ayudarnos a mejorar ##mail_site_name##.</p>',
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
