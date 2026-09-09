<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registro de auditoría de acciones de agentes de IA.
 *
 * POR QUÉ EXISTE
 * Hasta ahora no había forma de responder "¿qué hizo el agente en este sitio?".
 * Eso no es un problema de ingeniería sino de responsabilidad: si un agente rompe
 * algo en el sitio de un cliente, sin registro no se puede demostrar qué pasó ni
 * quién lo pidió.
 *
 * QUÉ NO HACE
 * No reemplaza el log que ya trae "Enable Abilities for MCP"
 * (includes/activity-log.php → tabla {prefix}ewpa_activity_log). Ese envuelve las
 * abilities y guarda que ALGO ocurrió. Lo completa: acá quedan los argumentos, la
 * IP, el user agent, el resultado y las llamadas RECHAZADAS — nada de eso lo
 * guarda el log del plugin.
 *
 * DÓNDE ENGANCHA Y POR QUÉ SON DOS HOOKS Y NO UNO
 *
 *   mcp_adapter_tool_call_result   → una fila por ability EJECUTADA
 *   rest_post_dispatch (rutas MCP) → una fila por llamada RECHAZADA
 *
 * Hacen falta los dos porque el adapter corta antes de llegar a su propio filtro
 * cuando la tool no existe (ToolsHandler.php:147) o cuando falla el chequeo de
 * permisos (:168). O sea que el filtro del adapter, solo, es ciego justamente a
 * los intentos que más importan en una auditoría de seguridad.
 *
 * A la inversa, rest_post_dispatch solo tampoco alcanza: el transporte MCP acepta
 * lotes JSON-RPC, así que un único POST puede contener N tool calls y ese filtro
 * daría UNA fila para las N.
 *
 * REGLAS DURAS DE ESTE ARCHIVO
 * 1. Los dos son FILTROS. Si no devolvemos el valor recibido, rompemos el sitio:
 *    WordPress llama ->get_headers() y ->get_status() sobre lo que devolvemos
 *    (class-wp-rest-server.php:474-476). Devolver null = fatal en cada request
 *    REST, wp-admin incluido.
 * 2. Nunca se loguea el header Authorization ni el cuerpo completo.
 * 3. Auditar jamás puede tumbar el sitio: todo va envuelto en try/catch.
 */
class Agent_Audit {

    /** Tipo de contenido privado donde viven las filas del log. */
    const CPT = 'nandark_agent_log';

    /** Prefijo de ruta REST de los servidores MCP. Cubre el default y el de OAuth. */
    const MCP_ROUTE_PREFIX = '/mcp/';

    /** Tope de bytes que guardamos de los argumentos. Un post por llamada no puede pesar. */
    const MAX_ARGS_BYTES = 2000;

    /** Días que sobrevive una fila antes de que el cron la borre. */
    const RETENTION_DAYS = 90;

    const CRON_HOOK = 'nandark_agent_audit_purge';

    /**
     * Cuántas tool calls logueamos en el request HTTP actual.
     *
     * Sirve para no duplicar: si el filtro del adapter ya registró la llamada, el
     * de rest_post_dispatch no la vuelve a escribir. La diferencia entre lo pedido
     * y lo logueado es, exactamente, lo que el adapter rechazó antes de ejecutar.
     *
     * @var int
     */
    private static $logged_in_request = 0;

    /**
     * Identificador del request que ya cerramos, para no cerrarlo dos veces.
     *
     * rest_post_dispatch puede correr mas de una vez sobre la misma peticion. Sin
     * esta marca, la segunda pasada veria el contador ya en cero, restaria contra
     * las llamadas pedidas y escribiria una fila "blocked" que nunca ocurrio:
     * ruido inventado dentro de la unica tabla que tiene que ser confiable.
     *
     * @var int|null
     */
    private static $finalized_request = null;

    public static function init() {
        add_action('init', [__CLASS__, 'register_cpt']);

        // Una fila por ability ejecutada. El docblock del adapter nombra
        // explícitamente "audit logging" como uso previsto de este filtro.
        add_filter('mcp_adapter_tool_call_result', [__CLASS__, 'log_tool_result'], 999, 3);

        // Una fila por llamada que nunca llegó a ejecutarse.
        add_filter('rest_post_dispatch', [__CLASS__, 'log_rejected'], 999, 3);

        add_action(self::CRON_HOOK, [__CLASS__, 'purge_old_entries']);
        add_action('init', [__CLASS__, 'schedule_purge']);
    }

