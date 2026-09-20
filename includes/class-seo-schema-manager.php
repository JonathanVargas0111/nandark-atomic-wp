<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inyector Nativo de Schema.org JSON-LD & Open Graph
 * Optimizado para indexación en Google y consultas de Inteligencia Artificial (ChatGPT, Perplexity, Gemini).
 */
class Seo_Schema_Manager {

    public static function init() {
        add_action('wp_head', [__CLASS__, 'render_schema_json_ld'], 1);
        add_action('wp_head', [__CLASS__, 'render_open_graph_meta'], 2);
    }

    /**
     * Inyecta Schema JSON-LD estructurado para Gastrobar / Restaurante de Lujo
     */
    public static function render_schema_json_ld() {
        if (!is_front_page() && !is_page('inicio')) {
            return;
        }

        $site_url = home_url('/');
        // La imagen se resuelve por la mediateca (nandark_hero_image), no por una ruta
        // fija: el archivo que estaba escrito a mano aca no existe desde hace meses y
        // og:image servia un 404, o sea cero preview al compartir el sitio.
        $hero     = function_exists('nandark_hero_image') ? nandark_hero_image() : ['id' => null, 'url' => ''];
        $img_url  = $hero['url'];

        $site_name = get_bloginfo('name') ?: 'Nandark';
        $site_desc = get_bloginfo('description') ?: 'Sitio web profesional y de alto rendimiento.';

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'LocalBusiness',
            '@id'         => $site_url . '#business',
            'name'        => $site_name,
            'url'         => $site_url,
            'image'       => $img_url,
            'description' => $site_desc,
        ];

        $schema = apply_filters('nandark_schema_json_ld', $schema);

        echo "\n<!-- 🧠 Nandark GEO / AEO & Schema.org JSON-LD (Search & AI Ready) -->\n";
        // Una clave "image": null es peor que no tenerla: Google la lee como dato invalido.
        if ($img_url === '') {
            unset($schema['image']);
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</script>\n";
    }

    /**
     * Inyecta metadatos Open Graph de alta definición para previsualizaciones en WhatsApp y redes
     */
    public static function render_open_graph_meta() {
        if (!is_front_page() && !is_page('inicio')) {
            return;
        }

        $site_url  = home_url('/');
        $hero      = function_exists('nandark_hero_image') ? nandark_hero_image() : ['id' => null, 'url' => ''];
        $img_url   = $hero['url'];
        $site_name = get_bloginfo('name') ?: 'Nandark';
        $title     = apply_filters('nandark_og_title', $site_name . ' · ' . get_bloginfo('description'));
        $desc      = apply_filters('nandark_og_description', get_bloginfo('description') ?: 'Sitio web oficial.');

        echo "\n<!-- 📱 Nandark Open Graph & Social Cards -->\n";
        echo '<meta property="og:type" content="website" />' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($desc) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url($site_url) . '" />' . "\n";
        if ($img_url !== '') {
            echo '<meta property="og:image" content="' . esc_url($img_url) . '" />' . "\n";
        }
        $dims = !empty($hero['id']) ? wp_get_attachment_image_src((int) $hero['id'], 'full') : null;
        if (is_array($dims) && !empty($dims[1]) && !empty($dims[2])) {
            echo '<meta property="og:image:width" content="' . (int) $dims[1] . '" />' . "\n";
            echo '<meta property="og:image:height" content="' . (int) $dims[2] . '" />' . "\n";
        }
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($desc) . '" />' . "\n";
        if ($img_url !== '') {
            echo '<meta name="twitter:image" content="' . esc_url($img_url) . '" />' . "\n";
        }
    }
}
