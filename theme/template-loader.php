<?php
namespace NandarkAtomic;

if (!defined('ABSPATH')) {
    exit;
}

class Template_Loader {
    public static function init() {
        add_filter('template_include', [__CLASS__, 'load_atomic_templates']);
    }

    public static function load_atomic_templates($template) {
        // Si estamos viendo un post individual del CPT nandark_service
        if (is_singular('nandark_service')) {
            $custom_template = NANDARK_ATOMIC_PATH . 'components/templates/single-service.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        // Si estamos viendo el archivo del CPT nandark_service
        if (is_post_type_archive('nandark_service')) {
            $custom_template = NANDARK_ATOMIC_PATH . 'components/templates/archive-service.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        // Si estamos en la página de inicio (Front Page o /inicio/)
        if (is_front_page() || is_page('inicio')) {
            $custom_template = NANDARK_ATOMIC_PATH . 'components/templates/page-home.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        return $template;
    }
}
