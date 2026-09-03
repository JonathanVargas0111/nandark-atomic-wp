<?php
/**
 * Organismo: Información Práctica & Horarios
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;
?>
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
