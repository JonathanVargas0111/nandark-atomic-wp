<?php
/**
 * Molécula: Menu Item
 * Props:
 * - name (string)
 * - price (string)
 * - desc (string)
 * - badge (string, optional)
 * - category (string: 'cocina' | 'mixologia' | 'postres')
 */
$name     = $name ?? '';
$price    = $price ?? '';
$desc     = $desc ?? '';
$badge    = $badge ?? '';
$category = $category ?? 'cocina';
?>
<div class="origen-menu-item" data-category="<?php echo esc_attr($category); ?>">
    <div class="origen-menu-item__top">
        <h4 class="origen-menu-item__name"><?php echo esc_html($name); ?></h4>
        <span class="origen-menu-item__price"><?php echo esc_html($price); ?></span>
    </div>
    <p class="origen-menu-item__desc"><?php echo esc_html($desc); ?></p>
    <?php if (!empty($badge)): ?>
        <span class="origen-menu-item__badge"><?php echo esc_html($badge); ?></span>
    <?php endif; ?>
</div>
