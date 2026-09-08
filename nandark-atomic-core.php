<?php
/**
 * Plugin Name:       Nandark Atomic Core
 * Plugin URI:        https://nandark.com
 * Description:       Arquitectura de componentes atómicos, CPTs y optimización de alto rendimiento para WordPress asistido por IA (MCP).
 * Version:           1.0.6
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Nandark Studio (Felipe Vargas)
 * Author URI:        https://nandark.com
 * License:           GPL v2 or later
 * Text Domain:       nandark-atomic
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('NANDARK_ATOMIC_VERSION', '1.0.6');
define('NANDARK_ATOMIC_PATH', plugin_dir_path(__FILE__));
define('NANDARK_ATOMIC_URL', plugin_dir_url(__FILE__));

// Habilitar Application Passwords en entorno local (HTTP)
add_filter('wp_is_application_passwords_available', '__return_true');

/**
 * Helper global para renderizar componentes de Atomic Design
 *
 * @param string $component_path Ruta relativa (ej: 'atoms/button', 'molecules/service-card')
 * @param array  $props          Propiedades/datos que recibe el componente
 * @param bool   $echo           Si se imprime directamente o devuelve string
 * @return string|void
 */
function nandark_render($component_path, $props = [], $echo = true) {
    $relative_path = 'components/' . ltrim($component_path, '/') . '.php';

    // 1. Prioridad: Buscar si el Tema Activo (o Child Theme) tiene el componente personalizado
    $theme_file = locate_template($relative_path);
    $file = $theme_file ? $theme_file : (NANDARK_ATOMIC_PATH . $relative_path);

    if (!file_exists($file)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("Nandark Atomic: Componente no encontrado en {$file}");
        }
        return '';
    }

    // Extrae las propiedades para que estén disponibles como variables locales
    extract($props, EXTR_SKIP);

    if (!$echo) {
        ob_start();
        include $file;
        return ob_get_clean();
    }

    include $file;
}

/**
 * Resuelve de dónde salen los frames del scrollytelling.
 *
 * Antes vivían versionados en assets/frames/ dentro del plugin: 240 JPGs, 51 MB.
 * Eso hacía que el plugin pesara 54 MB y que CADA auto-update descargara y
 * descomprimiera esos 54 MB en un shared hosting con max_execution_time de 30s
 * — la receta exacta de una actualización que muere a la mitad.
 *
 * Ahora viven en la media library (subidos por MCP) y el plugin los descubre
 * solo: busca el attachment 'nandark-frame-0001' y deriva la URL base de ahí.
 * Cero configuración manual. El resultado se cachea 24 h.
 *
 * @return array{base:string, prefix:string}
 */
function nandark_frames_source() {
    $legacy = [
        'base'   => NANDARK_ATOMIC_URL . 'assets/frames/',
        'prefix' => 'frame_',
    ];

    // Override explícito, por si algún sitio los sirve desde un CDN.
    $base = get_option('nandark_frames_base_url', '');
    if (!empty($base)) {
        return [
            'base'   => trailingslashit($base),
            'prefix' => get_option('nandark_frames_prefix', 'nandark-frame-'),
        ];
    }

    $cached = get_transient('nandark_frames_source');
    if (is_array($cached)) {
        return $cached;
    }

    $prefix = 'nandark-frame-';
    $first  = get_page_by_path($prefix . '0001', OBJECT, 'attachment');

    if ($first) {
        $url = wp_get_attachment_url($first->ID);
        if ($url) {
            $resolved = [
                'base'   => trailingslashit(dirname($url)),
                'prefix' => $prefix,
            ];
            set_transient('nandark_frames_source', $resolved, DAY_IN_SECONDS);
            return $resolved;
        }
    }

    // Sin frames en la mediateca: el hero degrada a la primera imagen fija.
    return $legacy;
}

/**
 * Resuelve LA imagen del hero estático.
 *
 * El hero de 240 frames simulaba un video y le costaba ~55 MB de descarga a cada
 * visitante. Una sola imagen hace el mismo trabajo visual por 240 veces menos.
 *
 * Devuelve el ID del attachment además de la URL: con el ID podemos usar
 * wp_get_attachment_image(), que emite srcset y sizes, así un celular se baja la
 * versión chica en vez de la de 1920px. Servirla desde la media library también
 * permite que LiteSpeed la convierta a WebP — cosa imposible para un archivo
 * que viva dentro del plugin.
 *
 * @return array{id:int|null, url:string}
 */
function nandark_hero_image() {
    $override = get_option('nandark_hero_image_url', '');
    if (!empty($override)) {
        return ['id' => null, 'url' => $override];
    }

    $cached = get_transient('nandark_hero_image');
    if (is_array($cached)) {
        return $cached;
    }

    $slug = apply_filters('nandark_hero_image_slug', 'nandark-frame-0001');
    $att  = get_page_by_path($slug, OBJECT, 'attachment');

    if ($att) {
        $url = wp_get_attachment_url($att->ID);
        if ($url) {
            $resolved = ['id' => (int) $att->ID, 'url' => $url];
            set_transient('nandark_hero_image', $resolved, DAY_IN_SECONDS);
            return $resolved;
        }
    }

    // Sin nada en la mediateca: la imagen que viene con el plugin.
    return ['id' => null, 'url' => NANDARK_ATOMIC_URL . 'assets/images/01-hero-lounge.jpg'];
}

/**
 * Carga de módulos principales
 */
require_once NANDARK_ATOMIC_PATH . 'includes/class-assets-loader.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-cpt-manager.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-self-updater.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-bundle-manager.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-performance-optimizer.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-seo-schema-manager.php';
require_once NANDARK_ATOMIC_PATH . 'theme/template-loader.php';

// Capa Backend / API & Servicios
require_once NANDARK_ATOMIC_PATH . 'api/services/class-whatsapp-service.php';
require_once NANDARK_ATOMIC_PATH . 'api/services/class-booking-service.php';
require_once NANDARK_ATOMIC_PATH . 'api/class-rest-api.php';
require_once NANDARK_ATOMIC_PATH . 'api/class-graphql-schema.php';

// Inicialización de módulos
add_action('plugins_loaded', function () {
    \NandarkAtomic\Performance_Optimizer::init();
    \NandarkAtomic\Seo_Schema_Manager::init();
    \NandarkAtomic\Assets_Loader::init();
    \NandarkAtomic\CPT_Manager::init();
    \NandarkAtomic\Self_Updater::init();
    \NandarkAtomic\Bundle_Manager::init();
    \NandarkAtomic\Template_Loader::init();
    \NandarkAtomic\API\REST_API::init();
    \NandarkAtomic\API\GraphQL_Schema::init();
});
