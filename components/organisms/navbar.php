<?php
/**
 * Organismo: Navbar Editorial
 */
require_once NANDARK_ATOMIC_PATH . 'components/atoms/svg-icons.php';
use NandarkAtomic\Icons\SVG;
?>
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
