<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Auditoría de contenido de SOLO LECTURA, expuesta como ability para agentes de IA.
 *
 * QUÉ RESPONDE
 * Lo que WordPress realmente sabe de su propio contenido: qué le falta imagen
 * destacada, qué no se toca hace meses, qué borradores se pudrieron, qué comentarios
 * esperan moderación, y qué páginas no tienen ni un enlace entrante.
 *
 * QUÉ NO RESPONDE, Y ES A PROPÓSITO
 * Tráfico. **WordPress no guarda visitas**: no existe esa tabla. Cualquier función
 * acá que dijera "las páginas menos visitadas" estaría inventando. Esa pregunta se
 * le hace a Search Console, no a la base de datos.
 *
 * POR QUÉ NO HAY ESTADO PERSISTENTE
 * La versión anterior de este módulo guardaba el grafo de enlaces en una option para
 * poder construirlo por tandas. Esa decisión, sola, generó los cuatro defectos más
 * graves que encontró la revisión:
 *
 *   - el cursor nunca avanzaba con los argumentos por defecto
 *   - reentrar con el grafo terminado era un fatal
 *   - el estado no distinguía por tipo de contenido: pedir "posts" y después
 *     "páginas" devolvía huérfanas falsas, sin ningún error
 *   - un solo parámetro en cero dejaba el grafo marcado como completo y vacío,
 *     y a partir de ahí el sitio ENTERO figuraba como huérfano, para todos
 *
 * Ese último es el que decide el diseño. Un informe de auditoría equivocado no es un
 * bug cosmético: es la entrada de un agente que después actúa sobre él. Un grafo
 * envenenado le dice "todo esto está huérfano" y el agente limpia el sitio.
 *
 * Así que este módulo **no guarda nada**. Cada consulta se calcula entera o se niega
 * a correr. Si el sitio es más grande de lo que entra en una pasada, devuelve un
 * error que lo dice. Negarse es una respuesta correcta; contestar mal, no.
 *
 * TOPES
 * Todo parámetro numérico está acotado por arriba y por abajo. Todo array vacío es un
 * error explícito y no una consulta rota que devuelve vacío. En un shared hosting con
 * 128 MB, una consulta sin techo no da un error legible: da un proceso muerto.
 */
class Content_Audit {

    const CATEGORY = 'nandark';
    const ABILITY  = 'nandark/content-audit';

    /** Filas por página. Nunca sin techo. */
    const MAX_LIMIT     = 200;
    const DEFAULT_LIMIT = 50;

    /**
     * Presupuesto de contenido que el análisis de huérfanas se anima a leer, en bytes.
     *
     * El límite va en BYTES y no en cantidad de posts, porque el costo está en el
     * contenido: 4.000 posts de Elementor pesan más que 40.000 notas cortas. La
     * medición que originó este número mostró 204 MB de memoria con menos de 5.000
     * posts, justamente por eso.
     */
    const ORPHAN_MAX_BYTES = 12582912; // 12 MB

    /** Cuántas filas se traen por tanda al recorrer contenido. */
    const SCAN_BATCH = 100;

    const CACHE_TTL = 3600;

    public static function init() {
        // Los dos hooks van juntos y en este orden conceptual: la categoría tiene que
        // existir antes que cualquier ability que la nombre, o el registro se descarta
        // en silencio. Core dispara categories_init antes que api_init, así que
        // engancharse a los dos alcanza.
        add_action('wp_abilities_api_categories_init', [__CLASS__, 'register_category']);
        add_action('wp_abilities_api_init', [__CLASS__, 'register_ability']);
    }

    public static function register_category() {
        if (!function_exists('wp_register_ability_category') || wp_has_ability_category(self::CATEGORY)) {
            return;
        }

        wp_register_ability_category(self::CATEGORY, [
            'label'       => 'Nandark',
            'description' => 'Diagnóstico de solo lectura sobre el contenido de este sitio.',
        ]);
    }

