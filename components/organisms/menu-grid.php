<?php
/**
 * Organismo: Menú de Temporada con Filtro de Categorías
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;
?>
<section id="carta" class="origen-section origen-section--dark">
    <div class="nandark-container">
        <div class="origen-section-header">
            <span class="scrolly-tag">Menú de Temporada</span>
            <h2 class="origen-section-title">Creaciones Seleccionadas</h2>
            <div class="origen-section-line"></div>
            <p class="origen-section-desc">Ingredientes de origen trazable, técnicas de maduración propia y recetas diseñadas por nuestro chef ejecutivo.</p>
            
            <!-- Tabs Interactivos -->
            <div class="origen-menu-tabs" role="tablist">
                <button type="button" class="origen-tab-btn is-active" data-filter="all">Toda la Carta</button>
                <button type="button" class="origen-tab-btn" data-filter="cocina">Cocina de Fuego</button>
                <button type="button" class="origen-tab-btn" data-filter="mixologia">Mixología Botánica</button>
            </div>
        </div>

        <div class="origen-menu-grid">
            <!-- Columna 1: Cocina -->
            <div class="origen-menu-col" data-col="cocina">
                <div class="origen-menu-col__header">
                    <span class="origen-menu-col__num">01</span>
                    <h3>Cocina de Fuego & Autor</h3>
                </div>

                <?php
                nandark_render('molecules/menu-item', [
                    'name'     => 'Ojo de Bife Madurado 45 Días',
                    'price'    => '$115.000',
                    'desc'     => '400g de corte premium a la brasa de quebracho, mantequilla de tuétano y sal marina de Manaure.',
                    'badge'    => 'Recomendación del Chef',
                    'category' => 'cocina',
                ]);
                nandark_render('molecules/menu-item', [
                    'name'     => 'Tartar de Atún Rojo & Trufa Negra',
                    'price'    => '$68.000',
                    'desc'     => 'Atún aleta amarilla cortado a cuchillo, emulsión de trufa silvestre, yema curada y láminas de wonton crujiente.',
                    'category' => 'cocina',
                ]);
                nandark_render('molecules/menu-item', [
                    'name'     => 'Risotto de Hongos Andinos & Médula',
                    'price'    => '$74.000',
                    'desc'     => 'Arroz carnaroli con reducción de setas silvestres, queso Paipa madurado 18 meses y aceite de romero.',
                    'category' => 'cocina',
                ]);
                ?>
            </div>

            <!-- Columna 2: Mixología -->
            <div class="origen-menu-col" data-col="mixologia">
                <div class="origen-menu-col__header">
                    <span class="origen-menu-col__num">02</span>
                    <h3>Mixología Botánica</h3>
                </div>

                <?php
                nandark_render('molecules/menu-item', [
                    'name'     => 'Humo de Origen',
                    'price'    => '$46.000',
                    'desc'     => 'Bourbon añejo macerado con roble andino, bitter artesanal de cacao, vermouth rosso y campana de humo de romero.',
                    'badge'    => 'Firma de la Casa',
                    'category' => 'mixologia',
                ]);
                nandark_render('molecules/menu-item', [
                    'name'     => 'Neblina Botánica',
                    'price'    => '$42.000',
                    'desc'     => 'Gin infusionado con cardamomo y hojas de eucalipto, reducción de uchuva fresca, tónica premium y esfera cristalina.',
                    'category' => 'mixologia',
                ]);
                nandark_render('molecules/menu-item', [
                    'name'     => 'Cacao & Mezcal Silvestre',
                    'price'    => '$48.000',
                    'desc'     => 'Mezcal espadín artesanal, licor de chile ancho, infusión de café Geisha de altura y tierra de chocolate amargo 85%.',
                    'category' => 'mixologia',
                ]);
                ?>
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