    /**
     * El CPT es privado a propósito y en varios ejes a la vez.
     *
     * 'show_in_rest' => false no es opcional: los argumentos guardados pueden traer
     * contenido del cliente, y exponerlos por la API sería filtrar por la puerta de
     * atrás justo lo que vinimos a proteger.
     *
     * 'rewrite' => false tampoco es cosmético: este plugin no tiene activation hook
     * ni flush de rewrite rules en ningún lado, así que un CPT con slug quedaría
     * dando 404 hasta que alguien entrara a Ajustes ▸ Enlaces permanentes.
     */
    public static function register_cpt() {
        register_post_type(self::CPT, [
            'labels' => [
                'name'          => 'Auditoría de agentes',
                'singular_name' => 'Registro de agente',
                'menu_name'     => 'Auditoría IA',
            ],
            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => false,
            'has_archive'         => false,
            'rewrite'             => false,
            'query_var'           => false,
            'menu_icon'           => 'dashicons-shield-alt',
            'menu_position'       => 80,
            'supports'            => ['title'],
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            // Nadie crea ni edita filas a mano: un log que se puede editar desde el
            // admin no sirve como evidencia de nada.
            'capabilities'        => [
                'create_posts' => 'do_not_allow',
                'edit_post'    => 'do_not_allow',
                'edit_posts'   => 'manage_options',
            ],
        ]);
    }

    /**
     * Registra una ability que SÍ se ejecutó.
     *
     * Firma real verificada en mcp-adapter 0.6.1:
     *   apply_filters('mcp_adapter_tool_call_result', $result, $args, $tool_name, $mcp_tool, $server)
     * Tomamos solo los tres primeros; los otros dos son objetos que no queremos retener.
     *
     * @param mixed  $result    Resultado crudo, puede ser WP_Error.
     * @param array  $args      Argumentos con los que se ejecutó.
     * @param string $tool_name Nombre de la tool.
     * @return mixed El $result intacto. SIEMPRE.
     */
    public static function log_tool_result($result, $args = [], $tool_name = '') {
        try {
            $is_error = is_wp_error($result);

            self::write([
                'ability' => self::resolve_ability($tool_name, $args),
                'tool'    => (string) $tool_name,
                'status'  => $is_error ? 'error' : 'ok',
                'error'   => $is_error ? $result->get_error_message() : '',
                'args'    => self::summarize_args($args),
            ]);

            ++self::$logged_in_request;
        } catch (\Throwable $e) {
            self::fail_quietly($e);
        }

        return $result;
    }

    /**
     * Registra las llamadas que el adapter cortó ANTES de ejecutar.
     *
     * Son las que no pasan por el filtro anterior: tool inexistente, permisos
     * denegados, o el request entero rechazado por autenticación. En una auditoría
     * de seguridad son las filas más valiosas y hoy no las guarda nadie.
     *
     * @param \WP_HTTP_Response $response Respuesta ya resuelta.
     * @param \WP_REST_Server   $server   Instancia del servidor.
     * @param \WP_REST_Request  $request  Petición original.
     * @return \WP_HTTP_Response El $response intacto. SIEMPRE.
     */
    public static function log_rejected($response, $server = null, $request = null) {
        try {
            // Este filtro corre 3+ veces por request HTTP (dispatch principal, cada
            // recurso _embed, cada sub-request de un lote) más la precarga del
            // editor. Sin este filtro por ruta inundaríamos la tabla.
            if (!$request instanceof \WP_REST_Request) {
                return $response;
            }

            $route = (string) $request->get_route();
            if (strpos($route, self::MCP_ROUTE_PREFIX) !== 0) {
                return $response;
            }

            $request_id = spl_object_id($request);
            if (self::$finalized_request === $request_id) {
                return $response;
            }

            $requested = self::count_tool_calls($request);
            $missing   = $requested - self::$logged_in_request;

            if ($missing > 0) {
                $status = self::response_status($response);
                self::write([
                    'ability' => '',
                    'tool'    => '',
                    'status'  => 'blocked',
                    'error'   => 'Rechazado antes de ejecutar (HTTP ' . $status . ')',
                    'args'    => ['blocked_calls' => $missing, 'route' => $route],
                ]);
            }

            self::$finalized_request = $request_id;
            self::$logged_in_request = 0;
        } catch (\Throwable $e) {
            self::fail_quietly($e);
        }

        return $response;
    }

