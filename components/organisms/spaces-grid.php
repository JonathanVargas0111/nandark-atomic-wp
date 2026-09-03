<?php
/**
 * Organismo: Grid de Espacios & Experiencias
 * Props:
 * - img_url (string)
 */
$img_url = $img_url ?? (NANDARK_ATOMIC_URL . 'assets/images/');
?>
<section id="experiencias" class="origen-section">
    <div class="nandark-container">
        <div class="origen-section-header">
            <span class="scrolly-tag">Espacios Diseñados</span>
            <h2 class="origen-section-title">El Arte de los Sentidos</h2>
            <div class="origen-section-line"></div>
            <p class="origen-section-desc">Tres ambientes curados arquitectónicamente para acompañar cada momento de tu velada.</p>
        </div>

        <div class="origen-grid-3">
            <article class="origen-card">
                <div class="origen-card__media">
                    <img src="<?php echo esc_url($img_url . '02-plato-gourmet.jpg'); ?>" alt="Cocina de Vanguardia" loading="lazy">
                </div>
                <div class="origen-card__body">
                    <span class="origen-card__tag">Comedor Principal</span>
                    <h3 class="origen-card__title">Alta Cocina de Autor</h3>
                    <p class="origen-card__text">Cortes madurados, reducciones artesanales y técnicas contemporáneas en un ambiente cálido y sofisticado.</p>
                </div>
            </article>

            <article class="origen-card">
                <div class="origen-card__media">
                    <img src="<?php echo esc_url($img_url . '03-coctel-bar.jpg'); ?>" alt="Mixología de Autor" loading="lazy">
                </div>
                <div class="origen-card__body">
                    <span class="origen-card__tag">Bar & Barra de Mármol</span>
                    <h3 class="origen-card__title">Mixología Experimental</h3>
                    <p class="origen-card__text">Destilados de pequeños productores, infusiones ahumadas y recetas de autor servidas a la temperatura perfecta.</p>
                </div>
            </article>

            <article class="origen-card">
                <div class="origen-card__media">
                    <img src="<?php echo esc_url($img_url . '04-terraza-noche.jpg'); ?>" alt="Rooftop Lounge" loading="lazy">
                </div>
                <div class="origen-card__body">
                    <span class="origen-card__tag">Terraza Abierta</span>
                    <h3 class="origen-card__title">Rooftop & Fogatas</h3>
                    <p class="origen-card__text">Fogatas lineales, acústica cuidada y vista panorámica a la ciudad para cerrar la noche en privacidad.</p>
                </div>
            </article>
        </div>
    </div>
</section>
