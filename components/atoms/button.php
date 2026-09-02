<?php
/**
 * Átomo: Button
 * Props:
 * - text (string)
 * - url (string)
 * - variant (string): 'primary' | 'secondary' | 'whatsapp'
 * - icon (string): optional
 */
$text    = $text ?? 'Ver más';
$url     = $url ?? '#';
$variant = $variant ?? 'primary';
?>
<a href="<?php echo esc_url($url); ?>" class="nandark-btn nandark-btn--<?php echo esc_attr($variant); ?>">
    <span><?php echo esc_html($text); ?></span>
    <?php if (!empty($icon)): ?>
        <span class="nandark-btn__icon"><?php echo esc_html($icon); ?></span>
    <?php endif; ?>
</a>
