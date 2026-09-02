<?php
/**
 * Organismo: Hero Section
 * Props:
 * - badge (string)
 * - title (string)
 * - description (string)
 * - cta_primary (array: ['text' => '', 'url' => ''])
 * - cta_secondary (array: ['text' => '', 'url' => ''])
 */
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
                <?php nandark_render('atoms/badge', ['text' => $badge, 'tone' => 'accent']); ?>
            <?php endif; ?>
            <h1 class="nandark-hero__title"><?php echo esc_html($title); ?></h1>
            <p class="nandark-hero__desc"><?php echo esc_html($description); ?></p>
            <div class="nandark-hero__actions">
                <?php nandark_render('atoms/button', [
                    'text'    => $cta_primary['text'],
                    'url'     => $cta_primary['url'],
                    'variant' => 'whatsapp',
                    'icon'    => '💬'
                ]); ?>
                <?php nandark_render('atoms/button', [
                    'text'    => $cta_secondary['text'],
                    'url'     => $cta_secondary['url'],
                    'variant' => 'secondary'
                ]); ?>
            </div>
        </div>
    </div>
</section>
