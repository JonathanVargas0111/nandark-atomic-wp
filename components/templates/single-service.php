<?php
/**
 * Plantilla Template: Single Service
 */
get_header();
?>

<main class="nandark-main nandark-service-single">
    <div class="nandark-container">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('nandark-service-article'); ?>>
                <header class="nandark-service-header">
                    <?php nandark_render('atoms/badge', ['text' => 'Servicio Veterinario', 'tone' => 'accent']); ?>
                    <h1 class="nandark-service-title"><?php the_title(); ?></h1>
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
                    <h3>¿Necesitas agendar este servicio?</h3>
                    <p>Contáctanos directamente y te responderemos en minutos.</p>
                    <?php nandark_render('atoms/button', [
                        'text'    => 'Consultar por WhatsApp',
                        'url'     => 'https://wa.me/573000000000?text=' . urlencode('Hola, me interesa información sobre ' . get_the_title()),
                        'variant' => 'whatsapp',
                        'icon'    => '💬'
                    ]); ?>
                </footer>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
