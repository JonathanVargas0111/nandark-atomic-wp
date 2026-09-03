<?php
/**
 * Organismo: Scrollytelling Section
 * Props:
 * - frames_url (string)
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;

$frames_url = $frames_url ?? (NANDARK_ATOMIC_URL . 'assets/frames/');
?>
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