    public static function register_ability() {
        if (!function_exists('wp_register_ability')) {
            return;
        }

        wp_register_ability(self::ABILITY, [
            'label'       => 'Auditoría de contenido',
            'description' => 'Revisa el contenido del sitio y devuelve qué le falta: imagen destacada, '
                           . 'metadatos obligatorios, contenido sin actualizar, borradores viejos, '
                           . 'comentarios sin moderar y páginas sin enlaces entrantes. Solo lectura: '
                           . 'no modifica nada. No incluye datos de tráfico porque WordPress no los guarda.',
            'category'    => self::CATEGORY,

            'input_schema' => [
                'type'       => 'object',
                'properties' => [
                    'check' => [
                        'type'        => 'string',
                        'enum'        => ['resumen', 'sin-meta', 'sin-actualizar', 'borradores', 'comentarios', 'huerfanas'],
                        'description' => 'Qué auditar. "resumen" corre todas con límites chicos.',
                        'default'     => 'resumen',
                    ],
                    'post_types' => [
                        'type'        => 'array',
                        'items'       => ['type' => 'string'],
                        'description' => 'Tipos de contenido a revisar. Por defecto, los públicos del sitio.',
                    ],
                    'required_meta' => [
                        'type'        => 'array',
                        'items'       => ['type' => 'string'],
                        'description' => 'Claves de metadatos obligatorias. Por defecto _thumbnail_id.',
                    ],
                    'months' => [
                        'type'        => 'integer',
                        'description' => 'Meses sin actualizar a partir de los cuales el contenido se considera viejo.',
                        'default'     => 12,
                    ],
                    'days' => [
                        'type'        => 'integer',
                        'description' => 'Días de antigüedad a partir de los cuales un borrador se considera podrido.',
                        'default'     => 90,
                    ],
                    'limit' => [
                        'type'        => 'integer',
                        'description' => 'Máximo de filas por auditoría (tope duro: 200).',
                        'default'     => self::DEFAULT_LIMIT,
                    ],
                    'after_id' => [
                        'type'        => 'integer',
                        'description' => 'Paginación: devuelve resultados con ID mayor a éste.',
                        'default'     => 0,
                    ],
                ],
                'additionalProperties' => false,
                'default'              => [],
            ],

            'output_schema' => [
                'type'       => 'object',
                'properties' => [
                    'check'   => ['type' => 'string'],
                    'results' => ['type' => 'object'],
                ],
                'required'   => ['check', 'results'],
            ],

            'execute_callback'    => [__CLASS__, 'execute'],
            'permission_callback' => [__CLASS__, 'check_permission'],

            /*
             * meta.mcp.public gana sobre meta.public cuando los dos están. Se declaran
             * los dos a propósito: el adapter que corre en este sitio puede ser el
             * plugin 0.6.1 o la copia 0.5.0 empaquetada dentro del plugin de abilities,
             * y las dos versiones no leen la misma clave. Declarar ambas cuesta dos
             * líneas y evita que la ability quede invisible según qué se instaló.
             */
            'meta' => [
                'annotations' => [
                    'readonly'    => true,
                    'destructive' => false,
                    'idempotent'  => true,
                ],
                'public' => true,
                'mcp'    => ['public' => true, 'type' => 'tool'],
            ],
        ]);
    }

    /**
     * Devolver inventario de contenido no es leer contenido público.
     *
     * `edit_posts` es el piso correcto: es la capability que separa a alguien que
     * puede ver el sitio de alguien que trabaja sobre él. Un suscriptor no tiene por
     * qué saber cuántos borradores hay.
     */
    public static function check_permission() {
        return current_user_can('edit_posts');
    }

    public static function execute($input = []) {
        $input = is_array($input) ? $input : [];

        $check      = isset($input['check']) ? (string) $input['check'] : 'resumen';
        $post_types = self::sanitize_post_types($input['post_types'] ?? null);
        if (is_wp_error($post_types)) {
            return $post_types;
        }

        $limit    = self::clamp($input['limit'] ?? self::DEFAULT_LIMIT, 1, self::MAX_LIMIT);
        $after_id = max(0, (int) ($input['after_id'] ?? 0));
        $months   = self::clamp($input['months'] ?? 12, 1, 1200);
        $days     = self::clamp($input['days'] ?? 90, 1, 36500);

        $meta = self::sanitize_meta_keys($input['required_meta'] ?? null);
        if (is_wp_error($meta)) {
            return $meta;
        }

        switch ($check) {
            case 'sin-meta':
                $results = self::missing_meta($post_types, $meta, $after_id, $limit);
                break;
            case 'sin-actualizar':
                $results = self::stale($post_types, $months, $after_id, $limit);
                break;
            case 'borradores':
                $results = self::old_drafts($post_types, $days, $after_id, $limit);
                break;
            case 'comentarios':
                $results = self::pending_comments($limit);
                break;
            case 'huerfanas':
                $results = self::orphans($post_types, $limit);
                break;
            case 'resumen':
                $small   = min(10, $limit);
                $results = [
                    'sin_meta'       => self::missing_meta($post_types, $meta, 0, $small),
                    'sin_actualizar' => self::stale($post_types, $months, 0, $small),
                    'borradores'     => self::old_drafts($post_types, $days, 0, $small),
                    'comentarios'    => self::pending_comments($small),
                    'huerfanas'      => self::orphans($post_types, $small),
                ];
                break;
            default:
                return new \WP_Error(
                    'nandark_audit_check_invalido',
                    'Auditoría desconocida: ' . $check,
                    ['status' => 400]
                );
        }

        if (is_wp_error($results)) {
            return $results;
        }

        return ['check' => $check, 'results' => $results];
    }

