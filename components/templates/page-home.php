<?php
/**
 * Plantilla Template: Page Home con Scrollytelling Cinemático & Diseño Editorial (Origen Gastrobar)
 */
get_header();

$img_url    = NANDARK_ATOMIC_URL . 'assets/images/';
$frames_url = NANDARK_ATOMIC_URL . 'assets/frames/';
?>

<main class="nandark-main nandark-home-page">
    
    <!-- 🍸 FLOATING LUXURY NAVBAR -->
    <header class="origen-nav">
        <div class="nandark-container origen-nav__container">
            <a href="/" class="origen-nav__logo">
                <span>ORIGEN</span>
                <small>GASTROBAR & LOUNGE</small>
            </a>
            <nav class="origen-nav__links">
                <a href="#experiencias">Experiencias</a>
                <a href="#menu">Platos</a>
                <a href="#mixologia">Mixología</a>
                <a href="#reservas" class="origen-nav__btn">Reservar Mesa</a>
            </nav>
        </div>
    </header>

    <!-- 🎬 SCROLLYTELLING CANVAS EXPERIENCE (240 FRAMES) -->
    <section id="scrollytelling-container" class="scrolly-section" data-frames-url="<?php echo esc_url($frames_url); ?>">
        <div class="scrolly-sticky">
            <canvas id="scrollytelling-canvas" class="scrolly-canvas"></canvas>
            <div class="scrolly-overlay"></div>

            <div class="nandark-container scrolly-ui-container">
                
                <!-- Paso 1: Salón Principal (0% a 25%) -->
                <div class="scrolly-step is-active" data-start="0.0" data-end="0.25">
                    <div class="scrolly-card">
                        <div class="scrolly-card__header">
                            <span class="scrolly-badge">Experiencia Bogotá</span>
                            <span class="scrolly-num">01 <i>/ 04</i></span>
                        </div>
                        <h1 class="scrolly-title">ORIGEN</h1>
                        <p class="scrolly-subtitle">Gastrobar de Autor, Mixología & Rooftop Lounge</p>
                        <div class="scrolly-divider"></div>
                        <p class="scrolly-desc">Desliza hacia abajo para recorrer la experiencia gastronómica sensorial en tiempo real.</p>
                        <div class="scrolly-indicator">
                            <div class="scrolly-mouse"><div class="scrolly-wheel"></div></div>
                            <span>Scroll para explorar</span>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Cocina & Platos (25% a 50%) -->
                <div class="scrolly-step" data-start="0.25" data-end="0.50">
                    <div class="scrolly-card">
                        <div class="scrolly-card__header">
                            <span class="scrolly-badge">Alta Cocina</span>
                            <span class="scrolly-num">02 <i>/ 04</i></span>
                        </div>
                        <h2 class="scrolly-title">Platos de Autor</h2>
                        <p class="scrolly-subtitle">Vanguardia & Sabores de Origen</p>
                        <div class="scrolly-divider"></div>
                        <p class="scrolly-desc">Cortes madurados, reducciones artesanales de trufa y técnica molecular en cada creación culinaria.</p>
                    </div>
                </div>

                <!-- Paso 3: Coctelería & Bar (50% a 75%) -->
                <div class="scrolly-step" data-start="0.50" data-end="0.75">
                    <div class="scrolly-card">
                        <div class="scrolly-card__header">
                            <span class="scrolly-badge">Mixología</span>
                            <span class="scrolly-num">03 <i>/ 04</i></span>
                        </div>
                        <h2 class="scrolly-title">Coctelería Botánica</h2>
                        <p class="scrolly-subtitle">Destilados de Autor & Hielo Cristalino</p>
                        <div class="scrolly-divider"></div>
                        <p class="scrolly-desc">Destilados premium, infusiones ahumadas con romero y recetas exclusivas servidas sobre mármol negro.</p>
                    </div>
                </div>

                <!-- Paso 4: Rooftop & Noches (75% a 1.0) -->
                <div class="scrolly-step" data-start="0.75" data-end="1.0">
                    <div class="scrolly-card scrolly-card--cta">
                        <div class="scrolly-card__header">
                            <span class="scrolly-badge scrolly-badge--gold">Noches Exclusivas</span>
                            <span class="scrolly-num">04 <i>/ 04</i></span>
                        </div>
                        <h2 class="scrolly-title">Rooftop Lounge</h2>
                        <p class="scrolly-subtitle">Fogatas Lineales & Vista Panorámica</p>
                        <div class="scrolly-divider"></div>
                        <p class="scrolly-desc">El mejor ambiente nocturno para tus celebraciones. Cupos limitados por turno.</p>
                        <div class="scrolly-actions">
                            <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20reservar%20una%20mesa." class="origen-btn-cta" target="_blank" rel="noopener">
                                <span class="origen-btn-cta__icon">💬</span>
                                <span>Reservar Mesa por WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Experiencias / Grid Detallado -->
    <section id="experiencias" class="origen-section">
        <div class="nandark-container">
            <div class="origen-section-header text-center">
                <span class="scrolly-badge">Carta y Espacios</span>
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
    <section id="reservas" class="origen-booking-strip">
        <div class="nandark-container">
            <div class="origen-booking-box">
                <div class="origen-booking-box__info">
                    <span class="scrolly-badge scrolly-badge--gold">Atención Selecta</span>
                    <h2>¿Listo para vivir la experiencia Origen?</h2>
                    <p>Atención de Miércoles a Domingo a partir de las 5:00 PM. Reserva con anticipación.</p>
                </div>
                <div class="origen-booking-box__cta">
                    <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20reservar%20una%20mesa." class="origen-btn-cta" target="_blank" rel="noopener">
                        <span class="origen-btn-cta__icon">✨</span>
                        <span>Agendar Reserva Inmediata</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
