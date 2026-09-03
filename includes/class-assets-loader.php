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

        // Encolar script interactivo de scrollytelling
        wp_enqueue_script(
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
