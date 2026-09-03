<?php
/**
 * Plugin Name:       Nandark Atomic Core
 * Plugin URI:        https://nandark.com
 * Description:       Arquitectura de componentes atómicos, CPTs y optimización de alto rendimiento para WordPress asistido por IA (MCP).
 * Version:           1.0.0
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

define('NANDARK_ATOMIC_VERSION', '1.0.0');
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
    $file = NANDARK_ATOMIC_PATH . 'components/' . ltrim($component_path, '/') . '.php';

    if (!file_exists($file)) {
        if (WP_DEBUG) {
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
 * Carga de módulos principales
 */
require_once NANDARK_ATOMIC_PATH . 'includes/class-assets-loader.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-cpt-manager.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-self-updater.php';
require_once NANDARK_ATOMIC_PATH . 'includes/class-bundle-manager.php';
require_once NANDARK_ATOMIC_PATH . 'theme/template-loader.php';

// Capa Backend / API & Servicios
require_once NANDARK_ATOMIC_PATH . 'api/services/class-whatsapp-service.php';
require_once NANDARK_ATOMIC_PATH . 'api/services/class-booking-service.php';
require_once NANDARK_ATOMIC_PATH . 'api/class-rest-api.php';
require_once NANDARK_ATOMIC_PATH . 'api/class-graphql-schema.php';

// Inicialización de módulos
add_action('plugins_loaded', function () {
    \NandarkAtomic\Assets_Loader::init();
    \NandarkAtomic\CPT_Manager::init();
    \NandarkAtomic\Self_Updater::init();
    \NandarkAtomic\Bundle_Manager::init();
    \NandarkAtomic\Template_Loader::init();
    \NandarkAtomic\API\REST_API::init();
    \NandarkAtomic\API\GraphQL_Schema::init();
});
