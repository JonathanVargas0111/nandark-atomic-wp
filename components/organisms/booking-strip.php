<?php
/**
 * Organismo: Strip de Reserva Final
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;
?>
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
