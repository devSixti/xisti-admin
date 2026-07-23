<?php

namespace App\Helpers;

use App\Models\PushEventTemplate;
use Illuminate\Support\Facades\Cache;

class PushEventTemplateHelper
{
    public static function clearCache(): void
    {
        $keys = PushEventTemplate::query()->pluck('event_key');
        foreach ($keys as $key) {
            Cache::forget('push_event_template:'.$key);
        }
    }

    /**
     * @param  array<string, string|int|float>  $vars
     * @return array{title: string, message: string, title_code: int, message_code: int, notification_type: int, sound: string}
     */
    public static function resolve(string $eventKey, string $lang = 'es', array $vars = []): array
    {
        $template = Cache::remember(
            'push_event_template:'.$eventKey,
            300,
            static fn () => PushEventTemplate::query()
                ->where('event_key', $eventKey)
                ->where('is_active', true)
                ->first()
        );

        if ($template === null) {
            return [
                'title' => '',
                'message' => '',
                'title_code' => 91,
                'message_code' => 0,
                'notification_type' => 1,
                'sound' => 'default',
            ];
        }

        $useEn = $lang !== 'es' && filled($template->title_en) && filled($template->message_en);
        $title = $useEn ? (string) $template->title_en : (string) $template->title_es;
        $message = $useEn ? (string) $template->message_en : (string) $template->message_es;

        foreach ($vars as $name => $value) {
            $replacement = (string) $value;
            $title = str_replace('{'.$name.'}', $replacement, $title);
            $message = str_replace('{'.$name.'}', $replacement, $message);
        }

        return [
            'title' => $title,
            'message' => $message,
            'title_code' => (int) $template->title_code,
            'message_code' => (int) $template->message_code,
            'notification_type' => (int) $template->app_notification_type,
            'sound' => $template->sound_profile === 'new_request' ? 'new_request.wav' : 'default',
        ];
    }