    /**
     * Escribe una fila.
     *
     * Todo lo que identifica al actor se guarda como meta para poder filtrar; el
     * detalle va en post_content porque no se consulta, se lee.
     */
    private static function write(array $entry) {
        if (!post_type_exists(self::CPT)) {
            return;
        }

        $user  = function_exists('wp_get_current_user') ? wp_get_current_user() : null;
        $label = $entry['ability'] !== '' ? $entry['ability'] : ($entry['tool'] !== '' ? $entry['tool'] : 'mcp');

        $post_id = wp_insert_post([
            'post_type'    => self::CPT,
            'post_status'  => 'private',
            'post_title'   => $label . ' · ' . $entry['status'],
            'post_content' => wp_json_encode($entry['args'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ], true);

        if (is_wp_error($post_id) || !$post_id) {
            return;
        }

        $meta = [
            '_nandark_ability' => $entry['ability'],
            '_nandark_tool'    => $entry['tool'],
            '_nandark_status'  => $entry['status'],
            '_nandark_error'   => mb_substr((string) $entry['error'], 0, 500),
            '_nandark_user_id' => $user ? (int) $user->ID : 0,
            '_nandark_user'    => $user && $user->ID ? $user->user_login : 'anon',
            '_nandark_ip'      => self::client_ip(),
            '_nandark_ua'      => mb_substr(self::server_value('HTTP_USER_AGENT'), 0, 255),
        ];

        foreach ($meta as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }

    /**
     * El nombre real de la ability no está en $tool_name.
     *
     * Los servidores MCP exponen solo 3 tools, así que $tool_name es casi siempre
     * "mcp-adapter/execute-ability" y la ability concreta viaja en los argumentos.
     * Loguear $tool_name a secas daría una tabla entera diciendo lo mismo.
     */
    private static function resolve_ability($tool_name, $args) {
        if (is_array($args) && !empty($args['ability_name']) && is_string($args['ability_name'])) {
            return $args['ability_name'];
        }
        return is_string($tool_name) ? $tool_name : '';
    }

    /**
     * Recorta los argumentos y saca lo que no debe quedar escrito.
     *
     * El tope de bytes es real: sin él, una llamada que sube contenido largo
     * escribiría ese contenido entero en la tabla, por cada llamada.
     */
    private static function summarize_args($args) {
        if (!is_array($args)) {
            return [];
        }

        $blocked = ['authorization', 'token', 'password', 'secret', 'api_key', 'apikey'];
        $clean   = [];

        foreach ($args as $key => $value) {
            if (in_array(strtolower((string) $key), $blocked, true)) {
                $clean[$key] = '[redactado]';
                continue;
            }
            $clean[$key] = $value;
        }

        $encoded = wp_json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (is_string($encoded) && strlen($encoded) > self::MAX_ARGS_BYTES) {
            return [
                'truncated'      => true,
                'original_bytes' => strlen($encoded),
                'preview'        => mb_strcut($encoded, 0, self::MAX_ARGS_BYTES),
            ];
        }

        return $clean;
    }

    /**
     * Cuenta cuántas tool calls pidió este POST.
     *
     * El transporte MCP acepta lotes JSON-RPC: un solo POST puede traer N llamadas.
     * Sin contar el lote, una tanda rechazada de 10 quedaría como una sola línea.
     */
    private static function count_tool_calls(\WP_REST_Request $request) {
        $body = $request->get_json_params();
        if (!is_array($body)) {
            return 0;
        }

        // Un lote llega como lista; una llamada suelta, como objeto.
        $calls = isset($body['jsonrpc']) ? [$body] : $body;
        $count = 0;

        foreach ($calls as $call) {
            if (is_array($call) && isset($call['method']) && $call['method'] === 'tools/call') {
                ++$count;
            }
        }

        return $count;
    }

    private static function response_status($response) {
        if (is_object($response) && method_exists($response, 'get_status')) {
            return (int) $response->get_status();
        }
        return 0;
    }

    /**
     * IP del cliente sin confiar en cabeceras que cualquiera puede falsificar.
     *
     * REMOTE_ADDR es la única que no se puede inventar desde afuera. Si algún día
     * hay un CDN adelante habrá que leer X-Forwarded-For, pero solo entonces y solo
     * confiando en el proxy: hacerlo ahora sería dejar que el atacante elija qué IP
     * queda escrita en la auditoría.
     */
    private static function client_ip() {
        $ip = self::server_value('REMOTE_ADDR');
        return $ip !== '' ? mb_substr($ip, 0, 45) : 'unknown';
    }

    private static function server_value($key) {
        return isset($_SERVER[$key]) && is_string($_SERVER[$key])
            ? sanitize_text_field(wp_unslash($_SERVER[$key]))
            : '';
    }

    /**
     * Un fallo del auditor no puede ser un fallo del sitio.
     */
    private static function fail_quietly(\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Nandark Agent Audit: ' . $e->getMessage());
        }
    }

    public static function schedule_purge() {
        if (!wp_next_scheduled(self::CRON_HOOK)) {
            wp_schedule_event(time() + HOUR_IN_SECONDS, 'daily', self::CRON_HOOK);
        }
    }

    /**
     * Borra en tandas acotadas.
     *
     * Nunca 'posts_per_page' => -1: WP no le pone LIMIT a esa consulta y en un
     * hosting compartido con 30s de max_execution_time es un timeout garantizado.
     * 'no_found_rows' evita el SQL_CALC_FOUND_ROWS que WP agrega por defecto y que
     * obliga a MySQL a materializar todo el resultado solo para contarlo.
     */
    public static function purge_old_entries() {
        $cutoff = gmdate('Y-m-d H:i:s', time() - (self::RETENTION_DAYS * DAY_IN_SECONDS));

        $old = get_posts([
            'post_type'      => self::CPT,
            'post_status'    => 'private',
            'posts_per_page' => 200,
            'no_found_rows'  => true,
            'fields'         => 'ids',
            'date_query'     => [
                ['column' => 'post_date_gmt', 'before' => $cutoff],
            ],
        ]);

        foreach ($old as $id) {
            wp_delete_post($id, true);
        }
    }
}
