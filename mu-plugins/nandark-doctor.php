<?php
/**
 * Plugin Name: Nandark Doctor
 * Description: Diagnóstico remoto de SOLO LECTURA. Vive como mu-plugin para sobrevivir cuando el plugin principal se cae.
 * Version:     1.0.0
 * Author:      Nandark Studio (Felipe Vargas)
 *
 * ============================================================================
 * POR QUÉ ESTO VIVE EN mu-plugins/ Y NO EN EL PLUGIN
 * ============================================================================
 * Un endpoint de diagnóstico metido dentro de nandark-atomic-core muere junto
 * con él: cuando el plugin tira un fatal o desaparece del disco — que es
 * exactamente cuando necesitás leer el log — el endpoint tampoco existe.
 * No puede rescatar aquello dentro de lo cual vive.
 *
 * WordPress carga los mu-plugins SIEMPRE, ANTES de los plugins normales, y no
 * se pueden desactivar desde el admin. Así que este archivo sigue respondiendo
 * aunque el Core esté roto, borrado o desactivado.
 *
 * ============================================================================
 * ES DE SOLO LECTURA. A PROPÓSITO.
 * ============================================================================
 * No ejecuta comandos, no escribe archivos, no activa ni desactiva nada, no
 * toca la base de datos. Un endpoint de "ejecutá lo que te mande" protegido por
 * un Bearer token es ejecución remota de código: si el token se filtra — cosa
 * que en este proyecto ya pasó una vez — el servidor es de quien lo tenga.
 * Leer no tiene ese problema.
 *
 * ============================================================================
 * INSTALACIÓN (una sola vez)
 * ============================================================================
 * 1. Subir este archivo a  wp-content/mu-plugins/nandark-doctor.php
 *    (crear la carpeta mu-plugins si no existe). No hay que activarlo.
 * 2. En wp-config.php, antes de "That's all, stop editing":
 *        define('NANDARK_DEPLOY_TOKEN', '<token de 32+ caracteres>');
 *    Si querés un token aparte solo para diagnóstico, definí además
 *    NANDARK_DOCTOR_TOKEN y este archivo va a preferir ese.
 * 3. Para que haya log que leer:
 *        define('WP_DEBUG', true);
 *        define('WP_DEBUG_LOG', true);
 *        define('WP_DEBUG_DISPLAY', false);
 *
 * ============================================================================
 * USO
 * ============================================================================
 *   GET /wp-json/nandark-doctor/v1/health
 *   GET /wp-json/nandark-doctor/v1/log?lines=50
 *   Header:  Authorization: Bearer <token>
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('Nandark_Doctor')) {
    return;
}

class Nandark_Doctor {

    const NAMESPACE_        = 'nandark-doctor/v1';
    const MIN_TOKEN_LENGTH  = 32;
    const MAX_LINES         = 200;
    const DEFAULT_LINES     = 50;
    const CORE_MAIN_FILE    = 'nandark-atomic-core/nandark-atomic-core.php';

    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route(self::NAMESPACE_, '/health', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'health'],
            'permission_callback' => [__CLASS__, 'authorize'],
        ]);

        register_rest_route(self::NAMESPACE_, '/log', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'log'],
            'permission_callback' => [__CLASS__, 'authorize'],
            'args'                => [
                'lines' => [
                    'type'    => 'integer',
                    'default' => self::DEFAULT_LINES,
                ],
            ],
        ]);
    }

    /**
     * Falla cerrado: sin token configurado no responde nada.
     */
    public static function authorize($request) {
        if (function_exists('current_user_can') && current_user_can('manage_options')) {
            return true;
        }

        $secret = '';
        if (defined('NANDARK_DOCTOR_TOKEN')) {
            $secret = NANDARK_DOCTOR_TOKEN;
        } elseif (defined('NANDARK_DEPLOY_TOKEN')) {
            $secret = NANDARK_DEPLOY_TOKEN;
        }

        if (!is_string($secret) || strlen($secret) < self::MIN_TOKEN_LENGTH) {
            return new WP_Error(
                'rest_forbidden',
                'Nandark Doctor: definí NANDARK_DOCTOR_TOKEN (o NANDARK_DEPLOY_TOKEN) en wp-config.php, mínimo ' . self::MIN_TOKEN_LENGTH . ' caracteres.',
                ['status' => 403]
            );
        }

        // Rate limit simple por IP: 20 lecturas por minuto.
        $ip  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
        $key = 'nandark_doctor_rate_' . md5($ip);
        $hits = (int) get_transient($key);
        if ($hits >= 20) {
            return new WP_Error('rest_rate_limited', 'Demasiadas consultas. Esperá un minuto.', ['status' => 429]);
        }
        set_transient($key, $hits + 1, 60);

        $header = $request->get_header('authorization');
        if (!$header) {
            return false;
        }

        $token = trim(preg_replace('/^Bearer\s+/i', '', trim($header)));
        if ($token === '') {
            return false;
        }

        return hash_equals($secret, $token);
    }

    /**
     * Estado del sitio: entorno, límites, plugins, y si el Core sigue en pie.
     */
    public static function health() {
        return new WP_REST_Response([
            'success'   => true,
            'checked_at'=> function_exists('current_time') ? current_time('mysql') : gmdate('Y-m-d H:i:s'),
            'wordpress' => [
                'version'     => get_bloginfo('version'),
                'multisite'   => is_multisite(),
                'debug'       => defined('WP_DEBUG') && WP_DEBUG,
                'debug_log'   => defined('WP_DEBUG_LOG') && WP_DEBUG_LOG,
                'home_url'    => home_url(),
            ],
            'php' => [
                'version'             => PHP_VERSION,
                'memory_limit'        => ini_get('memory_limit'),
                'memory_peak_mb'      => round(memory_get_peak_usage(true) / 1048576, 1),
                'max_execution_time'  => ini_get('max_execution_time'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size'       => ini_get('post_max_size'),
            ],
            'disk' => [
                'free_mb' => self::disk_free_mb(),
            ],
            'core_plugin' => self::core_status(),
            'paused'      => self::paused_extensions(),
            'plugins'     => self::plugin_list(),
        ], 200);
    }

    /**
     * Últimas N líneas del debug.log, leídas desde el final del archivo.
     */
    public static function log($request) {
        $lines = (int) $request->get_param('lines');
        if ($lines < 1) {
            $lines = self::DEFAULT_LINES;
        }
        $lines = min($lines, self::MAX_LINES);

        $path = self::log_path();

        if (!$path) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'No hay debug.log. Activá WP_DEBUG y WP_DEBUG_LOG en wp-config.php.',
                'looked_in' => self::redact(defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/debug.log' : ''),
            ], 404);
        }

        $tail = self::tail($path, $lines);
        if ($tail === null) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'El debug.log existe pero no se pudo leer (permisos).',
            ], 500);
        }

        return new WP_REST_Response([
            'success'    => true,
            'file'       => self::redact($path),
            'size_kb'    => round(filesize($path) / 1024, 1),
            'modified'   => gmdate('Y-m-d H:i:s', filemtime($path)) . ' UTC',
            'lines'      => count($tail),
            'log'        => array_map([__CLASS__, 'redact'], $tail),
        ], 200);
    }

    // ---------------------------------------------------------------- helpers

    private static function log_path() {
        $candidates = [];
        if (defined('WP_DEBUG_LOG') && is_string(WP_DEBUG_LOG) && WP_DEBUG_LOG !== '') {
            $candidates[] = WP_DEBUG_LOG;
        }
        if (defined('WP_CONTENT_DIR')) {
            $candidates[] = WP_CONTENT_DIR . '/debug.log';
        }
        $ini = ini_get('error_log');
        if ($ini) {
            $candidates[] = $ini;
        }

        foreach ($candidates as $c) {
            if ($c && @is_readable($c) && @is_file($c)) {
                return $c;
            }
        }
        return null;
    }

    /**
     * Lee el final del archivo por bloques, sin cargarlo entero en memoria:
     * un debug.log puede tener cientos de MB y un file() lo mataría.
     */
    private static function tail($file, $lines) {
        $fh = @fopen($file, 'rb');
        if (!$fh) {
            return null;
        }

        $chunk = 4096;
        fseek($fh, 0, SEEK_END);
        $pos    = ftell($fh);
        $buffer = '';
        $found  = 0;

        while ($pos > 0 && $found <= $lines) {
            $read = min($chunk, $pos);
            $pos -= $read;
            fseek($fh, $pos, SEEK_SET);
            $buffer = fread($fh, $read) . $buffer;
            $found  = substr_count($buffer, "\n");
        }
        fclose($fh);

        $all = explode("\n", rtrim($buffer, "\n"));
        return array_slice($all, -$lines);
    }

    /**
     * ¿El plugin principal sigue en disco? Es la pregunta que originó todo esto.
     */
    private static function core_status() {
        if (!defined('WP_PLUGIN_DIR')) {
            return ['installed' => null, 'note' => 'WP_PLUGIN_DIR no definido'];
        }

        $main = WP_PLUGIN_DIR . '/' . self::CORE_MAIN_FILE;
        $dir  = dirname($main);

        $status = [
            'dir_exists'   => is_dir($dir),
            'file_exists'  => file_exists($main),
            'version'      => null,
            'active'       => null,
        ];

        if ($status['file_exists']) {
            $head = @file_get_contents($main, false, null, 0, 2048);
            if ($head && preg_match('/^\s*\*\s*Version:\s*(.+)$/mi', $head, $m)) {
                $status['version'] = trim($m[1]);
            }
        }

        $active = get_option('active_plugins', []);
        if (is_array($active)) {
            $status['active'] = in_array(self::CORE_MAIN_FILE, $active, true);
        }

        return $status;
    }

    /**
     * Extensiones que WordPress pausó por un fatal (recovery mode).
     * Es la respuesta directa a "¿qué plugin murió?".
     */
    private static function paused_extensions() {
        $out = ['plugins' => [], 'themes' => []];

        if (function_exists('wp_paused_plugins')) {
            $storage = wp_paused_plugins();
            if (is_object($storage) && method_exists($storage, 'get_all')) {
                $out['plugins'] = $storage->get_all();
            }
        }
        if (function_exists('wp_paused_themes')) {
            $storage = wp_paused_themes();
            if (is_object($storage) && method_exists($storage, 'get_all')) {
                $out['themes'] = $storage->get_all();
            }
        }

        return $out;
    }

    private static function plugin_list() {
        if (!function_exists('get_plugins')) {
            $file = ABSPATH . 'wp-admin/includes/plugin.php';
            if (!@is_readable($file)) {
                return [];
            }
            require_once $file;
        }

        $active = get_option('active_plugins', []);
        $active = is_array($active) ? $active : [];
        $out    = [];

        foreach (get_plugins() as $file => $data) {
            $out[] = [
                'file'    => $file,
                'name'    => isset($data['Name']) ? $data['Name'] : '',
                'version' => isset($data['Version']) ? $data['Version'] : '',
                'active'  => in_array($file, $active, true),
            ];
        }

        return $out;
    }

    private static function disk_free_mb() {
        if (!function_exists('disk_free_space') || !defined('ABSPATH')) {
            return null;
        }
        $free = @disk_free_space(ABSPATH);
        return $free === false ? null : round($free / 1048576);
    }

    /**
     * No filtrar la ruta absoluta del servidor en las respuestas.
     */
    public static function redact($text) {
        if (!is_string($text) || !defined('ABSPATH')) {
            return $text;
        }
        return str_replace(rtrim(ABSPATH, '/\\'), '{ABSPATH}', $text);
    }
}

Nandark_Doctor::init();
