<?php
/**
 * Organismo: Navbar Editorial Mobile-First & Fullscreen Curtain Drawer
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

        <!-- Botón Toggle Móvil (Minimalista con palabra 'MENÚ' y dos líneas de compás) -->
        <button type="button" class="origen-nav__toggle" id="origen-menu-toggle" aria-label="Abrir menú" aria-expanded="false">
            <span class="origen-nav__toggle-text">Menú</span>
            <span class="origen-nav__toggle-lines" aria-hidden="true">
                <span class="origen-nav__line origen-nav__line--top"></span>
                <span class="origen-nav__line origen-nav__line--bot"></span>
            </span>
        </button>

        <!-- Navegación de Escritorio -->
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

    <!-- 🎭 FULLSCREEN CURTAIN DRAWER MÓVIL (Cortina Teatral de Arriba a Abajo) -->
    <div class="origen-drawer" id="origen-drawer" aria-hidden="true">
        <div class="origen-drawer__backdrop"></div>
        <div class="origen-drawer__panel">
            <div class="origen-drawer__top">
                <span class="origen-drawer__brand">ORIGEN BOGOTÁ</span>
                <button type="button" class="origen-drawer__close" id="origen-drawer-close" aria-label="Cerrar menú">
                    <span>Cerrar</span>
                    <?php echo SVG::close(); // phpcs:ignore ?>
                </button>
            </div>

            <nav class="origen-drawer__nav">
                <a href="#experiencias" class="origen-drawer__link">
                    <span class="origen-drawer__link-num">01</span>
                    <span class="origen-drawer__link-title">Espacios & Atmósfera</span>
                </a>
                <a href="#carta" class="origen-drawer__link">
                    <span class="origen-drawer__link-num">02</span>
                    <span class="origen-drawer__link-title">Carta de Temporada</span>
                </a>
                <a href="#eventos" class="origen-drawer__link">
                    <span class="origen-drawer__link-num">03</span>
                    <span class="origen-drawer__link-title">Eventos & Cenas Privadas</span>
                </a>
                <a href="#info" class="origen-drawer__link">
                    <span class="origen-drawer__link-num">04</span>
                    <span class="origen-drawer__link-title">Ubicación & Horarios</span>
                </a>
            </nav>

            <div class="origen-drawer__footer">
                <a href="https://wa.me/573000000000?text=Hola%20Origen%2C%20quiero%20solicitar%20una%20reserva." class="origen-btn-solid origen-drawer__btn" target="_blank" rel="noopener">
                    <span class="origen-btn-solid__icon"><?php echo SVG::whatsapp(); // phpcs:ignore ?></span>
                    <span>Reservar Mesa por WhatsApp</span>
                    <span class="origen-btn-solid__arrow"><?php echo SVG::arrow_right(); // phpcs:ignore ?></span>
                </a>
                <div class="origen-drawer__contact">
                    <p>Calle 85 # 11 - 53, Piso 4 · Bogotá</p>
                    <p>Miércoles a Domingo desde las 5:00 PM</p>
                </div>
            </div>
        </div>
    </div>
</header>
