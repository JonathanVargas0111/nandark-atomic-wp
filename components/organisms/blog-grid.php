<?php
/**
 * Organismo: Bitácora & Crónicas Gastronómicas (Blog Editorial)
 * Renderiza los artículos recientes o crónicas del chef/sommelier.
 */
?>
<section class="origen-section origen-section--dark" id="bitacora">
    <div class="nandark-container">
        <div class="origen-section-header">
            <span class="scrolly-tag">Bitácora & Filosofía</span>
            <h2 class="origen-section-title">Crónicas de Mesa & Fuego</h2>
            <div class="origen-section-line"></div>
            <p class="origen-section-desc">Ensayos breves sobre cocina de origen, maduración de cortes y el arte silencioso de la coctelería contemporánea.</p>
        </div>

        <div class="origen-blog-grid">
            <?php
            $blog_query = new \WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 3,
                'post_status'    => 'publish',
            ]);

            if ($blog_query->have_posts()) :
                $counter = 1;
                while ($blog_query->have_posts()) :
                    $blog_query->the_post();
                    $date_formatted = get_the_date('d F, Y');
                    $excerpt = get_the_excerpt() ?: wp_trim_words(get_the_content(), 22, '...');
                    ?>
                    <article class="origen-blog-card">
                        <span class="origen-blog-date"><?php echo esc_html(strtoupper($date_formatted)); ?> · CRÓNICA 0<?php echo $counter++; ?></span>
                        <h3 class="origen-blog-title"><?php the_title(); ?></h3>
                        <p class="origen-blog-excerpt"><?php echo esc_html($excerpt); ?></p>
                        <a href="<?php the_permalink(); ?>" class="origen-blog-link">
                            <span>Leer Crónica</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <p style="color: #64748b; font-size: 0.9rem; grid-column: 1 / -1; text-align: center;">No hay crónicas publicadas aún.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
