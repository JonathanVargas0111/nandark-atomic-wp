<?php
/**
 * Organismo: Scrollytelling Section.
 *
 * El recorrido por pasos (4 tarjetas que cambian con el scroll) SE MANTIENE.
 * Lo que cambió es el fondo: antes era un canvas que iba dibujando 240 JPGs
 * — ~55 MB por visitante — y ahora es UNA sola imagen fija detrás de las
 * tarjetas. Misma narrativa, 210 veces menos peso.
 *
 * Props:
 * - image_id  (int)    Attachment del fondo; por defecto lo resuelve nandark_hero_image()
 * - image_url (string) URL directa, si no hay attachment
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;

// El script se registra en Assets_Loader y se encola acá: así solo viaja a las
// páginas donde este organismo realmente se dibuja.
if (function_exists('wp_enqueue_script')) {
    wp_enqueue_script('nandark-scrollytelling');
}

$nandark_hero = function_exists('nandark_hero_image') ? nandark_hero_image() : ['id' => null, 'url' => ''];
$image_id     = isset($image_id) ? (int) $image_id : $nandark_hero['id'];
$image_url    = isset($image_url) ? $image_url : $nandark_hero['url'];
?>
<section id="scrollytelling-container" class="scrolly-section">
    <div class="scrolly-sticky">
        <div class="scrolly-backdrop" aria-hidden="true">
            <?php
            if ($image_id) {
                // srcset/sizes: en móvil baja la variante chica, no la de 1920px.
                echo wp_get_attachment_image($image_id, 'full', false, [
                    'class'         => 'scrolly-backdrop__img',
                    'alt'           => '',
                    'fetchpriority' => 'high',
                    'decoding'      => 'async',
                    'sizes'         => '100vw',
                ]);
            } elseif ($image_url) {
                printf(
                    '<img class="scrolly-backdrop__img" src="%s" alt="" fetchpriority="high" decoding="async">',
                    esc_url($image_url)
                );
            }
            ?>
        </div>
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
