<?php
/**
 * Plantilla Template: Single Service (Diseño Sobrio & Vectorial)
 */
get_header();

require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;
?>

<main class="nandark-main nandark-service-single">
    <div class="nandark-container">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('nandark-service-article'); ?>>
                <header class="nandark-service-header">
                    <span class="scrolly-tag">Servicio Especializado</span>
                    <h1 class="nandark-service-title"><?php the_title(); ?></h1>
                    <div class="scrolly-line"></div>
                </header>

                <div class="nandark-service-body">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="nandark-service-featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="nandark-service-content">
                        <?php the_content(); ?>
                    </div>
                </div>

                <footer class="nandark-service-cta-box">
                    <h3>¿Deseas consultar o agendar este servicio?</h3>
                    <p>Atención directa y personalizada por WhatsApp sin esperas.</p>
                    <div class="nandark-service-cta-actions">
                        <a href="<?php echo esc_url('https://wa.me/573000000000?text=' . rawurlencode('Hola, me interesa información sobre: ' . get_the_title())); ?>" class="origen-btn-solid" target="_blank" rel="noopener">
                            <span class="origen-btn-solid__icon"><?php echo SVG::whatsapp(); // phpcs:ignore ?></span>
                            <span>Consultar por WhatsApp</span>
                            <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                        </a>
                    </div>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
