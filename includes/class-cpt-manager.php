<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

class CPT_Manager {
    public static function init() {
        add_action('init', [__CLASS__, 'register_post_types']);
    }

    public static function register_post_types() {
        // CPT: Servicios / Tratamientos (Ideal para Veterinaria, Salud o Agencia)
        $labels = [
            'name'                  => _x('Servicios', 'Post type general name', 'nandark-atomic'),
            'singular_name'         => _x('Servicio', 'Post type singular name', 'nandark-atomic'),
            'menu_name'             => _x('Servicios', 'Admin Menu text', 'nandark-atomic'),
            'add_new'               => __('Añadir nuevo', 'nandark-atomic'),
            'add_new_item'          => __('Añadir nuevo servicio', 'nandark-atomic'),
            'edit_item'             => __('Editar servicio', 'nandark-atomic'),
            'all_items'             => __('Todos los servicios', 'nandark-atomic'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'servicios'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-star-filled',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'show_in_rest'       => true, // Permite que el MCP y Gutenberg accedan
        ];

        register_post_type('nandark_service', $args);
    }
}
