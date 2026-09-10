<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Barrera de aprobación humana para acciones peligrosas de un agente de IA.
 *
 * EL FLUJO
 *
 *     agente  ->  nandark/propose-action        NO ejecuta nada: propone
 *                     v
 *                 se guarda como propuesta pendiente
 *                     v
 *     humano  ->  wp-admin > Acciones pendientes
 *                 ve QUÉ se va a hacer, y aprieta Aprobar
 *                     v
 *     server  ->  ejecuta desde los parámetros GUARDADOS
 *
 * EL AGUJERO QUE ESTE DISEÑO EVITA, Y QUE MI PRIMER BOCETO TENÍA
 *
 * El boceto original decía: la propuesta lleva "una ability y sus parámetros", y al
 * aprobar se ejecuta esa ability. Eso convierte la barrera en su propio contrario.
 *
 * Si un agente puede proponer CUALQUIER ability, y la aprobación la ejecuta del lado
 * del servidor, entonces la cola es un lavadero de privilegios: el agente propone
 * justo aquello que su rol le prohíbe, un humano aprieta Aprobar, y la acción corre
 * igual. Le pusimos una tranquera al agente y le dejamos la llave al lado.
 *
 * Por eso acá **no se proponen abilities: se proponen acciones de una lista blanca**.
 * Cada tipo de acción se registra con su validador, su vista previa y su ejecutor, y
 * sólo esos tipos existen. Una lista blanca de verbos se puede auditar. Un "ejecutá
 * lo que te aprueben" no.
 *
 * LAS OTRAS TRES REGLAS QUE NO SE NEGOCIAN
 *
 * 1. La ejecución lee los parámetros GUARDADOS, nunca los de la petición. Si no,
 *    el agente cambia los parámetros entre el "aprobar" y el "ejecutar" y el humano
 *    termina firmando algo que no leyó. Se guarda un hash y se valida antes de correr.
 *
 * 2. La vista previa se genera desde los parámetros guardados, en el momento de
 *    mostrarla. Nunca se guarda un texto aparte: un resumen almacenado por separado
 *    puede decir una cosa mientras los parámetros dicen otra, y entonces el humano
 *    aprueba mirando la etiqueta equivocada.
 *
 * 3. Quien aprueba necesita la capability de la acción, no una genérica. Aprobar la
 *    instalación de un plugin exige `install_plugins`. Un editor no puede autorizar
 *    lo que él mismo no podría hacer a mano.
 */
class Human_Gate {

    const CPT       = 'nandark_action';
    const NONCE     = 'nandark_gate_action';
    const MENU_SLUG = 'nandark-acciones';

    /** Vida de una propuesta sin decidir. */
    const TTL = DAY_IN_SECONDS;

    /** Tope de propuestas pendientes. Un agente en loop no puede llenar la tabla. */
    const MAX_PENDING = 50;

    const S_PENDING  = 'pending';
    const S_APPROVED = 'approved';
    const S_REJECTED = 'rejected';
    const S_EXECUTED = 'executed';
    const S_FAILED   = 'failed';
    const S_EXPIRED  = 'expired';

    /** @var array<string,array> Lista blanca de acciones registradas. */
    private static $actions = [];

    public static function init() {
        add_action('init', [__CLASS__, 'register_cpt']);
        add_action('admin_menu', [__CLASS__, 'register_admin_page']);
        add_action('admin_post_nandark_gate_decide', [__CLASS__, 'handle_decision']);
    }

    /**
     * Registra un tipo de acción aprobable.
     *
     * @param string $type   Identificador corto, sólo minúsculas y guiones.
     * @param array  $config label, capability, validate, preview, execute.
     */
    public static function register_action($type, array $config) {
        foreach (['label', 'capability', 'validate', 'preview', 'execute'] as $key) {
            if (empty($config[$key])) {
                return;
            }
        }
        self::$actions[$type] = $config;
    }

    public static function registered_types() {
        return array_keys(self::$actions);
    }

    public static function get_action($type) {
        return self::$actions[$type] ?? null;
    }

    public static function register_cpt() {
        register_post_type(self::CPT, [
            'labels' => [
                'name'          => 'Acciones pendientes',
                'singular_name' => 'Acción propuesta',
            ],
            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            // La lista se dibuja a mano: la pantalla estándar de WordPress dejaría
            // editar los parámetros de una propuesta ya aprobada.
            'show_ui'             => false,
            // Los parámetros pueden traer contenido del cliente. No salen por la API.
            'show_in_rest'        => false,
            'rewrite'             => false,
            'query_var'           => false,
            'supports'            => ['title'],
        ]);
    }

