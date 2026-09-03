<?php
/**
 * Átomo: Button
 * Props:
 * - text (string)
 * - url (string)
 * - variant (string): 'primary' | 'secondary' | 'whatsapp' | 'brass'
 * - icon_svg (string, optional raw svg)
 * - target (string, optional): '_self' | '_blank'
 */
$text     = $text ?? 'Explorar';
$url      = $url ?? '#';
$variant  = $variant ?? 'primary';
$icon_svg = $icon_svg ?? '';
$target   = $target ?? '_self';
?>
<a href="<?php echo esc_url($url); ?>" class="nandark-btn nandark-btn--<?php echo esc_attr($variant); ?>" target="<?php echo esc_attr($target); ?>" <?php echo ($target === '_blank') ? 'rel="noopener noreferrer"' : ''; ?>>
    <span><?php echo esc_html($text); ?></span>
    <?php if (!empty($icon_svg)): ?>
        <span class="nandark-btn__icon"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
    <?php endif; ?>
</a>
