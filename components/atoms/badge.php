<?php
/**
 * Átomo: Badge
 * Props:
 * - text (string)
 * - tone (string): 'accent' | 'neutral' | 'success'
 */
$text = $text ?? '';
$tone = $tone ?? 'accent';
?>
<span class="nandark-badge nandark-badge--<?php echo esc_attr($tone); ?>">
    <?php echo esc_html($text); ?>
</span>
