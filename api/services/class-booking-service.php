<?php
namespace NandarkAtomic\API\Services;

if (!defined('ABSPATH')) {
    exit;
}

class Booking_Service {
    public static function create_booking($data) {
        // Guarda la solicitud o dispara webhook a WhatsApp / CRM
        $booking_id = uniqid('book_');

        // Log o almacenamiento de prueba
        if (WP_DEBUG) {
            error_log("Nandark Booking [{$booking_id}]: " . wp_json_encode($data));
        }

        // Simulación de envío a WhatsApp API / Webhook
        WhatsApp_Service::send_notification($data);

        return [
            'booking_id' => $booking_id,
            'status'     => 'pending',
            'created_at' => current_time('mysql'),
        ];
    }
}
