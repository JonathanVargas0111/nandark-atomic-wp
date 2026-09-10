<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Las acciones que un agente puede PROPONER, y las abilities para proponerlas.
 *
 * Este archivo es la lista blanca. Si una acción no está acá, no se puede proponer y
 * no se puede aprobar: no existe. Ése es el punto — un agente no elige qué ejecutar,
 * elige de un menú corto que escribimos nosotros.
 *
 * Hoy hay una sola acción, a propósito. Es la que convierte a la barrera en algo
 * demostrable en vez de una pantalla vacía, y es también el mejor ejemplo de la
 * regla que gobierna todas las que vengan:
 *
 *     el agente aporta la INTENCIÓN, nunca el destino
 *
 * Para instalar un plugin, el agente manda un slug del directorio de WordPress. No
 * manda una URL. La dirección real de descarga se la pedimos a wordpress.org y esa
 * es la que se usa. Si aceptáramos una URL, "instalar un plugin" sería "ejecutá este
 * ZIP que yo elijo", y toda la barrera no serviría para nada.
 */
class Gated_Actions {

    const CATEGORY = 'nandark';

    public static function init() {
        self::register_install_plugin();

        add_action('wp_abilities_api_categories_init', [__CLASS__, 'register_category']);
        add_action('wp_abilities_api_init', [__CLASS__, 'register_abilities']);
    }

    public static function register_category() {
        if (!function_exists('wp_register_ability_category') || wp_has_ability_category(self::CATEGORY)) {
            return;
        }

        wp_register_ability_category(self::CATEGORY, [
            'label'       => 'Nandark',
            'description' => 'Acciones de agente sobre este sitio.',
        ]);
    }

    /* ------------------------------------------------- acción: instalar plugin */

    private static function register_install_plugin() {
        Human_Gate::register_action('install-plugin', [
            'label' => 'Instalar un plugin del directorio de WordPress',

            // Quien aprueba tiene que poder hacerlo a mano. No alcanza con ser admin
            // "de algo": aprobar una instalación exige la capacidad de instalar.
            'capability' => 'install_plugins',

            'validate' => [__CLASS__, 'validate_install_plugin'],
            'preview'  => [__CLASS__, 'preview_install_plugin'],
            'execute'  => [__CLASS__, 'execute_install_plugin'],
        ]);
    }

    /**
     * Sólo un slug del directorio oficial. Nunca una URL, nunca una ruta.
     *
     * El formato se valida con una expresión estricta antes de tocar la red, y
     * después se confirma contra wordpress.org que el plugin exista de verdad. Los
     * dos pasos hacen falta: el primero evita que el valor viaje a ningún lado, el
     * segundo evita aprobar la instalación de algo que no está.
     */
    public static function validate_install_plugin($params) {
        $slug = isset($params['slug']) ? strtolower(trim((string) $params['slug'])) : '';

        if ($slug === '' || !preg_match('/^[a-z0-9][a-z0-9-]{0,61}$/', $slug)) {
            return new \WP_Error(
                'nandark_slug_invalido',
                'El slug tiene que ser el del directorio de WordPress (ej: "wordpress-seo"). '
                . 'No se aceptan URLs ni rutas de archivo.',
                ['status' => 400]
            );
        }

        $info = self::lookup($slug);
        if (is_wp_error($info)) {
            return $info;
        }

        if (self::already_installed($slug)) {
            return new \WP_Error(
                'nandark_plugin_ya_instalado',
                sprintf('El plugin "%s" ya está instalado.', $slug),
                ['status' => 409]
            );
        }

        // Se guarda sólo el slug. Todo lo demás se vuelve a resolver contra
        // wordpress.org al mostrar y al ejecutar, así el humano nunca lee un dato
        // que puso el agente: lee el que devuelve el directorio oficial.
        return ['slug' => $slug];
    }

    public static function preview_install_plugin($params) {
        $slug = (string) ($params['slug'] ?? '');
        $info = self::lookup($slug);

        if (is_wp_error($info)) {
            return ['slug' => $slug, 'error' => $info->get_error_message()];
        }

        return [
            'Plugin'              => $info->name ?? $slug,
            'Slug'                => $slug,
            'Versión'             => $info->version ?? '?',
            'Autor'               => isset($info->author) ? wp_strip_all_tags($info->author) : '?',
            'Instalaciones'       => isset($info->active_installs) ? number_format((int) $info->active_installs) : '?',
            'Última actualización' => $info->last_updated ?? '?',
            'Se descargará de'    => $info->download_link ?? '?',
            'Se activará'         => 'No. Queda instalado y desactivado.',
        ];
    }

