<?php
/**
 * Plantilla Template: Page Home — Origen Gastrobar (Diseño de Alta Gama / Anti-AI Slop)
 */
get_header();

require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;

$img_url    = NANDARK_ATOMIC_URL . 'assets/images/';
$frames_url = NANDARK_ATOMIC_URL . 'assets/frames/';
?>

<main class="nandark-main nandark-home-page">
    
    <!-- 🏛️ BARRA DE NAVEGACIÓN MINIMALISTA DE ALTO NIVEL -->
    <header class="origen-nav">
        <div class="nandark-container origen-nav__container">
            <a href="/" class="origen-nav__logo">
                <span class="origen-nav__logo-title">ORIGEN</span>
                <span class="origen-nav__logo-sub">BOGOTÁ · GASTRONOMÍA & MIXOLOGÍA</span>
            </a>
            <nav class="origen-nav__links">
                <a href="#experiencias">Experiencias</a>
                <a href="#experiencias">Platos</a>
                <a href="#experiencias">Mixología</a>
                <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20reservar%20una%20mesa." class="origen-nav__cta" target="_blank" rel="noopener">
                    <span>Reservar Mesa</span>
                    <?php echo SVG::arrow_right(); // phpcs:ignore ?>
                </a>
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
                        <div class="scrolly-card__meta">
                            <span class="scrolly-tag">Atmósfera Principal</span>
                            <span class="scrolly-index">01 &mdash; 04</span>
                        </div>
                        <h1 class="scrolly-title">ORIGEN</h1>
                        <p class="scrolly-subtitle">Cocina de Autor & Mixología Experimental</p>
                        <div class="scrolly-line"></div>
                        <p class="scrolly-desc">Una experiencia sensorial diseñada para recorrer los sabores del origen en un ambiente arquitectónico sobrio y envolvente.</p>
                        <div class="scrolly-indicator">
                            <div class="scrolly-indicator__line"></div>
                            <span>Desliza para recorrer la experiencia</span>
                        </div>
                    </div>
                </div>

                <!-- Paso 2: Cocina & Platos (25% a 50%) -->
                <div class="scrolly-step" data-start="0.25" data-end="0.50">
                    <div class="scrolly-card">
                        <div class="scrolly-card__meta">
                            <span class="scrolly-tag">Propuesta Culinaria</span>
                            <span class="scrolly-index">02 &mdash; 04</span>
                        </div>
                        <h2 class="scrolly-title">Platos de Autor</h2>
                        <p class="scrolly-subtitle">Técnica Contemporánea & Producto Local</p>
                        <div class="scrolly-line"></div>
                        <p class="scrolly-desc">Cortes madurados, reducciones artesanales de trufa y texturas diseñadas con precisión milimétrica en cada servicio.</p>
                    </div>
                </div>

                <!-- Paso 3: Coctelería & Bar (50% a 75%) -->
                <div class="scrolly-step" data-start="0.50" data-end="0.75">
                    <div class="scrolly-card">
                        <div class="scrolly-card__meta">
                            <span class="scrolly-tag">Laboratorio de Bar</span>
                            <span class="scrolly-index">03 &mdash; 04</span>
                        </div>
                        <h2 class="scrolly-title">Mixología Botánica</h2>
                        <p class="scrolly-subtitle">Infusiones Ahumadas & Hielo Cristalino</p>
                        <div class="scrolly-line"></div>
                        <p class="scrolly-desc">Destilados selectos, hierbas aromáticas tratadas en frío y cristalería fina sobre barra de mármol pulido.</p>
                    </div>
                </div>

                <!-- Paso 4: Rooftop & Noches (75% a 1.0) -->
                <div class="scrolly-step" data-start="0.75" data-end="1.0">
                    <div class="scrolly-card scrolly-card--highlight">
                        <div class="scrolly-card__meta">
                            <span class="scrolly-tag scrolly-tag--accent">Exclusividad & Noches</span>
                            <span class="scrolly-index">04 &mdash; 04</span>
                        </div>
                        <h2 class="scrolly-title">Rooftop Lounge</h2>
                        <p class="scrolly-subtitle">Fogatas Lineales & Vista a la Ciudad</p>
                        <div class="scrolly-line"></div>
                        <p class="scrolly-desc">Mesas con vista panorámica y atención personalizada. Disponibilidad limitada por turno de servicio.</p>
                        <div class="scrolly-actions">
                            <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20solicitar%20una%20reserva." class="origen-btn-solid" target="_blank" rel="noopener">
                                <span class="origen-btn-solid__icon"><?php echo SVG::whatsapp(); // phpcs:ignore ?></span>
                                <span>Solicitar Reserva Inmediata</span>
                                <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- 🍽️ SECCIÓN EXPERIENCIAS: GRID SOBRIO Y ELEGANTE -->
    <section id="experiencias" class="origen-section">
        <div class="nandark-container">
            <div class="origen-section-header">
                <span class="scrolly-tag">Carta & Espacios</span>
                <h2 class="origen-section-title">El Arte de los Sentidos</h2>
                <div class="origen-section-line"></div>
                <p class="origen-section-desc">Cada espacio está concebido para acompañar momentos memorables con atención sin interrupciones.</p>
            </div>

            <div class="origen-grid-3">
                <article class="origen-card">
                    <div class="origen-card__media">
                        <img src="<?php echo esc_url($img_url . '02-plato-gourmet.jpg'); ?>" alt="Cocina de Vanguardia" loading="lazy">
                    </div>
                    <div class="origen-card__body">
                        <span class="origen-card__tag">Alta Cocina</span>
                        <h3 class="origen-card__title">Platos de Autor</h3>
                        <p class="origen-card__text">Cortes madurados, reducciones artesanales y técnicas contemporáneas con ingredientes seleccionados.</p>
                    </div>
                </article>

                <article class="origen-card">
                    <div class="origen-card__media">
                        <img src="<?php echo esc_url($img_url . '03-coctel-bar.jpg'); ?>" alt="Mixología de Autor" loading="lazy">
                    </div>
                    <div class="origen-card__body">
                        <span class="origen-card__tag">Mixología</span>
                        <h3 class="origen-card__title">Coctelería Botánica</h3>
                        <p class="origen-card__text">Destilados artesanales, infusiones ahumadas y recetas equilibradas servidas a la temperatura perfecta.</p>
                    </div>
                </article>

                <article class="origen-card">
                    <div class="origen-card__media">
                        <img src="<?php echo esc_url($img_url . '04-terraza-noche.jpg'); ?>" alt="Rooftop Lounge" loading="lazy">
                    </div>
                    <div class="origen-card__body">
                        <span class="origen-card__tag">Atmósfera</span>
                        <h3 class="origen-card__title">Rooftop & Terraza</h3>
                        <p class="origen-card__text">Fogatas lineales, acústica cuidada y el mejor ambiente nocturno para tus reuniones exclusivas.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- 📲 STRIP DE CONVERSIÓN SOBRIO -->
    <section id="reservas" class="origen-booking-strip">
        <div class="nandark-container">
            <div class="origen-booking-box">
                <div class="origen-booking-box__info">
                    <span class="scrolly-tag scrolly-tag--accent">Atención por Reserva</span>
                    <h2>Planifica tu velada en Origen</h2>
                    <p>Miércoles a Domingo desde las 5:00 PM. Recomendamos agendar con al menos 24 horas de antelación.</p>
                </div>
                <div class="origen-booking-box__cta">
                    <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20reservar%20una%20mesa." class="origen-btn-solid" target="_blank" rel="noopener">
                        <span class="origen-btn-solid__icon"><?php echo SVG::whatsapp(); // phpcs:ignore ?></span>
                        <span>Contactar por WhatsApp</span>
                        <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
