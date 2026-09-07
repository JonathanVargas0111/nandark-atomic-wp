<?php
/**
 * Plantilla Template: Single Post (Entrada de Blog / Crónica Editorial)
 * Arquitectura 100% Atómica para artículos creados vía MCP o WordPress Admin
 */
get_header();
?>

<main class="nandark-main nandark-single-post">
    <!-- Navbar Organism -->
    <?php nandark_render('organisms/navbar'); ?>

    <article class="origen-single-article">
        <div class="nandark-container">
            <?php while (have_posts()) : the_post(); ?>
                <header class="origen-article-header">
                    <span class="scrolly-tag">Crónica Gastronómica · <?php echo esc_html(get_the_date('d F, Y')); ?></span>
                    <h1 class="origen-article-title"><?php the_title(); ?></h1>
                    <div class="origen-section-line"></div>
                </header>

                <div class="origen-article-content">
                    <?php the_content(); ?>
                </div>

                <footer class="origen-article-footer">
                    <a href="<?php echo esc_url(home_url('/#bitacora')); ?>" class="origen-article-back">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                        <span>Volver a la Bitácora</span>
                    </a>
                </footer>
            <?php endwhile; ?>
        </div>
    </article>

    <!-- Footer Editorial Organism -->
    <?php nandark_render('organisms/footer-editorial'); ?>
</main>

<?php
get_footer();
