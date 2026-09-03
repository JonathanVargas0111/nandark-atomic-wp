<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

class Self_Updater {
    const GITHUB_REPO = 'JonathanVargas0111/nandark-atomic-wp';
    const SLUG        = 'nandark-atomic-core';
    const MAIN_FILE   = 'nandark-atomic-core/nandark-atomic-core.php';

    public static function init() {
        // 1. Hook para verificar si hay nueva versión (Transient de WP Updates)
        add_filter('pre_set_site_transient_update_plugins', [__CLASS__, 'check_update']);

        // 2. Hook para detalles del plugin en el modal de WP
        add_filter('plugins_api', [__CLASS__, 'plugin_info'], 20, 3);

        // 3. Hook para renombrar la carpeta del ZIP tras la actualización
        add_filter('upgrader_post_install', [__CLASS__, 'post_install'], 10, 3);

        // 4. Endpoint Webhook directo para actualización forzada instantánea
        add_action('rest_api_init', [__CLASS__, 'register_webhook_endpoint']);
    }

    /**
     * Consulta la API de GitHub Releases
     */
    private static function get_latest_release() {
        $transient_key = 'nandark_atomic_latest_release';
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            return $cached;
        }

        $url = 'https://api.github.com/repos/' . self::GITHUB_REPO . '/releases/latest';
        $args = [
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
                'Accept'     => 'application/vnd.github.v3+json',
            ],
            'timeout' => 10,
        ];

        // Token opcional si el repo llega a ser privado
        $token = defined('NANDARK_GITHUB_TOKEN') ? NANDARK_GITHUB_TOKEN : get_option('nandark_github_token', '');
        if (!empty($token)) {
            $args['headers']['Authorization'] = 'Bearer ' . $token;
        }

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response));
        if (!$data || empty($data->tag_name)) {
            return false;
        }

        set_transient($transient_key, $data, 300); // 5 minutos de caché
        return $data;
    }

    /**
     * Inyecta la actualización en el transient de WordPress
     */
    public static function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $release = self::get_latest_release();
        if (!$release) {
            return $transient;
        }

        $new_version = ltrim($release->tag_name, 'v');
        $current_version = NANDARK_ATOMIC_VERSION;

        if (version_compare($new_version, $current_version, '>')) {
            $package = !empty($release->zipball_url) ? $release->zipball_url : '';

            // Buscar asset .zip en el release si existe
            if (!empty($release->assets) && is_array($release->assets)) {
                foreach ($release->assets as $asset) {
                    if (str_ends_with($asset->name, '.zip')) {
                        $package = $asset->browser_download_url;
                        break;
                    }
                }
            }

            $obj = new \stdClass();
            $obj->id          = 'nandark-atomic-core';
            $obj->slug        = self::SLUG;
            $obj->plugin      = self::MAIN_FILE;
            $obj->new_version = $new_version;
            $obj->url         = 'https://github.com/' . self::GITHUB_REPO;
            $obj->package     = $package;
            $obj->icons       = [];
            $obj->banners     = [];
            $obj->tested      = '6.7';
            $obj->requires_php= '8.0';

            $transient->response[self::MAIN_FILE] = $obj;
        }

        return $transient;
    }

    /**
     * Muestra la información del release en el modal de actualización
     */
    public static function plugin_info($res, $action, $args) {
        if ($action !== 'plugin_information' || empty($args->slug) || $args->slug !== self::SLUG) {
            return $res;
        }

        $release = self::get_latest_release();
        if (!$release) {
            return $res;
        }

        $res = new \stdClass();
        $res->name          = 'Nandark Atomic Core';
        $res->slug          = self::SLUG;
        $res->version       = ltrim($release->tag_name, 'v');
        $res->author        = '<a href="https://nandark.com">Nandark Studio</a>';
        $res->homepage      = 'https://github.com/' . self::GITHUB_REPO;
        $res->requires      = '6.0';
        $res->tested        = '6.7';
        $res->requires_php  = '8.0';
        $res->last_updated  = $release->published_at;
        $res->sections      = [
            'description' => 'Motor atómico de alto rendimiento con Scrollytelling y soporte para agentes IA (MCP).',
            'changelog'   => nl2br(esc_html($release->body ?? 'Mejoras y correcciones automáticas.')),
        ];

        return $res;
    }

    /**
     * Asegura que tras la descarga del ZIP de GitHub la carpeta destino sea 'nandark-atomic-core'
     */
    public static function post_install($true, $hook_extra, $result) {
        global $wp_filesystem;

        if (isset($hook_extra['plugin']) && $hook_extra['plugin'] === self::MAIN_FILE) {
            $proper_destination = WP_PLUGIN_DIR . '/' . self::SLUG;
            $wp_filesystem->move($result['destination'], $proper_destination);
            $result['destination'] = $proper_destination;
            activate_plugin(self::MAIN_FILE);
        }

        return $result;
    }

    /**
     * Webhook REST: Permite que desde la terminal o GitHub Actions se fuerce el update al instante
     * POST /wp-json/nandark/v1/self-update
     */
    public static function register_webhook_endpoint() {
        register_rest_route('nandark/v1', '/self-update', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_direct_update'],
            'permission_callback' => [__CLASS__, 'verify_webhook_token'],
        ]);
    }

    public static function verify_webhook_token($request) {
        $auth_header = $request->get_header('authorization');
        $secret_key  = defined('NANDARK_DEPLOY_TOKEN') ? NANDARK_DEPLOY_TOKEN : get_option('nandark_deploy_token', 'nandark-secure-deploy-key-2026');

        if (!$auth_header) {
            return false;
        }

        $token = str_replace('Bearer ', '', trim($auth_header));
        return hash_equals($secret_key, $token);
    }

    public static function handle_direct_update() {
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';

        delete_transient('nandark_atomic_latest_release');
        delete_site_transient('update_plugins');

        wp_update_plugins();

        $skin = new \Automatic_Upgrader_Skin();
        $upgrader = new \Plugin_Upgrader($skin);

        $result = $upgrader->upgrade(self::MAIN_FILE);

        if (is_wp_error($result)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => $result->get_error_message(),
            ], 500);
        }

        return new \WP_REST_Response([
            'success'     => true,
            'message'     => 'Plugin Nandark Atomic Core actualizado con éxito desde GitHub.',
            'version'     => NANDARK_ATOMIC_VERSION,
            'upgraded_at' => current_time('mysql'),
        ], 200);
    }
}
