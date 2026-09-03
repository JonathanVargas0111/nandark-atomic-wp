<?php
/**
 * Organismo: Eventos Privados & Buyouts
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;

$img_url = $img_url ?? NANDARK_ATOMIC_URL . 'assets/images/';
?>
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
