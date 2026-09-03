<?php
/**
 * Organismo: Hero Section (Limpio & Vectorial)
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;

$badge         = $badge ?? 'Atención Especializada';
$title         = $title ?? 'Cuidado Veterinario Integral para tu Mascota';
$description   = $description ?? 'Medicina preventiva, cirugías, urgencias y bienestar con tecnología avanzada en Bogotá.';
$cta_primary   = $cta_primary ?? ['text' => 'Agendar por WhatsApp', 'url' => 'https://wa.me/573000000000'];
$cta_secondary = $cta_secondary ?? ['text' => 'Ver Servicios', 'url' => '#servicios'];
?>
<section class="nandark-hero">
    <div class="nandark-container">
        <div class="nandark-hero__content">
            <?php if (!empty($badge)): ?>
                <span class="scrolly-tag"><?php echo esc_html($badge); ?></span>
            <?php endif; ?>
            <h1 class="nandark-hero__title"><?php echo esc_html($title); ?></h1>
            <div class="scrolly-line"></div>
            <p class="nandark-hero__desc"><?php echo esc_html($description); ?></p>
            <div class="nandark-hero__actions">
                <a href="<?php echo esc_url($cta_primary['url']); ?>" class="origen-btn-solid" target="_blank" rel="noopener">
                    <span class="origen-btn-solid__icon"><?php echo SVG::whatsapp(); // phpcs:ignore ?></span>
                    <span><?php echo esc_html($cta_primary['text']); ?></span>
                    <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                </a>
            </div>
        </div>
    </div>
</section>
