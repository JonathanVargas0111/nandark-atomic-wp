<?php
namespace NandarkAtomic\API\Services;

if (!defined('ABSPATH')) {
    exit;
}

class WhatsApp_Service {
    public static function send_notification($booking_data) {
        // Enlace directo de notificación o disparo de webhook
        $phone   = $booking_data['phone'] ?? '';
        $name    = $booking_data['name'] ?? '';
        $service = $booking_data['service'] ?? 'General';

        $message = "Nueva reserva recibida de {$name} para el servicio: {$service}. Teléfono: {$phone}";

        // Si existe webhook configurado en opciones de WordPress:
        $webhook_url = get_option('nandark_whatsapp_webhook_url');
        if (!empty($webhook_url)) {
            wp_remote_post($webhook_url, [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => wp_json_encode([
                    'message' => $message,
                    'data'    => $booking_data,
                ]),
                'timeout' => 5,
            ]);
        }

        return true;
    }
}
