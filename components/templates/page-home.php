<?php
/**
 * Plantilla Template: Page Home con Scrollytelling Cinemático (Origen Gastrobar)
 */
get_header();

$img_url    = NANDARK_ATOMIC_URL . 'assets/images/';
$frames_url = NANDARK_ATOMIC_URL . 'assets/frames/';
?>

<main class="nandark-main nandark-home-page">
    
    <!-- 🎬 SCROLLYTELLING CANVAS EXPERIENCE (240 FRAMES) -->
    <section id="scrollytelling-container" class="scrolly-section" data-frames-url="<?php echo esc_url($frames_url); ?>">
        <div class="scrolly-sticky">
            <canvas id="scrollytelling-canvas" class="scrolly-canvas"></canvas>
            <div class="scrolly-overlay"></div>

            <div class="nandark-container scrolly-ui-container">
                <!-- Paso 1: Salón Principal (0% a 25%) -->
                <div class="scrolly-step is-active" data-start="0.0" data-end="0.25">
                    <?php nandark_render('atoms/badge', ['text' => 'Experiencia Sensorial · Bogotá', 'tone' => 'accent']); ?>
                    <h1 class="scrolly-title">ORIGEN</h1>
                    <p class="scrolly-subtitle">Gastrobar de Autor, Mixología & Rooftop Lounge</p>
                    <p class="scrolly-desc">Desliza hacia abajo para recorrer la experiencia gastronómica interactiva.</p>
                    <div class="scrolly-indicator">
                        <span>Scroll para explorar</span>
                        <div class="scrolly-mouse"><div class="scrolly-wheel"></div></div>
                    </div>
                </div>

                <!-- Paso 2: Cocina & Platos (25% a 50%) -->
                <div class="scrolly-step" data-start="0.25" data-end="0.50">
                    <?php nandark_render('atoms/badge', ['text' => '01 · Vanguardia', 'tone' => 'neutral']); ?>
                    <h2 class="scrolly-title">Platos de Autor</h2>
                    <p class="scrolly-desc">Cortes madurados, reducciones artesanales de trufa y técnica molecular en cada creación.</p>
                </div>

                <!-- Paso 3: Coctelería & Bar (50% a 75%) -->
                <div class="scrolly-step" data-start="0.50" data-end="0.75">
                    <?php nandark_render('atoms/badge', ['text' => '02 · Mixología', 'tone' => 'accent']); ?>
                    <h2 class="scrolly-title">Coctelería Botánica</h2>
                    <p class="scrolly-desc">Destilados premium, infusiones ahumadas con romero y recetas exclusivas servidas sobre mármol negro.</p>
                </div>

                <!-- Paso 4: Rooftop & Noches (75% a 1.0) -->
                <div class="scrolly-step" data-start="0.75" data-end="1.0">
                    <?php nandark_render('atoms/badge', ['text' => '03 · Atmósfera', 'tone' => 'neutral']); ?>
                    <h2 class="scrolly-title">Rooftop Lounge</h2>
                    <p class="scrolly-desc">Fogatas lineales, vista a la ciudad y el mejor ambiente nocturno para tus celebraciones.</p>
                    <div class="scrolly-actions">
                        <?php nandark_render('atoms/button', [
                            'text'    => 'Reservar Mesa por WhatsApp',
                            'url'     => 'https://wa.me/573000000000?text=' . urlencode('Hola Origen, quiero reservar una mesa.'),
                            'variant' => 'whatsapp',
                            'icon'    => '💬'
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Experiencias / Grid Detallado -->
    <section id="experiencias" class="origen-section">
        <div class="nandark-container">
            <div class="origen-section-header text-center">
                <?php nandark_render('atoms/badge', ['text' => 'Nuestras Experiencias', 'tone' => 'neutral']); ?>
                <h2 class="origen-section-title">El Arte de los Sentidos</h2>
                <p class="origen-section-desc">Cada detalle está diseñado para transformar una cena en un recuerdo inolvidable.</p>
            </div>

            <div class="origen-grid-3">
                <div class="origen-card">
                    <div class="origen-card__media">
                        <img src="<?php echo esc_url($img_url . '02-plato-gourmet.jpg'); ?>" alt="Cocina de Vanguardia" loading="lazy">
                    </div>
                    <div class="origen-card__body">
                        <span class="origen-card__tag">Alta Cocina</span>
                        <h3 class="origen-card__title">Platos de Autor</h3>
                        <p class="origen-card__text">Cortes madurados, reducciones artesanales y técnicas moleculares con producto local premium.</p>
                    </div>
                </div>

                <div class="origen-card">
                    <div class="origen-card__media">
                        <img src="<?php echo esc_url($img_url . '03-coctel-bar.jpg'); ?>" alt="Mixología de Autor" loading="lazy">
                    </div>
                    <div class="origen-card__body">
                        <span class="origen-card__tag">Mixología</span>
                        <h3 class="origen-card__title">Coctelería Botánica</h3>
                        <p class="origen-card__text">Destilados seleccionados, infusiones ahumadas y recetas de autor servidas con hielo cristalino.</p>
                    </div>
                </div>

                <div class="origen-card">
                    <div class="origen-card__media">
                        <img src="<?php echo esc_url($img_url . '04-terraza-noche.jpg'); ?>" alt="Rooftop Lounge" loading="lazy">
                    </div>
                    <div class="origen-card__body">
                        <span class="origen-card__tag">Atmósfera</span>
                        <h3 class="origen-card__title">Rooftop & Terraza</h3>
                        <p class="origen-card__text">Fogatas lineales, vista a la ciudad y la mejor selección musical para cerrar la noche.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Reserva Rápida / WhatsApp Directo -->
    <section class="origen-booking-strip">
        <div class="nandark-container">
            <div class="origen-booking-box">
                <div class="origen-booking-box__info">
                    <h2>¿Listo para vivir la experiencia Origen?</h2>
                    <p>Atención de Miércoles a Domingo a partir de las 5:00 PM. Cupos limitados por turno.</p>
                </div>
                <div class="origen-booking-box__cta">
                    <?php nandark_render('atoms/button', [
                        'text'    => 'Agendar Reserva Inmediata',
                        'url'     => 'https://wa.me/573000000000?text=' . urlencode('Hola, quiero verificar disponibilidad para este fin de semana.'),
                        'variant' => 'whatsapp',
                        'icon'    => '✨'
                    ]); ?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
