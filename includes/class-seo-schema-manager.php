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
        $img_url  = NANDARK_ATOMIC_URL . 'assets/images/hero_lounge_interior_1788380532462.jpg';

        $schema = [
            '@context'         => 'https://schema.org',
            '@type'            => ['Restaurant', 'BarOrPub'],
            '@id'              => $site_url . '#restaurant',
            'name'             => 'ORIGEN Gastrobar & Rooftop Lounge',
            'alternateName'    => 'Origen Bogotá',
            'url'              => $site_url,
            'image'            => $img_url,
            'description'      => 'Gastronomía contemporánea de fuego, mixología botánica experimental y terraza rooftop exclusiva en Bogotá.',
            'servesCuisine'    => ['Contemporánea', 'Cocina de Autor', 'Parrilla de Vanguardia', 'Mixología'],
            'priceRange'       => '$$$',
            'telephone'        => '+57-300-000-0000',
            'currenciesAccepted' => 'COP',
            'paymentAccepted'  => 'Cash, Credit Card, Debit Card, Transfer',
            'address'          => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => 'Calle 85 # 11 - 53, Piso 4',
                'addressLocality' => 'Bogotá',
                'addressRegion'   => 'Cundinamarca',
                'postalCode'      => '110221',
                'addressCountry'  => 'CO',
            ],
            'geo'              => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => 4.6682,
                'longitude' => -74.0538,
            ],
            'openingHoursSpecification' => [
                [
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens'     => '17:00',
                    'closes'    => '02:00',
                ],
                [
                    '@type'     => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Sunday'],
                    'opens'     => '13:00',
                    'closes'    => '21:00',
                ],
            ],
            'menu'             => $site_url . '#carta',
            'acceptsReservations' => 'True',
            'hasMenu'          => [
                '@type' => 'Menu',
                'name'  => 'Carta de Temporada Origen',
                'hasMenuItem' => [
                    [
                        '@type'       => 'MenuItem',
                        'name'        => 'Ojo de Bife Madurado 45 Días',
                        'description' => '400g corte premium a la brasa de quebracho con mantequilla de tuétano.',
                        'offers'      => [
                            '@type'         => 'Offer',
                            'price'         => '115000',
                            'priceCurrency' => 'COP',
                        ],
                    ],
                    [
                        '@type'       => 'MenuItem',
                        'name'        => 'Tartar de Atún Rojo & Trufa Negra',
                        'description' => 'Atún aleta amarilla cortado a cuchillo, emulsión de trufa silvestre y yema curada.',
                        'offers'      => [
                            '@type'         => 'Offer',
                            'price'         => '68000',
                            'priceCurrency' => 'COP',
                        ],
                    ],
                    [
                        '@type'       => 'MenuItem',
                        'name'        => 'Humo de Origen',
                        'description' => 'Bourbon añejo macerado con roble andino, bitter artesanal de cacao y campana de humo.',
                        'offers'      => [
                            '@type'         => 'Offer',
                            'price'         => '46000',
                            'priceCurrency' => 'COP',
                        ],
                    ],
                ],
            ],
        ];

        echo "\n<!-- 🧠 Nandark GEO / AEO & Schema.org JSON-LD (Search & AI Ready) -->\n";
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</script>\n";
    }

    /**
     * Inyecta metadatos Open Graph de alta definición para previsualizaciones en WhatsApp y redes
     */
    public static function render_open_graph_meta() {
        if (!is_front_page() && !is_page('inicio')) {
            return;
        }

        $site_url = home_url('/');
        $img_url  = NANDARK_ATOMIC_URL . 'assets/images/hero_lounge_interior_1788380532462.jpg';
        $title    = 'ORIGEN Bogotá · Cocina de Autor, Mixología & Rooftop Lounge';
        $desc     = 'Experiencia gastronómica contemporánea, scrollytelling sensorial y reservas privadas en el corazón de Bogotá.';

        echo "\n<!-- 📱 Nandark Open Graph & Social Cards -->\n";
        echo '<meta property="og:type" content="restaurant" />' . "\n";
        echo '<meta property="og:site_name" content="ORIGEN Gastrobar" />' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($desc) . '" />' . "\n";
        echo '<meta property="og:url" content="' . esc_url($site_url) . '" />' . "\n";
        echo '<meta property="og:image" content="' . esc_url($img_url) . '" />' . "\n";
        echo '<meta property="og:image:width" content="1200" />' . "\n";
        echo '<meta property="og:image:height" content="630" />' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($desc) . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($img_url) . '" />' . "\n";
    }
}