    /**
     * Instala. No activa.
     *
     * Dejarlo desactivado es deliberado y copia el patrón que ya usa la ability de
     * snippets del ecosistema: el código llega al disco pero no corre hasta que una
     * persona lo enciende. Son dos decisiones distintas y merecen dos clics distintos.
     */
    public static function execute_install_plugin($params) {
        $slug = (string) ($params['slug'] ?? '');

        $info = self::lookup($slug);
        if (is_wp_error($info)) {
            return $info;
        }

        if (empty($info->download_link)) {
            return new \WP_Error('nandark_sin_descarga', 'wordpress.org no devolvió un enlace de descarga.');
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $upgrader = new \Plugin_Upgrader(new \Automatic_Upgrader_Skin());
        $ok       = $upgrader->install($info->download_link);

        if (is_wp_error($ok)) {
            return $ok;
        }

        if ($ok !== true) {
            return new \WP_Error(
                'nandark_instalacion_fallida',
                'La instalación no terminó bien. Revisá wp-admin ▸ Plugins.'
            );
        }

        return sprintf('Instalado %s %s (desactivado).', $info->name ?? $slug, $info->version ?? '');
    }

    /** Consulta el directorio oficial. Cacheada: la pantalla la llama al dibujar. */
    private static function lookup($slug) {
        $cached = get_transient('nandark_plugin_info_' . $slug);
        if (is_object($cached)) {
            return $cached;
        }

        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

        $info = plugins_api('plugin_information', [
            'slug'   => $slug,
            'fields' => ['sections' => false, 'short_description' => true],
        ]);

        if (is_wp_error($info)) {
            return new \WP_Error(
                'nandark_plugin_no_encontrado',
                sprintf('wordpress.org no conoce el plugin "%s": %s', $slug, $info->get_error_message()),
                ['status' => 404]
            );
        }

        set_transient('nandark_plugin_info_' . $slug, $info, HOUR_IN_SECONDS);

        return $info;
    }

    private static function already_installed($slug) {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        foreach (array_keys(get_plugins()) as $file) {
            if (strpos($file, $slug . '/') === 0 || $file === $slug . '.php') {
                return true;
            }
        }

        return false;
    }

    /* ------------------------------------------------------------- abilities */

    public static function register_abilities() {
        if (!function_exists('wp_register_ability')) {
            return;
        }

        wp_register_ability('nandark/propose-action', [
            'label'       => 'Proponer una acción para aprobación humana',
            'description' => 'Deja una acción en la cola de aprobación. NO la ejecuta: una persona '
                           . 'tiene que aprobarla desde wp-admin. Devuelve el id de la propuesta '
                           . 'para consultar después con nandark/action-status.',
            'category'    => self::CATEGORY,

            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'type' => [
                        'type'        => 'string',
                        'description' => 'Tipo de acción. Sólo se aceptan las de la lista blanca.',
                    ],
                    'params' => [
                        'type'        => 'object',
                        'description' => 'Parámetros de la acción. Para install-plugin: {"slug":"wordpress-seo"}.',
                    ],
                    'reason' => [
                        'type'        => 'string',
                        'description' => 'Por qué se propone. Lo lee la persona que decide, así que escribilo para ella.',
                    ],
                ],
                'required'             => ['type'],
                'additionalProperties' => false,
                'default'              => [],
            ],

            'output_schema' => [
                'type'       => 'object',
                'properties' => [
                    'proposal_id' => ['type' => 'integer'],
                    'status'      => ['type' => 'string'],
                    'expires_at'  => ['type' => 'string'],
                    'preview'     => ['type' => 'object'],
                    'message'     => ['type' => 'string'],
                ],
                'required'   => ['proposal_id', 'status'],
            ],

            'execute_callback' => function ($input = []) {
                $input = is_array($input) ? $input : [];
                return Human_Gate::propose(
                    $input['type'] ?? '',
                    isset($input['params']) && is_array($input['params']) ? $input['params'] : [],
                    $input['reason'] ?? ''
                );
            },

            // Proponer no ejecuta nada, así que NO exige la capacidad de la acción.
            // Ése es el sentido de la barrera: el agente propone justamente aquello
            // que no puede hacer solo.
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },

            'meta' => [
                // No es readonly: escribe una fila en la cola. Pero tampoco es
                // destructiva — no toca nada del sitio.
                'annotations' => ['readonly' => false, 'destructive' => false, 'idempotent' => false],
                'public'      => true,
                'mcp'         => ['public' => true, 'type' => 'tool'],
            ],
        ]);

        wp_register_ability('nandark/action-status', [
            'label'       => 'Ver en qué quedó una propuesta',
            'description' => 'Devuelve el estado de una propuesta: pendiente, aprobada, ejecutada, '
                           . 'rechazada, vencida o fallida, con su resultado.',
            'category'    => self::CATEGORY,

            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'proposal_id' => ['type' => 'integer', 'description' => 'Id devuelto por propose-action.'],
                ],
                'required'             => ['proposal_id'],
                'additionalProperties' => false,
                'default'              => [],
            ],

            'output_schema' => [
                'type'       => 'object',
                'properties' => [
                    'proposal_id' => ['type' => 'integer'],
                    'status'      => ['type' => 'string'],
                    'result'      => ['type' => 'string'],
                ],
                'required'   => ['proposal_id', 'status'],
            ],

            'execute_callback' => function ($input = []) {
                return Human_Gate::status(is_array($input) ? ($input['proposal_id'] ?? 0) : 0);
            },

            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },

            'meta' => [
                'annotations' => ['readonly' => true, 'destructive' => false, 'idempotent' => true],
                'public'      => true,
                'mcp'         => ['public' => true, 'type' => 'tool'],
            ],
        ]);
    }
}
