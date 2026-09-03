<?php
/**
 * Plantilla Template: Page Home — Origen Gastrobar
 * Arquitectura 100% Atómica y Modular
 */
get_header();

$img_url    = NANDARK_ATOMIC_URL . 'assets/images/';
$frames_url = NANDARK_ATOMIC_URL . 'assets/frames/';
?>

<main class="nandark-main nandark-home-page">
    
    <!-- Navbar Organism -->
    <?php nandark_render('organisms/navbar'); ?>

    <!-- Scrollytelling Hero Organism -->
    <?php nandark_render('organisms/scrollytelling-hero', ['frames_url' => $frames_url]); ?>

    <!-- Espacios Organism -->
    <?php nandark_render('organisms/spaces-grid', ['img_url' => $img_url]); ?>

    <!-- Menú Interactivo Organism -->
    <?php nandark_render('organisms/menu-grid'); ?>

    <!-- Eventos Privados Organism -->
    <?php nandark_render('organisms/events-card', ['img_url' => $img_url]); ?>

    <!-- Reseñas Prensa Organism -->
    <?php nandark_render('organisms/reviews-section'); ?>

    <!-- Información Práctica Organism -->
    <?php nandark_render('organisms/practical-info'); ?>

    <!-- Strip Reserva Organism -->
    <?php nandark_render('organisms/booking-strip'); ?>

    <!-- Footer Editorial Organism -->
    <?php nandark_render('organisms/footer-editorial'); ?>

</main>

<?php
get_footer();
