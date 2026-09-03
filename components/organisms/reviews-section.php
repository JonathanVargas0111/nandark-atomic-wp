<?php
/**
 * Organismo: Palabras de la Crítica Gastronómica
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;
?>
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