    /* ------------------------------------------------------------- proponer */

    /**
     * Crea una propuesta. NO ejecuta nada.
     *
     * @return array|\WP_Error
     */
    public static function propose($type, array $params, $reason = '') {
        // Se conserva lo que mando el agente para el mensaje de error. sanitize_key()
        // borra las barras, asi que reportar el valor limpio le diria "ewpadelete-post
        // no existe" a alguien que escribio "ewpa/delete-post": un mensaje que miente
        // sobre lo que se recibio manda a debuguear al lugar equivocado.
        $raw    = (string) $type;
        $type   = sanitize_key($raw);
        $action = self::get_action($type);

        if (!$action) {
            return new \WP_Error(
                'nandark_gate_tipo_desconocido',
                sprintf('Acción "%s" no está en la lista blanca. Disponibles: %s',
                    $raw, implode(', ', self::registered_types()) ?: '(ninguna)'),
                ['status' => 400]
            );
        }

        $valid = call_user_func($action['validate'], $params);
        if (is_wp_error($valid)) {
            return $valid;
        }
        // El validador puede normalizar: se guarda lo normalizado, que es lo que se
        // va a ejecutar y lo que el humano va a ver.
        if (is_array($valid)) {
            $params = $valid;
        }

        if (self::count_pending() >= self::MAX_PENDING) {
            return new \WP_Error(
                'nandark_gate_cola_llena',
                sprintf('Hay %d propuestas sin decidir. Resolvé algunas antes de proponer más.', self::MAX_PENDING),
                ['status' => 429]
            );
        }

        $payload = wp_json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $post_id = wp_insert_post([
            'post_type'    => self::CPT,
            'post_status'  => 'private',
            'post_title'   => $action['label'],
            'post_content' => $payload,
        ], true);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        $expires = time() + self::TTL;

        update_post_meta($post_id, '_nandark_type', $type);
        update_post_meta($post_id, '_nandark_status', self::S_PENDING);
        update_post_meta($post_id, '_nandark_hash', hash('sha256', $payload));
        update_post_meta($post_id, '_nandark_reason', sanitize_textarea_field((string) $reason));
        update_post_meta($post_id, '_nandark_expires', $expires);
        update_post_meta($post_id, '_nandark_proposer', get_current_user_id());

        return [
            'proposal_id' => (int) $post_id,
            'status'      => self::S_PENDING,
            'expires_at'  => gmdate('c', $expires),
            'preview'     => self::preview($post_id),
            'message'     => 'Propuesta creada. NO se ejecutó nada: espera aprobación humana en wp-admin.',
        ];
    }

    /**
     * Estado de una propuesta, para que el agente consulte sin adivinar.
     */
    public static function status($proposal_id) {
        $post = get_post((int) $proposal_id);
        if (!$post || $post->post_type !== self::CPT) {
            return new \WP_Error('nandark_gate_no_existe', 'No existe esa propuesta.', ['status' => 404]);
        }

        $status = self::effective_status($post->ID);

        return [
            'proposal_id' => (int) $post->ID,
            'type'        => get_post_meta($post->ID, '_nandark_type', true),
            'status'      => $status,
            'expires_at'  => gmdate('c', (int) get_post_meta($post->ID, '_nandark_expires', true)),
            'result'      => get_post_meta($post->ID, '_nandark_result', true) ?: null,
            'preview'     => self::preview($post->ID),
        ];
    }

    /**
     * El vencimiento se calcula al leer, no por un cron.
     *
     * Depender de wp-cron para caducar una autorización sería confiar en que alguien
     * visite el sitio. En un sitio con poco tráfico, una propuesta podría quedar viva
     * semanas después de su vencimiento.
     */
    private static function effective_status($post_id) {
        $status = (string) get_post_meta($post_id, '_nandark_status', true);

        if ($status === self::S_PENDING) {
            $expires = (int) get_post_meta($post_id, '_nandark_expires', true);
            if ($expires > 0 && time() > $expires) {
                return self::S_EXPIRED;
            }
        }

        return $status;
    }

    private static function count_pending() {
        $ids = get_posts([
            'post_type'      => self::CPT,
            'post_status'    => 'private',
            'posts_per_page' => self::MAX_PENDING,
            'no_found_rows'  => true,
            'fields'         => 'ids',
            'meta_key'       => '_nandark_status',
            'meta_value'     => self::S_PENDING,
        ]);

        $vivas = 0;
        foreach ($ids as $id) {
            if (self::effective_status($id) === self::S_PENDING) {
                ++$vivas;
            }
        }

        return $vivas;
    }