    /* ------------------------------------------------------------- auditorías */

    /**
     * Contenido publicado al que le falta alguna clave de metadatos.
     *
     * Dos consultas acotadas en vez de una `meta_query` con NOT EXISTS. El motivo no
     * es el JOIN sino que WordPress agrega un `GROUP BY wp_posts.ID` ante cualquier
     * meta_query, y eso fuerza tabla temporal más filesort. Acá la primera consulta
     * recorre wp_posts por ID y la segunda pregunta por los metadatos de esa página
     * de una sola vez.
     */
    public static function missing_meta(array $post_types, array $required, $after_id, $limit) {
        global $wpdb;

        $ids = self::published_ids($post_types, $after_id, $limit);
        if (empty($ids)) {
            return ['items' => [], 'next_after_id' => null];
        }

        $ph_ids  = implode(',', array_fill(0, count($ids), '%d'));
        $ph_keys = implode(',', array_fill(0, count($required), '%s'));

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT post_id, meta_key FROM {$wpdb->postmeta}
                 WHERE post_id IN ($ph_ids) AND meta_key IN ($ph_keys)
                   AND meta_value <> '' AND meta_value <> '0'",
                array_merge($ids, $required)
            ),
            ARRAY_A
        );

        $present = [];
        foreach ((array) $rows as $r) {
            $present[(int) $r['post_id']][$r['meta_key']] = true;
        }

        $items = [];
        foreach ($ids as $id) {
            $missing = [];
            foreach ($required as $key) {
                if (empty($present[$id][$key])) {
                    $missing[] = $key;
                }
            }
            if ($missing) {
                $items[] = ['id' => $id, 'titulo' => get_the_title($id), 'falta' => $missing];
            }
        }

        return ['items' => $items, 'next_after_id' => end($ids)];
    }

    /** Contenido publicado que no se modifica desde hace N meses. */
    public static function stale(array $post_types, $months, $after_id, $limit) {
        global $wpdb;

        $cutoff = gmdate('Y-m-d H:i:s', time() - ($months * MONTH_IN_SECONDS));
        $ph     = implode(',', array_fill(0, count($post_types), '%s'));

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title, post_modified_gmt FROM {$wpdb->posts}
                 WHERE post_type IN ($ph) AND post_status = 'publish'
                   AND post_modified_gmt < %s AND ID > %d
                 ORDER BY ID ASC LIMIT %d",
                array_merge($post_types, [$cutoff, $after_id, $limit])
            ),
            ARRAY_A
        );

        return [
            'corte' => $cutoff,
            'items' => array_map(function ($r) {
                return [
                    'id'         => (int) $r['ID'],
                    'titulo'     => $r['post_title'],
                    'modificado' => $r['post_modified_gmt'],
                ];
            }, (array) $rows),
        ];
    }

    /**
     * Borradores más viejos que N días.
     *
     * Se filtra por autor cuando quien pregunta no puede editar lo ajeno. WordPress
     * no le muestra a un colaborador los borradores de otro, y esta ability tampoco
     * debería: una herramienta de lectura no puede ser el atajo que enseñe lo que la
     * interfaz esconde.
     */
    public static function old_drafts(array $post_types, $days, $after_id, $limit) {
        global $wpdb;

        $cutoff = gmdate('Y-m-d H:i:s', time() - ($days * DAY_IN_SECONDS));
        $ph     = implode(',', array_fill(0, count($post_types), '%s'));

        $sql  = "SELECT ID, post_title, post_modified_gmt FROM {$wpdb->posts}
                 WHERE post_type IN ($ph) AND post_status = 'draft'
                   AND post_modified_gmt < %s AND ID > %d";
        $args = array_merge($post_types, [$cutoff, $after_id]);

        if (!current_user_can('edit_others_posts')) {
            $sql   .= ' AND post_author = %d';
            $args[] = get_current_user_id();
        }

        $sql   .= ' ORDER BY ID ASC LIMIT %d';
        $args[] = $limit;

        $rows = $wpdb->get_results($wpdb->prepare($sql, $args), ARRAY_A);

        return [
            'corte' => $cutoff,
            'items' => array_map(function ($r) {
                return [
                    'id'         => (int) $r['ID'],
                    'titulo'     => $r['post_title'],
                    'modificado' => $r['post_modified_gmt'],
                ];
            }, (array) $rows),
        ];
    }

    /** Comentarios esperando moderación. */
    public static function pending_comments($limit) {
        $comments = get_comments([
            'status' => 'hold',
            'number' => $limit,
            'orderby' => 'comment_ID',
            'order'   => 'ASC',
        ]);

        $items = [];
        foreach ((array) $comments as $c) {
            // Ni email ni IP: para decidir qué moderar alcanza con saber dónde y cuándo.
            $items[] = [
                'id'      => (int) $c->comment_ID,
                'post_id' => (int) $c->comment_post_ID,
                'autor'   => $c->comment_author,
                'fecha'   => $c->comment_date_gmt,
            ];
        }

        return ['total' => count($items), 'items' => $items];
    }

    /**
     * Contenido publicado sin un solo enlace entrante desde otro contenido del sitio.
     *
     * Una sola pasada, sin estado, con presupuesto. Si el sitio no entra en el
     * presupuesto, se niega con un error que dice por qué en vez de devolver una
     * respuesta a medias que parece completa.
     */
    public static function orphans(array $post_types, $limit) {
        global $wpdb;

        $ph    = implode(',', array_fill(0, count($post_types), '%s'));
        $scope = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) AS n, COALESCE(SUM(LENGTH(post_content)),0) AS bytes
                 FROM {$wpdb->posts}
                 WHERE post_type IN ($ph) AND post_status = 'publish'",
                $post_types
            ),
            ARRAY_A
        );

        $bytes = isset($scope['bytes']) ? (int) $scope['bytes'] : 0;
        $total = isset($scope['n']) ? (int) $scope['n'] : 0;

        if ($bytes > self::ORPHAN_MAX_BYTES) {
            return new \WP_Error(
                'nandark_audit_demasiado_grande',
                sprintf(
                    'El análisis de páginas huérfanas necesita leer %s MB de contenido y el tope es %s MB. '
                    . 'No se ejecutó: un resultado parcial acá sería peor que ninguno.',
                    number_format($bytes / 1048576, 1),
                    number_format(self::ORPHAN_MAX_BYTES / 1048576, 1)
                ),
                ['status' => 413, 'posts' => $total, 'bytes' => $bytes]
            );
        }

        $home  = home_url();
        $hosts = [strtolower((string) wp_parse_url($home, PHP_URL_HOST))];

        $inbound = [];
        $cursor  = 0;

        while (true) {
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT ID, post_content FROM {$wpdb->posts}
                     WHERE post_type IN ($ph) AND post_status = 'publish' AND ID > %d
                     ORDER BY ID ASC LIMIT %d",
                    array_merge($post_types, [$cursor, self::SCAN_BATCH])
                ),
                ARRAY_A
            );

            if (empty($rows)) {
                break;
            }

            foreach ($rows as $r) {
                $cursor = (int) $r['ID'];
                if (!preg_match_all('#href\s*=\s*[\'"]([^\'"]+)[\'"]#i', $r['post_content'], $m)) {
                    continue;
                }
                foreach ($m[1] as $raw) {
                    $slug = self::resolve_local_slug($raw, $hosts);
                    if ($slug !== null) {
                        $inbound[$slug] = true;
                    }
                }
            }

            unset($rows);
        }

        $candidatos = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title, post_name FROM {$wpdb->posts}
                 WHERE post_type IN ($ph) AND post_status = 'publish'
                 ORDER BY ID ASC LIMIT %d",
                array_merge($post_types, [self::MAX_LIMIT])
            ),
            ARRAY_A
        );

        $items = [];
        foreach ((array) $candidatos as $r) {
            if (count($items) >= $limit) {
                break;
            }
            if (empty($inbound[strtolower($r['post_name'])])) {
                $items[] = ['id' => (int) $r['ID'], 'titulo' => $r['post_title'], 'slug' => $r['post_name']];
            }
        }

        return ['analizados' => $total, 'items' => $items];
    }

    /* ----------------------------------------------------------------- ayudas */

    /**
     * Slug de un enlace que apunta a este sitio, o null.
     *
     * Tres cosas que la versión anterior contaba mal, y que inflaban el resultado
     * hacia el lado peligroso: decían "esto tiene enlaces entrantes" cuando no.
     *
     * 1. Un ancla interna (`href="#introduccion"`) se contaba como enlace entrante a
     *    cualquier página cuyo slug fuera "introduccion". Cualquiera que pueda
     *    publicar podía borrar hallazgos del informe escribiendo un ancla.
     * 2. El fragmento y la query string se tokenizaban después de partir la ruta, así
     *    que `/pagina?utm=x` y `/pagina#seccion` no coincidían con `/pagina`.
     * 3. La comparación de dominio era sensible a mayúsculas.
     */
    private static function resolve_local_slug($raw, array $hosts) {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        // Un ancla pura no navega a ningún lado: no es un enlace entrante.
        if ($raw[0] === '#') {
            return null;
        }

        foreach (['#', '?'] as $sep) {
            $pos = strpos($raw, $sep);
            if ($pos !== false) {
                $raw = substr($raw, 0, $pos);
            }
        }

        $parsed = wp_parse_url($raw);
        if ($parsed === false) {
            return null;
        }

        if (!empty($parsed['host'])) {
            $host = strtolower(rtrim($parsed['host'], '.'));
            if (!in_array($host, $hosts, true)) {
                return null;
            }
        }

        $path = isset($parsed['path']) ? trim($parsed['path'], '/') : '';
        if ($path === '') {
            return null;
        }

        $parts = explode('/', $path);
        $slug  = end($parts);

        return $slug !== '' ? strtolower($slug) : null;
    }

    /** IDs publicados de una página, ordenados por ID. */
    private static function published_ids(array $post_types, $after_id, $limit) {
        global $wpdb;

        $ph  = implode(',', array_fill(0, count($post_types), '%s'));
        $ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts}
                 WHERE post_type IN ($ph) AND post_status = 'publish' AND ID > %d
                 ORDER BY ID ASC LIMIT %d",
                array_merge($post_types, [$after_id, $limit])
            )
        );

        return array_map('intval', (array) $ids);
    }

    /**
     * Un array vacío arma `IN ()`, que es un error de sintaxis SQL.
     *
     * Con WP_DEBUG apagado —o sea, en producción— esa consulta rota devuelve vacío sin
     * decir nada, y vacío se lee como "no hay nada que arreglar". El informe queda
     * verde por estar roto. Por eso acá se devuelve un error explícito.
     */
    private static function sanitize_post_types($input) {
        if ($input === null || $input === []) {
            $input = get_post_types(['public' => true], 'names');
            unset($input['attachment']);
        }

        if (!is_array($input)) {
            return new \WP_Error('nandark_audit_post_types', 'post_types tiene que ser una lista.', ['status' => 400]);
        }

        $clean = [];
        foreach ($input as $type) {
            $type = sanitize_key((string) $type);
            if ($type !== '' && post_type_exists($type)) {
                $clean[] = $type;
            }
        }

        $clean = array_values(array_unique($clean));

        if (empty($clean)) {
            return new \WP_Error(
                'nandark_audit_post_types',
                'Ningún tipo de contenido válido. Tipos registrados: ' . implode(', ', get_post_types([], 'names')),
                ['status' => 400]
            );
        }

        return $clean;
    }

    private static function sanitize_meta_keys($input) {
        if ($input === null || $input === []) {
            return ['_thumbnail_id'];
        }

        if (!is_array($input)) {
            return new \WP_Error('nandark_audit_meta', 'required_meta tiene que ser una lista.', ['status' => 400]);
        }

        $clean = [];
        foreach ($input as $key) {
            $key = trim((string) $key);
            if ($key !== '') {
                $clean[] = $key;
            }
        }

        $clean = array_values(array_unique(array_slice($clean, 0, 20)));

        return empty($clean)
            ? new \WP_Error('nandark_audit_meta', 'required_meta quedó vacío.', ['status' => 400])
            : $clean;
    }

    /** Acota por arriba y por abajo. Un tope solo de un lado no es un tope. */
    private static function clamp($value, $min, $max) {
        $value = is_numeric($value) ? (int) $value : $min;
        return max($min, min($max, $value));
    }
}