    /**
     * Catálogo mínimo acordado: notificaciones automáticas pasajero y conductor.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function defaultCatalog(): array
    {
        return [
            // —— Cliente (pasajero) ——
            [
                'event_key' => 'passenger_ride_accepted',
                'label' => '👤 Conductor asignado',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 16,
                'title_es' => 'Viaje',
                'message_es' => 'Tu conductor aceptó el viaje y va en camino por ti.',
                'title_en' => 'Ride',
                'message_en' => 'Your driver accepted the ride and is on the way.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 100,
            ],
            [
                'event_key' => 'passenger_ride_scheduled_accepted',
                'label' => '👤 Viaje programado confirmado',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 45,
                'title_es' => 'Viaje programado',
                'message_es' => 'Un conductor aceptó tu servicio programado.',
                'title_en' => 'Scheduled ride',
                'message_en' => 'A driver accepted your scheduled service.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 101,
            ],
            [
                'event_key' => 'passenger_driver_at_pickup',
                'label' => '👤 Conductor en el punto de origen',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 46,
                'title_es' => 'Viaje',
                'message_es' => 'Tu conductor ha llegado al lugar de recogida.',
                'title_en' => 'Ride',
                'message_en' => 'Your driver has arrived at the pickup location.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 102,
            ],
            [
                'event_key' => 'passenger_ride_started',
                'label' => '👤 Viaje iniciado',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 19,
                'title_es' => 'Viaje',
                'message_es' => 'Tu viaje / entrega ha comenzado.',
                'title_en' => 'Ride',
                'message_en' => 'Your trip / delivery has started.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 103,
            ],
            [
                'event_key' => 'passenger_ride_waypoint',
                'label' => '👤 Llegada a parada intermedia',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 272,
                'title_es' => 'Viaje',
                'message_es' => 'El conductor llegó a la parada/parada intermedia de la ruta.',
                'title_en' => 'Ride',
                'message_en' => 'The driver reached an intermediate stop on the route.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 104,
            ],
            [
                'event_key' => 'passenger_ride_at_destination',
                'label' => '👤 Llegada al destino final',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 48,
                'title_es' => 'Viaje',
                'message_es' => 'El conductor ha llegado al destino.',
                'title_en' => 'Ride',
                'message_en' => 'The driver has arrived at the destination.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 105,
            ],
            [
                'event_key' => 'passenger_ride_payment_pending',
                'label' => '👤 Pago pendiente',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 21,
                'title_es' => 'Pago pendiente',
                'message_es' => 'Tu viaje está listo. Completa el pago para finalizar.',
                'title_en' => 'Payment pending',
                'message_en' => 'Your ride is ready. Complete payment to finish.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 106,
            ],
            [
                'event_key' => 'passenger_ride_completed',
                'label' => '👤 Viaje finalizado con éxito',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 150,
                'message_code' => 21,
                'title_es' => 'Viaje completado',
                'message_es' => 'Tu viaje o entrega se ha completado correctamente.',
                'title_en' => 'Ride completed',
                'message_en' => 'Your trip or delivery was completed successfully.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 106,
            ],
            [
                'event_key' => 'passenger_ride_cancelled',
                'label' => '👤 Viaje cancelado',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 65,
                'message_code' => 20,
                'title_es' => 'Viaje cancelado',
                'message_es' => 'Tu viaje ha sido cancelado por el conductor o por el administrador.',
                'title_en' => 'Ride cancelled',
                'message_en' => 'Your ride was cancelled by the driver or administrator.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 107,
            ],
            [
                'event_key' => 'passenger_driver_bid',
                'label' => '👤 Nueva oferta de tarifa (subasta)',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 329,
                'title_es' => 'Subasta',
                'message_es' => 'Un conductor te ha propuesto una tarifa para tu envío/viaje.',
                'title_en' => 'Bid',
                'message_en' => 'A driver proposed a fare for your trip/delivery.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 108,
            ],
            [
                'event_key' => 'passenger_wallet_update',
                'label' => '👤 Movimiento en la billetera',
                'audience' => 'passenger',
                'category' => 'wallet',
                'app_notification_type' => 6,
                'title_code' => 262,
                'message_code' => 263,
                'title_es' => 'Billetera',
                'message_es' => 'Has recibido o enviado saldo en tu billetera digital.',
                'title_en' => 'Wallet',
                'message_en' => 'You have received or sent balance in your digital wallet.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 109,
            ],
            // —— Conductor ——
            [
                'event_key' => 'driver_new_request',
                'label' => '🚗 Nueva oferta disponible (radar)',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 7,
                'title_code' => 91,
                'message_code' => 90,
                'title_es' => 'Nueva solicitud',
                'message_es' => 'Tienes una solicitud de servicio cercana disponible.',
                'title_en' => 'New request',
                'message_en' => 'You have a nearby service request available.',
                'sound_profile' => 'new_request',
                'placeholders' => '{currency},{price},{pickup},{destination}',
                'sort_order' => 200,
            ],
            [
                'event_key' => 'driver_passenger_cancelled',
                'label' => '🚗 Viaje cancelado por el cliente',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 65,
                'message_code' => 35,
                'title_es' => 'Viaje cancelado',
                'message_es' => 'El usuario canceló el servicio que tenías asignado.',
                'title_en' => 'Ride cancelled',
                'message_en' => 'The passenger cancelled the service assigned to you.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 201,
            ],
            [
                'event_key' => 'driver_ride_completed',
                'label' => '🚗 Viaje completado',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 150,
                'message_code' => 21,
                'title_es' => 'Viaje completado',
                'message_es' => 'El viaje ha finalizado exitosamente.',
                'title_en' => 'Ride completed',
                'message_en' => 'The ride finished successfully.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 202,
            ],
            [
                'event_key' => 'driver_fare_changed_by_user',
                'label' => '🚗 Cambio de tarifa por el usuario',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 330,
                'title_es' => 'Subasta',
                'message_es' => 'El cliente modificó el precio de la oferta en la subasta.',
                'title_en' => 'Bid',
                'message_en' => 'The passenger changed the bid price in the auction.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 203,
            ],
            [
                'event_key' => 'driver_bid_rejected',
                'label' => '🚗 Oferta rechazada',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 14,
                'title_code' => 91,
                'message_code' => 376,
                'title_es' => 'Subasta',
                'message_es' => 'El cliente rechazó la tarifa que le propusiste.',
                'title_en' => 'Bid',
                'message_en' => 'The passenger rejected the fare you proposed.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 204,
            ],
            [
                'event_key' => 'driver_bid_accepted',
                'label' => '🚗 Cliente aceptó tu oferta',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 150,
                'message_code' => 21,
                'title_es' => 'Viaje confirmado',
                'message_es' => 'El cliente aceptó tu tarifa. Puedes iniciar el servicio.',
                'title_en' => 'Ride confirmed',
                'message_en' => 'The passenger accepted your fare. You can start the service.',
                'sound_profile' => 'new_request',
                'placeholders' => '',
                'sort_order' => 205,
            ],
            [
                'event_key' => 'driver_scheduled_ride_accepted',
                'label' => '🚗 Viaje programado confirmado',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 150,
                'message_code' => 21,
                'title_es' => 'Viaje programado confirmado',
                'message_es' => 'El cliente confirmó tu viaje programado.',
                'title_en' => 'Scheduled ride confirmed',
                'message_en' => 'The passenger confirmed your scheduled ride.',
                'sound_profile' => 'new_request',
                'placeholders' => '',
                'sort_order' => 206,
            ],
            [
                'event_key' => 'driver_ride_ready_at_pickup',
                'label' => '🚗 Listo en punto de recogida',
                'audience' => 'driver',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 91,
                'message_code' => 90,
                'title_es' => 'En punto de recogida',
                'message_es' => 'Marcaste llegada al punto de recogida. Puedes iniciar el viaje.',
                'title_en' => 'At pickup point',
                'message_en' => 'You marked arrival at pickup. You can start the ride.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 207,
            ],
            [
                'event_key' => 'passenger_shared_ride_join_pending',
                'label' => '🚌 Cupo enviado (viaje compartido)',
                'audience' => 'passenger',
                'category' => 'ride',
                'app_notification_type' => 1,
                'title_code' => 150,
                'message_code' => 21,
                'title_es' => 'Cupo enviado',
                'message_es' => 'Tu solicitud de cupo fue enviada al socio conductor. Te avisaremos cuando confirme.',
                'title_en' => 'Seat request sent',
                'message_en' => 'Your seat request was sent to the driver. We will notify you when confirmed.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 108,
            ],
            [
                'event_key' => 'driver_cash_out_processed',
                'label' => '🚗 Retiro de dinero procesado',
                'audience' => 'driver',
                'category' => 'wallet',
                'app_notification_type' => 6,
                'title_code' => 91,
                'message_code' => 338,
                'title_es' => 'Cash Out',
                'message_es' => 'Tu solicitud de retiro de dinero (Cash Out) ha sido procesada.',
                'title_en' => 'Cash Out',
                'message_en' => 'Your cash out request has been processed.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 205,
            ],
            [
                'event_key' => 'driver_cash_out_rejected',
                'label' => '🚗 Retiro de dinero rechazado',
                'audience' => 'driver',
                'category' => 'wallet',
                'app_notification_type' => 6,
                'title_code' => 91,
                'message_code' => 337,
                'title_es' => 'Cash Out',
                'message_es' => 'Tu solicitud de retiro de dinero (Cash Out) fue rechazada.',
                'title_en' => 'Cash Out',
                'message_en' => 'Your cash out request was rejected.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 206,
            ],
            [
                'event_key' => 'driver_document_approved',
                'label' => '🚗 Documento aprobado',
                'audience' => 'driver',
                'category' => 'system',
                'app_notification_type' => 13,
                'title_code' => 91,
                'message_code' => 369,
                'title_es' => 'Documentos',
                'message_es' => 'Tu documento cargado ha sido aprobado por el equipo de soporte.',
                'title_en' => 'Documents',
                'message_en' => 'Your uploaded document was approved by support.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 207,
            ],
            [
                'event_key' => 'driver_document_rejected',
                'label' => '🚗 Documento rechazado',
                'audience' => 'driver',
                'category' => 'system',
                'app_notification_type' => 13,
                'title_code' => 91,
                'message_code' => 368,
                'title_es' => 'Documentos',
                'message_es' => 'Tu documento ha sido rechazado. Por favor, verifícalo y súbelo de nuevo.',
                'title_en' => 'Documents',
                'message_en' => 'Your document was rejected. Please check it and upload again.',
                'sound_profile' => 'default',
                'placeholders' => '',
                'sort_order' => 208,
            ],
            [
                'event_key' => 'driver_document_expiry',
                'label' => '🚗 Alerta de documentos por vencer',
                'audience' => 'driver',
                'category' => 'system',
                'app_notification_type' => 13,
                'title_code' => 91,
                'message_code' => 341,
                'title_es' => 'Documentos',
                'message_es' => 'Tus documentos de conducción están cerca de expirar. Actualízalos pronto.',
                'title_en' => 'Documents',
                'message_en' => 'Your driving documents are about to expire. Update them soon.',
                'sound_profile' => 'default',
                'placeholders' => '{days}',
                'sort_order' => 209,
            ],
        ];
    }

    public static function seedDefaults(): void
    {
        self::syncCatalog();
    }

    public static function syncCatalog(): void
    {
        $catalogKeys = array_column(self::defaultCatalog(), 'event_key');
        PushEventTemplate::query()
            ->whereNotIn('event_key', $catalogKeys)
            ->update(['is_active' => false]);

        foreach (self::defaultCatalog() as $row) {
            PushEventTemplate::query()->updateOrCreate(
                ['event_key' => $row['event_key']],
                array_merge($row, ['is_active' => true])
            );
        }
        self::clearCache();
    }
}