    /**
     * La vista previa se RENDERIZA desde los parámetros guardados, cada vez.
     *
     * Nunca se guarda un resumen aparte. Si el texto y los parámetros vivieran por
     * separado podrían divergir, y el humano estaría aprobando la etiqueta en vez del
     * contenido — que es exactamente la falla que esta pantalla existe para evitar.
     */
    public static function preview($post_id) {
        $type   = (string) get_post_meta($post_id, '_nandark_type', true);
        $action = self::get_action($type);
        $params = self::stored_params($post_id);

        if (!$action || is_wp_error($params)) {
            return ['error' => 'No se puede mostrar esta propuesta.'];
        }

        return (array) call_user_func($action['preview'], $params);
    }

    /**
     * Lee los parámetros guardados y comprueba que nadie los tocó.
     */
    private static function stored_params($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return new \WP_Error('nandark_gate_no_existe', 'No existe esa propuesta.');
        }

        $payload = (string) $post->post_content;
        $hash    = (string) get_post_meta($post_id, '_nandark_hash', true);

        if ($hash === '' || !hash_equals($hash, hash('sha256', $payload))) {
            return new \WP_Error(
                'nandark_gate_alterada',
                'Los parámetros de esta propuesta no coinciden con los que se guardaron. No se ejecuta.',
                ['status' => 409]
            );
        }

        $params = json_decode($payload, true);

