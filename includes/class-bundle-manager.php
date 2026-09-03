<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

class Bundle_Manager {

    /**
     * Definición de dependencias oficiales de la suite Nandark
     */
    private static function get_bundle_definitions() {
        return [
            'mcp-adapter' => [
                'name'      => 'WordPress MCP Adapter',
                'slug'      => 'mcp-adapter',
                'file'      => 'mcp-adapter/mcp-adapter.php',
                'type'      => 'github',
                'repo'      => 'WordPress/mcp-adapter',
                'zip_url'   => 'https://github.com/WordPress/mcp-adapter/releases/latest/download/mcp-adapter.zip',
            ],
            'enable-abilities-for-mcp' => [
                'name'      => 'Enable Abilities for MCP',
                'slug'      => 'enable-abilities-for-mcp',
                'file'      => 'enable-abilities-for-mcp/enable-abilities-for-mcp.php',
                'type'      => 'wporg',
                'repo'      => 'enable-abilities-for-mcp',
                'zip_url'   => 'https://downloads.wordpress.org/plugin/enable-abilities-for-mcp.latest-stable.zip',
            ],
            'wp-graphql' => [
                'name'      => 'WPGraphQL',
                'slug'      => 'wp-graphql',
                'file'      => 'wp-graphql/wp-graphql.php',
                'type'      => 'wporg',
                'repo'      => 'wp-graphql',
                'zip_url'   => 'https://downloads.wordpress.org/plugin/wp-graphql.latest-stable.zip',
            ],
        ];
    }

    public static function init() {
        // Notificaciones en el dashboard si faltan plugins del bundle
        add_action('admin_notices', [__CLASS__, 'render_admin_notice']);

        // Endpoints REST para autoinstalación remota
        add_action('rest_api_init', [__CLASS__, 'register_rest_routes']);
    }

    /**
     * Revisa el estado de instalación y activación de cada plugin del bundle
     */
    public static function get_bundle_status() {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $installed_plugins = get_plugins();
        $status = [];

        foreach (self::get_bundle_definitions() as $key => $info) {
            $is_installed = isset($installed_plugins[$info['file']]);
            $is_active    = $is_installed && is_plugin_active($info['file']);

            $status[$key] = [
                'name'         => $info['name'],
                'slug'         => $info['slug'],
                'file'         => $info['file'],
                'is_installed' => $is_installed,
                'is_active'    => $is_active,
                'version'      => $is_installed ? $installed_plugins[$info['file']]['Version'] : null,
            ];
        }

        return $status;
    }

    /**
     * Muestra aviso amigable en wp-admin si falta activar o instalar alguna pieza del bundle
     */
    public static function render_admin_notice() {
        if (!current_user_can('install_plugins')) {
            return;
        }

        $status = self::get_bundle_status();
        $missing = [];

        foreach ($status as $key => $item) {
            if (!$item['is_active']) {
                $missing[] = $item['name'] . ($item['is_installed'] ? ' (inactivo)' : ' (no instalado)');
            }
        }

        if (empty($missing)) {
            return;
        }

        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>[Nandark Atomic Bundle]:</strong> Se recomiendan los siguientes complementos para habilitar la suite completa de IA y GraphQL:</p>';
        echo '<ul style="list-style: disc; margin-left: 20px;">';
        foreach ($missing as $m) {
            echo '<li>' . esc_html($m) . '</li>';
        }
        echo '</ul>';
        echo '<p>Puedes aprovisionarlos en 1 clic ejecutando <code>./deploy-update.sh --bundle</code> desde tu terminal.</p>';
        echo '</div>';
    }

    /**
     * Rutas REST para consultar e instalar el bundle
     */
    public static function register_rest_routes() {
        register_rest_route('nandark/v1', '/bundle-status', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'handle_get_status'],
            'permission_callback' => [__CLASS__, 'verify_permission'],
        ]);

        register_rest_route('nandark/v1', '/install-bundle', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_install_bundle'],
            'permission_callback' => [__CLASS__, 'verify_permission'],
        ]);
    }

    public static function verify_permission($request) {
        return Self_Updater::verify_webhook_token($request);
    }

    public static function handle_get_status() {
        return new \WP_REST_Response([
            'success' => true,
            'bundle'  => self::get_bundle_status(),
        ], 200);
    }

    /**
     * Descarga, instala y activa automáticamente las dependencias que falten
     */
    public static function handle_install_bundle() {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';

        $definitions = self::get_bundle_definitions();
        $status      = self::get_bundle_status();
        $installed_plugins = get_plugins();

        $results = [];

        foreach ($definitions as $key => $info) {
            $is_installed = isset($installed_plugins[$info['file']]);
            $is_active    = $is_installed && is_plugin_active($info['file']);

            if ($is_active) {
                $results[$key] = 'Ya instalado y activo';
                continue;
            }

            if (!$is_installed) {
                // Instalar plugin desde origen oficial
                $skin     = new \Automatic_Upgrader_Skin();
                $upgrader = new \Plugin_Upgrader($skin);

                if ($info['type'] === 'wporg') {
                    $api = plugins_api('plugin_information', ['slug' => $info['slug'], 'fields' => ['sections' => false]]);
                    if (!is_wp_error($api) && !empty($api->download_link)) {
                        $install_res = $upgrader->install($api->download_link);
                    } else {
                        $install_res = $upgrader->install($info['zip_url']);
                    }
                } else {
                    $install_res = $upgrader->install($info['zip_url']);
                }

                if (is_wp_error($install_res)) {
                    $results[$key] = 'Error en instalación: ' . $install_res->get_error_message();
                    continue;
                }
            }

            // Activar plugin
            $activate_res = activate_plugin($info['file']);
            if (is_wp_error($activate_res)) {
                $results[$key] = 'Instalado pero falló activación: ' . $activate_res->get_error_message();
            } else {
                $results[$key] = 'Instalado y activado exitosamente';
            }
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Proceso de aprovisionamiento de bundle completado.',
            'results' => $results,
            'bundle'  => self::get_bundle_status(),
        ], 200);
    }
}
