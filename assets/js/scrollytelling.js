/**
 * Scrollytelling — activación de pasos por scroll.
 *
 * El recorrido de tarjetas se mantiene igual que siempre. Lo que se sacó es el
 * motor de frames: antes este archivo precargaba 240 JPGs y los iba dibujando en
 * un <canvas> para simular un video, ~55 MB por visitante. Ahora el fondo es una
 * sola imagen fija en HTML y este script solo decide qué tarjeta está activa.
 *
 * De ~200 líneas con canvas, RAF de dibujo y precarga por lotes, a esto.
 */
(function () {
  'use strict';

  const initScrolly = () => {
    const container = document.getElementById('scrollytelling-container');
    if (!container) return;

    const steps = container.querySelectorAll('.scrolly-step');
    if (!steps.length) return;

    let isTicking = false;

    const update = () => {
      const rect = container.getBoundingClientRect();
      const maxScroll = rect.height - window.innerHeight;
      const progress = maxScroll > 0
        ? Math.min(Math.max(-rect.top / maxScroll, 0), 1)
        : 0;

      steps.forEach((step) => {
        const start = parseFloat(step.dataset.start || 0);
        const end = parseFloat(step.dataset.end || 1);
        step.classList.toggle('is-active', progress >= start && progress <= end);
      });

      isTicking = false;
    };

    const onScroll = () => {
      if (isTicking) return;
      isTicking = true;
      window.requestAnimationFrame(update);
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    update();
  };

  // Tabs de filtrado de la carta.
  const initMenuTabs = () => {
    const tabBtns = document.querySelectorAll('.origen-tab-btn');
    const menuCols = document.querySelectorAll('.origen-menu-col');
    if (!tabBtns.length) return;

    tabBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        tabBtns.forEach((b) => b.classList.remove('is-active'));
        btn.classList.add('is-active');

        const filter = btn.dataset.filter || 'all';

        menuCols.forEach((col) => {
          const colType = col.dataset.col;
          col.classList.toggle('is-hidden', filter !== 'all' && filter !== colType);
        });
      });
    });
  };

  const boot = () => {
    initScrolly();
    initMenuTabs();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
