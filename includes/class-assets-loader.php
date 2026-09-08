<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

class Assets_Loader {
    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_styles']);
    }

    public static function enqueue_styles() {
        wp_register_style(
            'nandark-atomic-core',
            NANDARK_ATOMIC_URL . 'assets/css/atomic-core.css',
            [],
            NANDARK_ATOMIC_VERSION
        );

        wp_enqueue_style('nandark-atomic-core');

        // Si el Tema activo tiene un archivo style.css o theme.css, encolarlo automáticamente como capa superior (Skin)
        if (file_exists(get_stylesheet_directory() . '/style.css')) {
            wp_enqueue_style('nandark-theme-skin', get_stylesheet_uri(), ['nandark-atomic-core'], wp_get_theme()->get('Version'));
        }

        // Se REGISTRA, no se encola: el organismo scrollytelling-hero hace el
        // enqueue cuando realmente se renderiza. Antes se bajaba en cada página
        // del sitio, incluso donde no hay canvas. (El comentario viejo decía
        // "solo si existe el contenedor" y no era cierto.)
        wp_register_script(
            'nandark-scrollytelling',
            NANDARK_ATOMIC_URL . 'assets/js/scrollytelling.js',
            [],
            NANDARK_ATOMIC_VERSION,
            true
        );

        // Encolar React y wp-element nativos de WordPress + mobile-nav.js
        wp_enqueue_script(
            'nandark-mobile-react',
            NANDARK_ATOMIC_URL . 'assets/js/mobile-nav.js',
            ['wp-element'], // Dependencia nativa de React en WordPress Core
            NANDARK_ATOMIC_VERSION,
            true
        );
    }
}
