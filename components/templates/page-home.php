<?php
/**
 * Plantilla Template: Page Home Completa — Origen Gastrobar (Diseño Editorial de Alto Nivel)
 */
get_header();

require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;

$img_url    = NANDARK_ATOMIC_URL . 'assets/images/';
$frames_url = NANDARK_ATOMIC_URL . 'assets/frames/';
?>

<main class="nandark-main nandark-home-page">
    
    <!-- 🏛️ BARRA DE NAVEGACIÓN EDITORIAL -->
    <header class="origen-nav">
        <div class="nandark-container origen-nav__container">
            <a href="/" class="origen-nav__logo">
                <span class="origen-nav__logo-title">ORIGEN</span>
                <span class="origen-nav__logo-sub">BOGOTÁ · GASTRONOMÍA & MIXOLOGÍA</span>
            </a>
            <nav class="origen-nav__links">
                <a href="#experiencias">Espacios</a>
                <a href="#carta">La Carta</a>
                <a href="#eventos">Eventos Privados</a>
                <a href="#info">Ubicación</a>
                <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20solicitar%20una%20reserva." class="origen-nav__cta" target="_blank" rel="noopener">
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
                            <span>Desliza para recorrer los espacios</span>
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

    <!-- 🍽️ SECCIÓN 1: ESPACIOS & EXPERIENCIAS -->
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

    <!-- 📜 SECCIÓN 2: LA CARTA DE TEMPORADA (MENÚ SELECCIONADO) -->
    <section id="carta" class="origen-section origen-section--dark">
        <div class="nandark-container">
            <div class="origen-section-header">
                <span class="scrolly-tag">Menú de Temporada</span>
                <h2 class="origen-section-title">Creaciones Seleccionadas</h2>
                <div class="origen-section-line"></div>
                <p class="origen-section-desc">Ingredientes de origen trazable, técnicas de maduración propia y recetas diseñadas por nuestro chef ejecutivo.</p>
            </div>

            <div class="origen-menu-grid">
                <!-- Columna 1: Cocina de Autor -->
                <div class="origen-menu-col">
                    <div class="origen-menu-col__header">
                        <span class="origen-menu-col__num">01</span>
                        <h3>Cocina de Fuego & Autor</h3>
                    </div>

                    <div class="origen-menu-item">
                        <div class="origen-menu-item__top">
                            <h4 class="origen-menu-item__name">Ojo de Bife Madurado 45 Días</h4>
                            <span class="origen-menu-item__price">$115.000</span>
                        </div>
                        <p class="origen-menu-item__desc">400g de corte premium a la brasa de quebracho, mantequilla de tuétano y sal marina de Manaure.</p>
                        <span class="origen-menu-item__badge">Recomendación del Chef</span>
                    </div>

                    <div class="origen-menu-item">
                        <div class="origen-menu-item__top">
                            <h4 class="origen-menu-item__name">Tartar de Atún Rojo & Trufa Negra</h4>
                            <span class="origen-menu-item__price">$68.000</span>
                        </div>
                        <p class="origen-menu-item__desc">Atún aleta amarilla cortado a cuchillo, emulsión de trufa silvestre, yema curada y láminas de wonton crujiente.</p>
                    </div>

                    <div class="origen-menu-item">
                        <div class="origen-menu-item__top">
                            <h4 class="origen-menu-item__name">Risotto de Hongos Andinos & Médula</h4>
                            <span class="origen-menu-item__price">$74.000</span>
                        </div>
                        <p class="origen-menu-item__desc">Arroz carnaroli con reducción de setas silvestres, queso Paipa madurado 18 meses y aceite de romero.</p>
                    </div>
                </div>

                <!-- Columna 2: Mixología & Destilados -->
                <div class="origen-menu-col">
                    <div class="origen-menu-col__header">
                        <span class="origen-menu-col__num">02</span>
                        <h3>Mixología Botánica</h3>
                    </div>

                    <div class="origen-menu-item">
                        <div class="origen-menu-item__top">
                            <h4 class="origen-menu-item__name">Humo de Origen</h4>
                            <span class="origen-menu-item__price">$46.000</span>
                        </div>
                        <p class="origen-menu-item__desc">Bourbon añejo macerado con roble andino, bitter artesanal de cacao, vermouth rosso y campana de humo de romero.</p>
                        <span class="origen-menu-item__badge">Firma de la Casa</span>
                    </div>

                    <div class="origen-menu-item">
                        <div class="origen-menu-item__top">
                            <h4 class="origen-menu-item__name">Neblina Botánica</h4>
                            <span class="origen-menu-item__price">$42.000</span>
                        </div>
                        <p class="origen-menu-item__desc">Gin infusionado con cardamomo y hojas de eucalipto, reducción de uchuva fresca, tónica premium y esfera cristalina.</p>
                    </div>

                    <div class="origen-menu-item">
                        <div class="origen-menu-item__top">
                            <h4 class="origen-menu-item__name">Cacao & Mezcal Silvestre</h4>
                            <span class="origen-menu-item__price">$48.000</span>
                        </div>
                        <p class="origen-menu-item__desc">Mezcal espadín artesanal, licor de chile ancho, infusión de café Geisha de altura y tierra de chocolate amargo 85%.</p>
                    </div>
                </div>
            </div>

            <div class="origen-menu-footer text-center">
                <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20solicitar%20la%20carta%20completa." class="origen-btn-outline" target="_blank" rel="noopener">
                    <span>Consultar Carta Completa y Vinos en PDF</span>
                    <?php echo SVG::arrow_right(); // phpcs:ignore ?>
                </a>
            </div>
        </div>
    </section>

    <!-- 🥂 SECCIÓN 3: EVENTOS PRIVADOS & CORPORATIVOS -->
    <section id="eventos" class="origen-section">
        <div class="nandark-container">
            <div class="origen-events-card">
                <div class="origen-events-card__content">
                    <span class="scrolly-tag">Exclusividad & Buyouts</span>
                    <h2 class="origen-events-card__title">Celebraciones & Cenas Privadas</h2>
                    <div class="scrolly-line"></div>
                    <p class="origen-events-card__desc">Disponemos del espacio completo de terraza o salón privado para aniversarios, lanzamientos de marca y reuniones directivas con servicio a medida.</p>

                    <ul class="origen-events-list">
                        <li>
                            <span class="origen-events-list__icon"><?php echo SVG::check(); // phpcs:ignore ?></span>
                            <span>Capacidad modulable hasta 80 personas en formato cóctel o 45 en cena servida.</span>
                        </li>
                        <li>
                            <span class="origen-events-list__icon"><?php echo SVG::check(); // phpcs:ignore ?></span>
                            <span>Menú de degustación personalizado con maridaje guiado por sommelier.</span>
                        </li>
                        <li>
                            <span class="origen-events-list__icon"><?php echo SVG::check(); // phpcs:ignore ?></span>
                            <span>Equipo audiovisual, seguridad privada y servicio de valet parking exclusivo.</span>
                        </li>
                    </ul>

                    <div class="origen-events-cta">
                        <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20cotizar%20un%20evento%20privado." class="origen-btn-solid" target="_blank" rel="noopener">
                            <span class="origen-btn-solid__icon"><?php echo SVG::users(); // phpcs:ignore ?></span>
                            <span>Cotizar Evento Privado</span>
                            <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                        </a>
                    </div>
                </div>

                <div class="origen-events-card__media">
                    <img src="<?php echo esc_url($img_url . '04-terraza-noche.jpg'); ?>" alt="Eventos Privados en Origen" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- 📰 SECCIÓN 4: CRÍTICA & RECONOCIMIENTOS EDITORIALES -->
    <section class="origen-section origen-section--dark">
        <div class="nandark-container">
            <div class="origen-section-header">
                <span class="scrolly-tag">Prensa & Crítica</span>
                <h2 class="origen-section-title">Palabras de la Crítica</h2>
                <div class="origen-section-line"></div>
            </div>

            <div class="origen-reviews-grid">
                <blockquote class="origen-review-box">
                    <div class="origen-review-box__quote"><?php echo SVG::quote(); // phpcs:ignore ?></div>
                    <p class="origen-review-box__text">"Origen logra lo que pocos espacios en Bogotá consiguen: una transición perfecta entre alta cocina técnica y una terraza nocturna con verdadera identidad."</p>
                    <footer class="origen-review-box__author">
                        <strong>Guía Gastronómica Andina</strong>
                        <span>Crítica de Restaurantes · 2026</span>
                    </footer>
                </blockquote>

                <blockquote class="origen-review-box">
                    <div class="origen-review-box__quote"><?php echo SVG::quote(); // phpcs:ignore ?></div>
                    <p class="origen-review-box__text">"La barra de mármol y su mixología ahumada elevan el estándar de la coctelería local. Un punto de parada obligado en la escena nocturna."</p>
                    <footer class="origen-review-box__author">
                        <strong>Revista Sommelier & Bar</strong>
                        <span>Edición Colección</span>
                    </footer>
                </blockquote>

                <blockquote class="origen-review-box">
                    <div class="origen-review-box__quote"><?php echo SVG::quote(); // phpcs:ignore ?></div>
                    <p class="origen-review-box__text">"Un diseño arquitectónico impecable donde cada plato justifica su presencia en mesa. Servicio atento, silencioso y preciso."</p>
                    <footer class="origen-review-box__author">
                        <strong>Editorial Diners Club</strong>
                        <span>Selección de la Ciudad</span>
                    </footer>
                </blockquote>
            </div>
        </div>
    </section>

    <!-- 📍 SECCIÓN 5: INFORMACIÓN PRÁCTICA, HORARIOS & UBICACIÓN -->
    <section id="info" class="origen-section">
        <div class="nandark-container">
            <div class="origen-info-grid">
                
                <div class="origen-info-item">
                    <div class="origen-info-item__icon"><?php echo SVG::map_pin(); // phpcs:ignore ?></div>
                    <span class="scrolly-tag">Ubicación</span>
                    <h3 class="origen-info-item__title">Zona Gastronómica</h3>
                    <p class="origen-info-item__text">Calle 85 # 11 - 53, Piso 4 (Rooftop)<br>Bogotá, Colombia</p>
                    <span class="origen-info-item__note">Servicio de Valet Parking en recepción</span>
                </div>

                <div class="origen-info-item">
                    <div class="origen-info-item__icon"><?php echo SVG::clock(); // phpcs:ignore ?></div>
                    <span class="scrolly-tag">Horarios de Atención</span>
                    <h3 class="origen-info-item__title">Turnos de Servicio</h3>
                    <p class="origen-info-item__text">
                        <strong>Miércoles a Sábado:</strong> 5:00 PM &mdash; 2:00 AM<br>
                        <strong>Domingos:</strong> 1:00 PM &mdash; 9:00 PM
                    </p>
                    <span class="origen-info-item__note">Cocina abierta hasta las 11:30 PM</span>
                </div>

                <div class="origen-info-item">
                    <div class="origen-info-item__icon"><?php echo SVG::diamond(); // phpcs:ignore ?></div>
                    <span class="scrolly-tag">Código de Visita</span>
                    <h3 class="origen-info-item__title">Smart Casual</h3>
                    <p class="origen-info-item__text">Agradecemos vestir formal o casual elegante. Nos reservamos el derecho de admisión según aforo y disponibilidad.</p>
                    <span class="origen-info-item__note">Edad mínima para barra y rooftop: 18 años</span>
                </div>

            </div>
        </div>
    </section>

    <!-- 📲 STRIP FINAL DE CONVERSIÓN -->
    <section id="reservas" class="origen-booking-strip">
        <div class="nandark-container">
            <div class="origen-booking-box">
                <div class="origen-booking-box__info">
                    <span class="scrolly-tag scrolly-tag--accent">Atención Personalizada</span>
                    <h2>Planifica tu experiencia en Origen</h2>
                    <p>Recomendamos solicitar tu mesa con al menos 24 horas de antelación para garantizar disponibilidad en el turno deseado.</p>
                </div>
                <div class="origen-booking-box__cta">
                    <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20solicitar%20una%20reserva%20para%20este%20fin%20de%20semana." class="origen-btn-solid" target="_blank" rel="noopener">
                        <span class="origen-btn-solid__icon"><?php echo SVG::whatsapp(); // phpcs:ignore ?></span>
                        <span>Reservar por WhatsApp</span>
                        <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 🏛️ FOOTER EDITORIAL -->
    <footer class="origen-footer">
        <div class="nandark-container">
            <div class="origen-footer__top">
                <div class="origen-footer__brand">
                    <span class="origen-nav__logo-title">ORIGEN</span>
                    <span class="origen-nav__logo-sub">GASTROBAR & ROOFTOP LOUNGE</span>
                    <p class="origen-footer__brand-text">Gastronomía contemporánea, mixología de autor y experiencias privadas en el corazón de Bogotá.</p>
                </div>

                <div class="origen-footer__links-col">
                    <span class="scrolly-tag">Navegación</span>
                    <a href="#experiencias">Espacios</a>
                    <a href="#carta">La Carta</a>
                    <a href="#eventos">Eventos Privados</a>
                    <a href="#info">Horarios & Ubicación</a>
                </div>

                <div class="origen-footer__links-col">
                    <span class="scrolly-tag">Contacto</span>
                    <a href="https://wa.me/573000000000" target="_blank" rel="noopener">WhatsApp Directo</a>
                    <a href="mailto:reservas@origenbogota.com">reservas@origenbogota.com</a>
                    <p class="origen-footer__address">Calle 85 # 11 - 53, Bogotá</p>
                </div>
            </div>

            <div class="origen-footer__bottom">
                <p>&copy; <?php echo esc_html(gmdate('Y')); ?> ORIGEN Gastrobar. Todos los derechos reservados.</p>
                <p class="origen-footer__credit">Desarrollado bajo arquitectura <a href="https://github.com/JonathanVargas0111/nandark-atomic-wp" target="_blank" rel="noopener">Nandark Atomic Core</a></p>
            </div>
        </div>
    </footer>

</main>

<?php
get_footer();