        return is_array($params) ? $params : [];
    }

    /* ---------------------------------------------------------------- admin */

    public static function register_admin_page() {
        add_menu_page(
            'Acciones pendientes',
            'Acciones IA',
            'edit_posts',
            self::MENU_SLUG,
            [__CLASS__, 'render_admin_page'],
            'dashicons-yes-alt',
            79
        );
    }

    public static function render_admin_page() {
        if (!current_user_can('edit_posts')) {
            wp_die('Sin permisos.');
        }

        $ids = get_posts([
            'post_type'      => self::CPT,
            'post_status'    => 'private',
            'posts_per_page' => 100,
            'no_found_rows'  => true,
            'fields'         => 'ids',
            'orderby'        => 'ID',
            'order'          => 'DESC',
        ]);

        echo '<div class="wrap"><h1>Acciones propuestas por el agente</h1>';
        echo '<p>Nada de esto se ejecutó. Cada acción espera que una persona la apruebe.</p>';

        $hay = false;

        foreach ($ids as $id) {
            $status = self::effective_status($id);
            if ($status !== self::S_PENDING) {
                continue;
            }
            $hay = true;
            self::render_proposal($id);
        }

        if (!$hay) {
            echo '<p><em>No hay acciones esperando decisión.</em></p>';
        }

        self::render_history($ids);
        echo '</div>';
    }

    private static function render_proposal($id) {
        $type    = (string) get_post_meta($id, '_nandark_type', true);
        $action  = self::get_action($type);
        $reason  = (string) get_post_meta($id, '_nandark_reason', true);
        $expires = (int) get_post_meta($id, '_nandark_expires', true);
        $preview = self::preview($id);
        $puede   = $action && current_user_can($action['capability']);

        echo '<div class="card" style="max-width:none;margin:1em 0;padding:1em;border-left:4px solid #2271b1">';
        echo '<h2 style="margin-top:0">' . esc_html($action ? $action['label'] : $type) . '</h2>';

        if ($reason !== '') {
            echo '<p><strong>Motivo que dio el agente:</strong> ' . esc_html($reason) . '</p>';
        }

        echo '<table class="widefat striped" style="margin:1em 0">';
        foreach ($preview as $k => $v) {
            echo '<tr><td style="width:200px"><strong>' . esc_html($k) . '</strong></td><td>'
               . esc_html(is_scalar($v) ? (string) $v : wp_json_encode($v)) . '</td></tr>';
        }
        echo '</table>';

        echo '<p><em>Vence: ' . esc_html(gmdate('Y-m-d H:i', $expires)) . ' UTC</em></p>';

        if (!$puede) {
            echo '<p style="color:#b32d2e"><strong>No tenés permiso para aprobar esta acción.</strong> '
               . 'Requiere la capacidad <code>' . esc_html($action['capability'] ?? '?') . '</code>.</p>';
        } else {
            echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline">';
            wp_nonce_field(self::NONCE . '_' . $id);
            echo '<input type="hidden" name="action" value="nandark_gate_decide">';
            echo '<input type="hidden" name="proposal_id" value="' . (int) $id . '">';
            echo '<button type="submit" name="decision" value="approve" class="button button-primary">Aprobar y ejecutar</button> ';
            echo '<button type="submit" name="decision" value="reject" class="button">Rechazar</button>';
            echo '</form>';
        }

        echo '</div>';
    }

    private static function render_history(array $ids) {
        $rows = [];
        foreach ($ids as $id) {
            $status = self::effective_status($id);
            if ($status === self::S_PENDING) {
                continue;
            }
            $rows[] = [$id, $status];
        }

        if (!$rows) {
            return;
        }

        echo '<h2>Historial</h2><table class="widefat striped"><thead><tr>'
           . '<th>#</th><th>Acción</th><th>Estado</th><th>Resultado</th></tr></thead><tbody>';

        foreach (array_slice($rows, 0, 30) as $row) {
            list($id, $status) = $row;
            $type   = (string) get_post_meta($id, '_nandark_type', true);
            $action = self::get_action($type);
            $result = get_post_meta($id, '_nandark_result', true);

            echo '<tr><td>' . (int) $id . '</td>'
               . '<td>' . esc_html($action ? $action['label'] : $type) . '</td>'
               . '<td>' . esc_html($status) . '</td>'
               . '<td>' . esc_html(is_scalar($result) ? (string) $result : wp_json_encode($result)) . '</td></tr>';
        }

        echo '</tbody></table>';
    }

    /* -------------------------------------------------------------- decidir */

    /**
     * Único camino de ejecución del sistema.
     *
     * Se comprueba, en orden: nonce, capability de ESA acción, que la propuesta siga
     * pendiente, que no haya vencido, y que los parámetros sean los mismos que se
     * guardaron. Cualquiera de las cinco que falle corta antes de ejecutar.
     */
    public static function handle_decision() {
        $id = isset($_POST['proposal_id']) ? (int) $_POST['proposal_id'] : 0;

        check_admin_referer(self::NONCE . '_' . $id);

        $decision = isset($_POST['decision']) ? sanitize_key(wp_unslash($_POST['decision'])) : '';
        $post     = get_post($id);

        if (!$post || $post->post_type !== self::CPT) {
            wp_die('Esa propuesta no existe.');
        }

        $type   = (string) get_post_meta($id, '_nandark_type', true);
        $action = self::get_action($type);

        if (!$action) {
            wp_die('Esta propuesta es de un tipo de acción que ya no está registrado. No se ejecuta.');
        }

        if (!current_user_can($action['capability'])) {
            wp_die('No tenés permiso para autorizar esta acción.');
        }

        if (self::effective_status($id) !== self::S_PENDING) {
            // Cortar acá es lo que impide ejecutar dos veces la misma autorización.
            self::redirect_back($id, 'ya-decidida');
        }

        if ($decision === 'reject') {
            update_post_meta($id, '_nandark_status', self::S_REJECTED);
            update_post_meta($id, '_nandark_result', 'Rechazada por ' . wp_get_current_user()->user_login);
            self::redirect_back($id, 'rechazada');
        }

        if ($decision !== 'approve') {
            wp_die('Decisión inválida.');
        }

        $params = self::stored_params($id);
        if (is_wp_error($params)) {
            update_post_meta($id, '_nandark_status', self::S_FAILED);
            update_post_meta($id, '_nandark_result', $params->get_error_message());
            self::redirect_back($id, 'alterada');
        }

        // Se marca aprobada ANTES de ejecutar. Si la ejecución muere a la mitad —un
        // timeout en shared hosting es lo normal, no lo excepcional— la propuesta no
        // queda pendiente para que alguien la apruebe de nuevo y se ejecute dos veces.
        update_post_meta($id, '_nandark_status', self::S_APPROVED);
        update_post_meta($id, '_nandark_approver', get_current_user_id());

        $result = call_user_func($action['execute'], $params);

        if (is_wp_error($result)) {
            update_post_meta($id, '_nandark_status', self::S_FAILED);
            update_post_meta($id, '_nandark_result', $result->get_error_message());
            self::redirect_back($id, 'fallo');
        }

        update_post_meta($id, '_nandark_status', self::S_EXECUTED);
        update_post_meta($id, '_nandark_result', is_scalar($result) ? (string) $result : wp_json_encode($result));
        self::redirect_back($id, 'ejecutada');
    }

    private static function redirect_back($id, $msg) {
        wp_safe_redirect(add_query_arg(
            ['page' => self::MENU_SLUG, 'nandark_msg' => $msg, 'id' => (int) $id],
            admin_url('admin.php')
        ));
        exit;
    }
}
