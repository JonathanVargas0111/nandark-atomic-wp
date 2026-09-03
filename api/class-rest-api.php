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

        // Endpoint: Contenido estructurado de la Landing (/wp-json/nandark/v1/landing)
        register_rest_route(self::NAMESPACE, '/landing', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'get_landing_content'],
            'permission_callback' => '__return_true',
        ]);

        // Endpoint: Calculadora / Cotizador remoto (/wp-json/nandark/v1/quote)
        register_rest_route(self::NAMESPACE, '/quote', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'calculate_quote'],
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

    /**
     * Devuelve toda la información estructurada de la landing para consumo de frontends desacoplados (Next.js, Astro, Apps)
     */
    public static function get_landing_content() {
        return rest_ensure_response([
            'brand' => [
                'name'     => 'ORIGEN Gastrobar',
                'tagline'  => 'Cocina de Autor & Mixología Experimental',
                'location' => 'Calle 85 # 11 - 53, Bogotá',
                'whatsapp' => '573000000000',
            ],
            'hero_steps' => [
                [
                    'index' => '01',
                    'tag'   => 'Atmósfera Principal',
                    'title' => 'ORIGEN',
                    'desc'  => 'Una experiencia sensorial diseñada para recorrer los sabores del origen en un ambiente arquitectónico sobrio.',
                ],
                [
                    'index' => '02',
                    'tag'   => 'Propuesta Culinaria',
                    'title' => 'Platos de Autor',
                    'desc'  => 'Cortes madurados, reducciones artesanales de trufa y texturas diseñadas con precisión milimétrica.',
                ],
                [
                    'index' => '03',
                    'tag'   => 'Laboratorio de Bar',
                    'title' => 'Mixología Botánica',
                    'desc'  => 'Destilados selectos, hierbas aromáticas tratadas en frío y cristalería fina sobre barra de mármol.',
                ],
                [
                    'index' => '04',
                    'tag'   => 'Exclusividad & Noches',
                    'title' => 'Rooftop Lounge',
                    'desc'  => 'Mesas con vista panorámica y fogatas lineales. Disponibilidad limitada por turno.',
                ],
            ],
            'menu_highlights' => [
                ['name' => 'Ojo de Bife Madurado 45 Días', 'price' => '$115.000', 'category' => 'cocina', 'badge' => 'Chef Choice'],
                ['name' => 'Tartar de Atún Rojo & Trufa Negra', 'price' => '$68.000', 'category' => 'cocina', 'badge' => ''],
                ['name' => 'Humo de Origen', 'price' => '$46.000', 'category' => 'mixologia', 'badge' => 'Firma'],
                ['name' => 'Neblina Botánica', 'price' => '$42.000', 'category' => 'mixologia', 'badge' => ''],
            ],
            'experiences' => [
                ['id' => 'degustacion', 'name' => 'Menú Degustación 6 Tiempos', 'price_per_person' => 180000],
                ['id' => 'maridaje', 'name' => 'Maridaje & Sommelier', 'price_per_person' => 260000],
                ['id' => 'rooftop', 'name' => 'Rooftop Cócteles & Fuego', 'price_per_person' => 140000],
            ],
            'addons' => [
                'sommelier_cava' => ['name' => 'Acceso a Cava Privada de Vinos', 'price_per_person' => 45000],
            ],
        ]);
    }

    /**
     * Calcula cotización de reservas vía API para chatbots o apps externas
     */
    public static function calculate_quote(\WP_REST_Request $request) {
        $params = $request->get_json_params();

        $guests          = max(1, min(50, (int) ($params['guests'] ?? 4)));
        $experience      = sanitize_text_field($params['experience'] ?? 'degustacion');
        $sommelier_addon = !empty($params['sommelier_addon']);
        $turn            = sanitize_text_field($params['turn'] ?? 'cena');

        $prices = [
            'degustacion' => 180000,
            'maridaje'    => 260000,
            'rooftop'     => 140000,
        ];

        $experience_names = [
            'degustacion' => 'Menú Degustación 6 Pasos',
            'maridaje'    => 'Experiencia con Maridaje de Alta Gama',
            'rooftop'     => 'Cócteles & Tapas en Terraza Rooftop',
        ];

        $unit_price = $prices[$experience] ?? $prices['degustacion'];
        if ($sommelier_addon) {
            $unit_price += 45000;
        }

        $total = $unit_price * $guests;

        $turn_label = ($turn === 'almuerzo') ? 'Turno Tarde (1:00 PM)' : 'Turno Noche (7:30 PM)';
        $addon_text = $sommelier_addon ? ' + Cava Privada de Vinos' : '';
        $exp_name   = $experience_names[$experience] ?? $experience_names['degustacion'];

        $whatsapp_text = rawurlencode(
            "Hola Origen, coticé vía API una reserva para {$guests} personas ({$exp_name}{$addon_text}) en {$turn_label}. Deseo verificar disponibilidad."
        );

        return rest_ensure_response([
            'success' => true,
            'quote'   => [
                'guests'           => $guests,
                'experience'       => $exp_name,
                'turn'             => $turn_label,
                'sommelier_addon'  => $sommelier_addon,
                'price_per_person' => $unit_price,
                'total'            => $total,
                'formatted_total'  => '$ ' . number_format($total, 0, ',', '.'),
                'whatsapp_url'     => "https://wa.me/573000000000?text={$whatsapp_text}",
            ],
        ]);
    }
}
