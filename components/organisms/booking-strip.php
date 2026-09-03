<?php
/**
 * Organismo: Strip de Reserva Final & Cotizador React VIP
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;
?>
<section id="reservas" class="origen-booking-strip">
    <div class="nandark-container">
        <!-- ⚛️ Montura Interactiva de React -->
        <div id="origen-react-booking-root" class="origen-react-booking-wrapper"></div>

        <!-- Banner de Contacto Rápido -->
        <div class="origen-booking-box">
            <div class="origen-booking-box__info">
                <span class="scrolly-tag scrolly-tag--accent">Atención Inmediata</span>
                <h2>¿Tienes una solicitud especial o grupo grande?</h2>
                <p>Escríbenos directamente a nuestra línea exclusiva de concierges en Bogotá. Te responderemos en menos de 15 minutos.</p>
            </div>
            <div class="origen-booking-box__cta">
                <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20tengo%20una%20consulta%20especial%20para%20una%20reserva." class="origen-btn-solid" target="_blank" rel="noopener">
                    <span class="origen-btn-solid__icon"><?php echo SVG::whatsapp(); // phpcs:ignore ?></span>
                    <span>Chat Directo por WhatsApp</span>
                    <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                </a>
            </div>
        </div>
    </div>
</section>
