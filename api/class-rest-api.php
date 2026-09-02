<?php
namespace NandarkAtomic\API;

if (!defined('ABSPATH')) {
    exit;
}

class REST_API {
    const NAMESPACE = 'nandark/v1';

    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        // Endpoint: Información del Sitio & Configuración pública (/wp-json/nandark/v1/config)
        register_rest_route(self::NAMESPACE, '/config', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'get_site_config'],
            'permission_callback' => '__return_true', // Acceso público para apps móviles/frontends
        ]);

        // Endpoint: Listado de Servicios enriquecidos (/wp-json/nandark/v1/services)
        register_rest_route(self::NAMESPACE, '/services', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'get_services'],
            'permission_callback' => '__return_true',
        ]);

        // Endpoint: Creación de Leads / Citas / Reservas (/wp-json/nandark/v1/book)
        register_rest_route(self::NAMESPACE, '/book', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_booking'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function get_site_config() {
        return rest_ensure_response([
            'site_name'    => get_bloginfo('name'),
            'description'  => get_bloginfo('description'),
            'url'          => site_url(),
            'version'      => NANDARK_ATOMIC_VERSION,
            'api_version'  => '1.0.0',
            'capabilities' => [
                'rest'     => true,
                'graphql'  => class_exists('WPGraphQL'),
                'mcp'      => class_exists('WP_MCP_Server') || function_exists('mcp_adapter_init'),
            ],
        ]);
    }

    public static function get_services(\WP_REST_Request $request) {
        $query = new \WP_Query([
            'post_type'      => 'nandark_service',
            'posts_per_page' => 20,
            'post_status'    => 'publish',
        ]);

        $services = [];
        while ($query->have_posts()) {
            $query->the_post();
            $services[] = [
                'id'       => get_the_ID(),
                'title'    => get_the_title(),
                'slug'     => get_post_field('post_name'),
                'excerpt'  => get_the_excerpt(),
                'content'  => get_the_content(),
                'url'      => get_permalink(),
                'image'    => get_the_post_thumbnail_url(get_the_ID(), 'full') ?: null,
            ];
        }
        wp_reset_postdata();

        return rest_ensure_response($services);
    }

    public static function handle_booking(\WP_REST_Request $request) {
        $params = $request->get_json_params();

        $name    = sanitize_text_field($params['name'] ?? '');
        $phone   = sanitize_text_field($params['phone'] ?? '');
        $service = sanitize_text_field($params['service'] ?? '');
        $notes   = sanitize_textarea_field($params['notes'] ?? '');

        if (empty($name) || empty($phone)) {
            return new \WP_Error('missing_fields', 'Nombre y teléfono son requeridos', ['status' => 400]);
        }

        // Aquí se conecta con el servicio de WhatsApp / CRM
        $result = Services\Booking_Service::create_booking([
            'name'    => $name,
            'phone'   => $phone,
            'service' => $service,
            'notes'   => $notes,
        ]);

        return rest_ensure_response([
            'success' => true,
            'message' => 'Solicitud recibida con éxito',
            'data'    => $result,
        ]);
    }
}
