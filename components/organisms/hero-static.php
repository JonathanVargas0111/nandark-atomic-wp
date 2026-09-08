<?php
/**
 * Organismo: Hero estático de una sola imagen.
 *
 * Reemplaza al scrollytelling de 240 frames. Aquel bajaba ~55 MB por visitante
 * para simular un video; éste baja una imagen y el navegador elige el tamaño que
 * le sirve gracias al srcset que genera WordPress.
 *
 * Sin canvas, sin JavaScript, sin listeners de scroll.
 *
 * Props (todas opcionales):
 * - tag        (string) Etiqueta chica sobre el título
 * - title      (string) Título principal
 * - subtitle   (string) Bajada
 * - description(string) Párrafo
 * - image_id   (int)    Attachment a usar; por defecto lo resuelve nandark_hero_image()
 * - image_url  (string) URL directa, si no hay attachment
 * - cta_label  (string) Texto del botón; vacío = sin botón
 * - cta_url    (string) Destino del botón
 */

if (!defined('ABSPATH')) {
    exit;
}

$hero = function_exists('nandark_hero_image') ? nandark_hero_image() : ['id' => null, 'url' => ''];

$image_id   = isset($image_id) ? (int) $image_id : $hero['id'];
$image_url  = isset($image_url) ? $image_url : $hero['url'];

$tag         = $tag         ?? 'Atmósfera Principal';
$title       = $title       ?? 'ORIGEN';
$subtitle    = $subtitle    ?? 'Cocina de Autor & Mixología Experimental';
$description = $description ?? 'Una experiencia sensorial diseñada para recorrer los sabores del origen en un ambiente arquitectónico sobrio y envolvente.';
$cta_label   = $cta_label   ?? '';
$cta_url     = $cta_url     ?? '';
?>
<section class="nandark-hero" id="hero">

    <div class="nandark-hero__media" aria-hidden="true">
        <?php
        if ($image_id) {
            // wp_get_attachment_image emite srcset/sizes: en móvil se baja la
            // versión chica, no la de 1920px.
            echo wp_get_attachment_image($image_id, 'full', false, [
                'class'         => 'nandark-hero__img',
                'alt'           => '',
                'fetchpriority' => 'high',
                'decoding'      => 'async',
                'sizes'         => '100vw',
            ]);
        } elseif ($image_url) {
            printf(
                '<img class="nandark-hero__img" src="%s" alt="" fetchpriority="high" decoding="async">',
                esc_url($image_url)
            );
        }
        ?>
        <div class="nandark-hero__scrim"></div>
    </div>

    <div class="nandark-container nandark-hero__inner">
        <div class="nandark-hero__card">
            <?php if ($tag) : ?>
                <span class="nandark-hero__tag"><?php echo esc_html($tag); ?></span>
            <?php endif; ?>

            <h1 class="nandark-hero__title"><?php echo esc_html($title); ?></h1>

            <?php if ($subtitle) : ?>
                <p class="nandark-hero__subtitle"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>

            <div class="nandark-hero__rule"></div>

            <?php if ($description) : ?>
                <p class="nandark-hero__desc"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <?php if ($cta_label && $cta_url) : ?>
                <a class="nandark-hero__cta" href="<?php echo esc_url($cta_url); ?>">
                    <?php echo esc_html($cta_label); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

</section>
