<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Optimizador de Rendimiento Nativo (Anti-Bloat de WordPress Core)
 * Elimina emojis, Gutenberg CSS sobrante, embeds, XML-RPC y cabeceras innecesarias.
 */
class Performance_Optimizer {

    public static function init() {
        // 1. Eliminar scripts y estilos de Emojis de WP Core
        add_action('init', [__CLASS__, 'disable_wp_emojis']);

        // 2. Limpiar Gutenberg CSS en páginas que usan Atomic Core (evita cargar 50kb de CSS no usado)
        add_action('wp_enqueue_scripts', [__CLASS__, 'deregister_block_library_css'], 100);

        // 3. Limpiar cabeceras HTML innecesarias (wlwmanifest, rsd, generator, shortlink)
        add_action('init', [__CLASS__, 'clean_html_head']);

        // 4. Deshabilitar XML-RPC por seguridad y consumo de CPU
        add_filter('xmlrpc_enabled', '__return_false');
        remove_action('wp_head', 'rsd_link');

        // 5. Deshabilitar oEmbeds innecesarios en frontend
        add_action('init', [__CLASS__, 'disable_embeds']);
    }

    public static function disable_wp_emojis() {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('tiny_mce_plugins', [__CLASS__, 'disable_emojis_tinymce']);
        add_filter('wp_resource_hints', [__CLASS__, 'disable_emojis_dns_prefetch'], 10, 2);
    }

    public static function disable_emojis_tinymce($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, ['wpemoji']);
        }
        return [];
    }

    public static function disable_emojis_dns_prefetch($urls, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/');
            $urls = array_diff($urls, [$emoji_svg_url]);
        }
        return $urls;
    }

    public static function deregister_block_library_css() {
        // Si estamos en la home o páginas gestionadas por Nandark Atomic, removemos el CSS pesado de bloques
        if (is_front_page() || is_page('inicio') || is_singular('nandark_service')) {
            wp_dequeue_style('wp-block-library');
            wp_dequeue_style('wp-block-library-theme');
            wp_dequeue_style('wc-blocks-style'); // WooCommerce si estuviera activo
            wp_dequeue_style('global-styles'); // inline global styles de FSE
        }
    }

    public static function clean_html_head() {
        remove_action('wp_head', 'wp_generator'); // Ocultar versión de WordPress
        remove_action('wp_head', 'wlwmanifest_link'); // Windows Live Writer
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    }

    public static function disable_embeds() {
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');
    }
}
