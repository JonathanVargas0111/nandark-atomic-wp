<?php
namespace NandarkAtomic\API;

if (!defined('ABSPATH')) {
    exit;
}

class GraphQL_Schema {
    public static function init() {
        // Se engancha solo cuando WPGraphQL está activo
        add_action('graphql_register_types', [__CLASS__, 'register_types']);
    }

    public static function register_types() {
        if (!function_exists('register_graphql_field')) {
            return;
        }

        // Campo personalizado en el esquema de GraphQL para Servicios
        register_graphql_field('NandarkService', 'whatsappBookingUrl', [
            'type'        => 'String',
            'description' => 'URL preformateada para agendar este servicio directamente por WhatsApp',
            'resolve'     => function ($service) {
                $title = get_the_title($service->ID);
                return 'https://wa.me/573000000000?text=' . rawurlencode("Hola, quiero agendar: {$title}");
            },
        ]);
    }
}
