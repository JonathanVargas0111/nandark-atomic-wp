<?php
/**
 * Plantilla Template: Page Home — Clínica Déntica by Cristina Suaza
 * Arquitectura 100% Atómica y Modular
 */
get_header();
?>

<div class="dentica-wrapper">
    <header class="dentica-header">
        <div class="dentica-container dentica-header__inner">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="dentica-brand">
                <h1 class="dentica-brand__title">DÉNTICA</h1>
                <span class="dentica-brand__sub">by Cristina Suaza</span>
            </a>
            <nav>
                <a href="https://wa.me/573023688788?text=Hola%20D%C3%A9ntica%2C%20quiero%20solicitar%20una%20valoraci%C3%B3n." class="dentica-btn-wa" target="_blank" rel="noopener">
                    <span>Agendar Valoración</span>
                    <span>→</span>
                </a>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="dentica-hero">
            <div class="dentica-container">
                <div class="dentica-badge">
                    <span>★ 4.9 en Google Maps (48 reseñas)</span>
                    <span>·</span>
                    <span>Bogotá (Virrey / Chico)</span>
                </div>
                <h2 class="dentica-hero__title">La odontología estética no tiene que parecer artificial.</h2>
                <p class="dentica-hero__desc">Diseñamos sonrisas naturales, proporcionales y armónicas con los rasgos de tu rostro. Sin desgastes innecesarios ni resultados genéricos.</p>
                <div class="dentica-hero__actions">
                    <a href="https://wa.me/573023688788?text=Hola%20D%C3%A9ntica%2C%20quiero%20solicitar%20una%20valoraci%C3%B3n%20con%20la%20Dra.%20Cristina%20Suaza." class="dentica-btn-wa" target="_blank" rel="noopener" style="padding: 14px 28px; font-size: 15px;">
                        <span>Agendar Cita en WhatsApp</span>
                        <span>→</span>
                    </a>
                </div>
                <div class="dentica-hero__rating">
                    Atención personalizada por la <strong>Dra. Cristina Suaza</strong> y especialistas en rehabilitación oral.
                </div>
            </div>
        </section>

        <!-- Tratamientos Especializados -->
        <section class="dentica-section">
            <div class="dentica-container">
                <div class="dentica-section-header">
                    <h3 class="dentica-section-title">Especialidades Clínicas</h3>
                    <p class="dentica-section-subtitle">Tratamientos odontológicos planificados digitalmente con tecnología de mínima intervención.</p>
                </div>

                <div class="dentica-grid">
                    <article class="dentica-card">
                        <div class="dentica-card__num">01</div>
                        <h4 class="dentica-card__title">Diseño de Sonrisa Natural</h4>
                        <p class="dentica-card__text">Carillas cerámicas de alta precisión y resinas estratificadas. Evaluamos textura, translucidez y función masticatoria para que nadie note que tienes carillas.</p>
                    </article>

                    <article class="dentica-card">
                        <div class="dentica-card__num">02</div>
                        <h4 class="dentica-card__title">Alineadores Invisibles</h4>
                        <p class="dentica-card__text">Ortodoncia digital para adultos sin brackets metálicos. Placas transparentes y cómodas que corrigen apiñamientos y espacios con discreción total.</p>
                    </article>

                    <article class="dentica-card">
                        <div class="dentica-card__num">03</div>
                        <h4 class="dentica-card__title">Rehabilitación & Implantes</h4>
                        <p class="dentica-card__text">Recuperación biológica de piezas perdidas mediante titanio biocompatible y coronas de circonio. Devolvemos la fuerza oclusal y la estabilidad ósea.</p>
                    </article>

                    <article class="dentica-card">
                        <div class="dentica-card__num">04</div>
                        <h4 class="dentica-card__title">Blanqueamiento Clínico</h4>
                        <p class="dentica-card__text">Aclaramiento dental seguro en consultorio. Protocolos con barrera gingival y desensibilizantes que protegen el esmalte sin dolor post-operatorio.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Dra. Cristina Suaza / Filosofía -->
        <section class="dentica-about">
            <div class="dentica-container dentica-about__grid">
                <div>
                    <span class="dentica-brand__sub" style="display:block; margin-bottom: 12px;">Filosofía Clínica</span>
                    <blockquote class="dentica-about__quote">
                        "Mi compromiso no es poner dientes blancos y rectos como teclas de piano; es devolver la armonía, la salud y la seguridad al sonreír."
                    </blockquote>
                    <p style="color: var(--dentica-muted); font-size: 15px; margin: 0;">
                        <strong>Dra. Cristina Suaza</strong><br>
                        Especialista en Estética Dental y Rehabilitación Oral.<br>
                        Directora Clínica en Déntica Colombia.
                    </p>
                </div>
                <div style="background: var(--dentica-bg); border: 1px solid var(--dentica-border); border-radius: 12px; padding: 32px;">
                    <h4 style="font-size: 18px; margin: 0 0 16px;">Ubicación & Consulta Privada</h4>
                    <p style="font-size: 14px; color: var(--dentica-muted); line-height: 1.6; margin: 0 0 16px;">
                        <strong>Consultorio:</strong> Cra. 19a #82 - 85, Oficina 201<br>
                        Edificio Médico Virrey · Bogotá, Colombia<br>
                        <strong>Horarios:</strong> Lunes a Viernes 8:00 AM – 6:00 PM | Sábados con cita previa.
                    </p>
                    <div style="font-size: 13px; color: var(--dentica-teal); font-weight: 600;">
                        Parqueadero privado para pacientes y fácil acceso desde la Calle 85.
                    </div>
                </div>
            </div>
        </section>

        <!-- Banner Final de Conversión -->
        <section class="dentica-container">
            <div class="dentica-cta-box">
                <h3 class="dentica-cta-box__title">Comienza con una valoración diagnóstica</h3>
                <p class="dentica-cta-box__desc">Evaluamos tu estructura dental con fotografías y escaneo digital. Te explicamos exactamente qué necesitas sin compromisos.</p>
                <a href="https://wa.me/573023688788?text=Hola%20Dra.%20Cristina%2C%20quiero%20agendar%20una%20valoraci%C3%B3n%20en%20D%C3%A9ntica." class="dentica-btn-wa" target="_blank" rel="noopener" style="padding: 14px 32px; font-size: 15px; background: #ffffff; color: var(--dentica-slate);">
                    <span>Escribir al WhatsApp de Déntica</span>
                    <span>→</span>
                </a>
            </div>
        </section>
    </main>

    <footer class="dentica-footer">
        <div class="dentica-container dentica-footer__grid">
            <div>
                <strong>Clínica Déntica by Cristina Suaza</strong> · Odontología Especializada Bogotá
            </div>
            <div>
                Cra. 19a #82 - 85 Of. 201 · WhatsApp: +57 302 3688788
            </div>
        </div>
    </footer>
</div>

<?php
get_footer();
