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
        // 1. Si el tema activo ya tiene su propio archivo single-service.php o page-home.php, respetarlo
        // Si estamos viendo un post individual del CPT nandark_service
        if (is_singular('nandark_service')) {
            $theme_tpl = locate_template(['components/templates/single-service.php', 'single-service.php']);
            if ($theme_tpl) {
                return $theme_tpl;
            }
            $custom_template = NANDARK_ATOMIC_PATH . 'components/templates/single-service.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        // Si estamos viendo el archivo del CPT nandark_service
        if (is_post_type_archive('nandark_service')) {
            $theme_tpl = locate_template(['components/templates/archive-service.php', 'archive-service.php']);
            if ($theme_tpl) {
                return $theme_tpl;
            }
            $custom_template = NANDARK_ATOMIC_PATH . 'components/templates/archive-service.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        // Si estamos en la página de inicio (Front Page o /inicio/)
        if (is_front_page() || is_page('inicio')) {
            $theme_tpl = locate_template(['components/templates/page-home.php', 'page-home.php']);
            if ($theme_tpl) {
                return $theme_tpl;
            }
            $custom_template = NANDARK_ATOMIC_PATH . 'components/templates/page-home.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }

        return $template;
    }
}
