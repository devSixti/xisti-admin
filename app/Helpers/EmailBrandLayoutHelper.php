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
            'admin_base_url' => rtrim((string) config('xisti.allowed_admin_host', 'admin.xistiapp.com'), '/'),
            'admin_url' => 'https://admin.xistiapp.com',
            'logo_url' => 'https://admin.xistiapp.com/assets/images/email-temp-images/mail-logo.png?v=2',
            'primary' => (string) ($brand['primary'] ?? '#39FF14'),
            'secondary' => (string) ($brand['secondary'] ?? '#9333EA'),
            'footer_bg' => (string) ($brand['background'] ?? '#0B0B0B'),
            'text' => '#E8E8E8',
            'body_text' => '#2A2A2A',
            'mail_site_name' => 'XISTI App',
            'tagline' => (string) config('xisti.tagline', 'Fácil y Seguro'),
        ];
    }

    public static function wrap(string $greeting, string $bodyHtml): string
    {
        $b = self::brandConfig();
        $greeting = trim($greeting);
        $accent = $b['primary'];
        $footer = $b['footer_bg'];
        $logo = $b['logo_url'];

        return <<<HTML
<div style="margin:0;padding:0;background:#f4f4f6;font-family:Arial,Helvetica,sans-serif;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f4f6;padding:24px 12px;">
    <tr><td align="center">
      <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
        <tr><td style="height:6px;background:{$accent};font-size:0;line-height:0;">&nbsp;</td></tr>
        <tr><td style="padding:28px 24px 8px;text-align:center;background:#ffffff;">
          <img src="{$logo}" alt="{$b['mail_site_name']}" style="max-height:64px;width:auto;" />
          <p style="margin:12px 0 0;font-size:12px;letter-spacing:.12em;text-transform:uppercase;color:#666;">{$b['tagline']}</p>
        </td></tr>
        <tr><td style="padding:8px 32px 24px;color:{$b['body_text']};font-size:15px;line-height:1.65;">
          <p style="margin:0 0 16px;font-size:20px;font-weight:700;color:#111;">{$greeting}</p>
          {$bodyHtml}
        </td></tr>
        <tr><td style="padding:20px 32px;background:{$footer};color:{$b['text']};font-size:13px;line-height:1.5;">
          <p style="margin:0 0 8px;font-weight:700;color:{$accent};">{$b['mail_site_name']}</p>
          <p style="margin:0 0 6px;"><a href="mailto:##site_email##" style="color:{$b['text']};text-decoration:none;">##site_email##</a></p>
          <p style="margin:0;"><a href="##site_url##" style="color:{$b['text']};text-decoration:none;">##site_url##</a></p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</div>
HTML;
    }

    /**
     * @return array<int, array{type: string, title: string, greeting: string, body: string}>
     */
    public static function templateCatalog(): array
    {
        return [
            ['type' => 'customer_signup', 'title' => 'Bienvenida — Registro de usuario', 'greeting' => 'Hola ##user_name##,', 'body' => '<p>¡Bienvenido a <strong>##mail_site_name##</strong>! Tu registro se completó correctamente. Ya puedes solicitar recorridos seguros en la ciudad.</p>'],
            ['type' => 'new_order_placed-transport', 'title' => 'Recorrido solicitado', 'greeting' => 'Hola ##user_name##,', 'body' => '<p>Recibimos tu solicitud de recorrido en <strong>##mail_site_name##</strong>. Puedes seguir al conductor en tiempo real desde la app.</p>'],
            ['type' => 'your_request_rejected/canceled', 'title' => 'Recorrido cancelado o rechazado', 'greeting' => 'Hola ##user_name##,', 'body' => '<p>Lamentamos informarte que tu recorrido fue cancelado o rechazado por <strong>##driver_name##</strong>. Puedes intentar de nuevo en unos minutos.</p>'],
            ['type' => 'request_completed', 'title' => 'Recorrido completado', 'greeting' => 'Hola ##user_name##,', 'body' => '<p>Tu recorrido <strong>##ride_id##</strong> fue completado con éxito. ¡Gracias por usar ##mail_site_name##!</p>'],
            ['type' => 'account_blocked_-_customer', 'title' => 'Cuenta suspendida — Pasajero', 'greeting' => 'Hola ##user_name##,', 'body' => '<p>Tu perfil de pasajero fue suspendido por actividad no permitida. Si crees que es un error, escríbenos a <strong>##site_email##</strong>.</p>'],
            ['type' => 'driver_new_ride_request_–_transport', 'title' => 'Nueva solicitud de recorrido — Conductor', 'greeting' => 'Hola ##driver_name##,', 'body' => '<p>Tienes una nueva solicitud de <strong>##user_name##</strong>.</p><p><strong>Origen:</strong> ##pickup_location##<br><strong>Destino:</strong> ##destination_location##<br><strong>Hora:</strong> ##pickup_time_location##</p>'],
            ['type' => 'driver_ride_completed_-_transport', 'title' => 'Recorrido completado — Conductor', 'greeting' => 'Hola ##driver_name##,', 'body' => '<p>El recorrido <strong>##ride_id##</strong> se completó el <strong>##date_time##</strong>. El valor se reflejará según la liquidación de tu billetera.</p>'],
            ['type' => 'driver_account_block', 'title' => 'Cuenta suspendida — Conductor', 'greeting' => 'Hola ##driver_name##,', 'body' => '<p>Tu perfil de conductor fue suspendido por actividad no permitida. Contacta soporte en <strong>##site_email##</strong> si necesitas ayuda.</p>'],
            ['type' => 'admin_new_user_signup', 'title' => 'Nuevo usuario registrado', 'greeting' => 'Hola administrador,', 'body' => '<p>El usuario <strong>##user_name##</strong> se registró en ##mail_site_name##. Revisa su perfil en el panel de administración.</p>'],
            ['type' => 'admin_new__ride_request_-_transport', 'title' => 'Nuevo recorrido — Alerta admin', 'greeting' => 'Hola administrador,', 'body' => '<p>El conductor <strong>##driver_name##</strong> recibió una solicitud de <strong>##user_name##</strong>.</p><p><strong>Origen:</strong> ##pickup_location##<br><strong>Destino:</strong> ##destination_location##<br><strong>Hora:</strong> ##pickup_time_location##</p>'],
            ['type' => 'driver_account_approve', 'title' => 'Cuenta de conductor aprobada', 'greeting' => 'Hola ##driver_name##,', 'body' => '<p>¡Felicitaciones! Tu cuenta de conductor en <strong>##mail_site_name##</strong> fue aprobada. Ya puedes recibir solicitudes y generar ingresos.</p>'],
            ['type' => 'new_issue_reported_-_to_issue_creator', 'title' => 'Reporte recibido', 'greeting' => 'Hola ##created_by##,', 'body' => '<p>Recibimos tu reporte en ##mail_site_name##.</p><p><strong>Ticket:</strong> ##ticket_id##<br><strong>Fecha:</strong> ##created_on##<br><strong>Categoría:</strong> ##issue_category##<br><strong>Descripción:</strong> ##issue_description##</p><p>Nuestro equipo lo revisará pronto.</p>'],
            ['type' => 'new_issue_reported_-_to_admin', 'title' => 'Nuevo reporte — Admin', 'greeting' => 'Hola administrador,', 'body' => '<p>Nuevo reporte de <strong>##creator_type##</strong> ##created_by##.</p><p><strong>Ticket:</strong> ##ticket_id##<br><strong>Fecha:</strong> ##created_on##<br><strong>Categoría:</strong> ##issue_category##<br><strong>Descripción:</strong> ##issue_description##</p><p><a href="##link##">Ver en el panel</a></p>'],
            ['type' => 'reported_issue_resolved_-_to_issue_creator', 'title' => 'Reporte resuelto', 'greeting' => 'Hola ##created_by##,', 'body' => '<p>Tu reporte <strong>##ticket_id##</strong> fue resuelto el <strong>##resolved_on##</strong>.</p><p><strong>Categoría:</strong> ##issue_category##<br><strong>Descripción:</strong> ##issue_description##</p><p>Gracias por ayudarnos a mejorar ##mail_site_name##.</p>'],
        ];
    }
}
