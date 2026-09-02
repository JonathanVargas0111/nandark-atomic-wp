<?php
/**
 * Molécula: Service Card
 * Props:
 * - title (string)
 * - excerpt (string)
 * - url (string)
 * - badge (string, optional)
 * - price (string, optional)
 */
$title   = $title ?? get_the_title();
$excerpt = $excerpt ?? get_the_excerpt();
$url     = $url ?? get_permalink();
$badge   = $badge ?? '';
$price   = $price ?? '';
?>
<article class="nandark-card">
    <div class="nandark-card__header">
        <?php if (!empty($badge)): ?>
            <?php nandark_render('atoms/badge', ['text' => $badge, 'tone' => 'accent']); ?>
        <?php endif; ?>
        <?php if (!empty($price)): ?>
            <span class="nandark-card__price"><?php echo esc_html($price); ?></span>
        <?php endif; ?>
    </div>
    <h3 class="nandark-card__title">
        <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a>
    </h3>
    <p class="nandark-card__excerpt"><?php echo esc_html($excerpt); ?></p>
    <div class="nandark-card__footer">
        <?php nandark_render('atoms/button', [
            'text'    => 'Ver servicio',
            'url'     => $url,
            'variant' => 'primary'
        ]); ?>
    </div>
</article>
