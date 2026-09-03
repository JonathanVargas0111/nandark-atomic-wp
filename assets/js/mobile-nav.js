(function () {
  'use strict';

  // 1. Manejador del Fullscreen Curtain Drawer Móvil
  const initMobileDrawer = () => {
    const toggleBtn = document.getElementById('origen-menu-toggle');
    const closeBtn = document.getElementById('origen-drawer-close');
    const drawer = document.getElementById('origen-drawer');
    const backdrop = document.querySelector('.origen-drawer__backdrop');
    const drawerLinks = document.querySelectorAll('.origen-drawer__link, .origen-drawer__btn');

    if (!toggleBtn || !drawer) return;

    const openDrawer = () => {
      drawer.classList.add('is-open');
      toggleBtn.classList.add('is-active');
      toggleBtn.setAttribute('aria-expanded', 'true');
      drawer.setAttribute('aria-hidden', 'false');
      document.body.classList.add('origen-drawer-locked');
    };

    const closeDrawer = () => {
      drawer.classList.remove('is-open');
      toggleBtn.classList.remove('is-active');
      toggleBtn.setAttribute('aria-expanded', 'false');
      drawer.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('origen-drawer-locked');
    };

    toggleBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (drawer.classList.contains('is-open')) {
        closeDrawer();
      } else {
        openDrawer();
      }
    });

    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (backdrop) backdrop.addEventListener('click', closeDrawer);

    drawerLinks.forEach((link) => {
      link.addEventListener('click', closeDrawer);
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
        closeDrawer();
      }
    });
  };

  // 2. Componente React: Calculadora Interactiva de Cenas & Eventos
  const initReactBookingCalculator = () => {
    const rootEl = document.getElementById('origen-react-booking-root');
    if (!rootEl) return;

    const { createElement: h, useState } = window.wp && window.wp.element ? window.wp.element : (window.React || {});
    const render = (window.wp && window.wp.element && window.wp.element.render) 
      ? window.wp.element.render 
      : (window.ReactDOM ? window.ReactDOM.render : null);

    if (!h || !render) {
      console.warn('React o wp.element no disponible en runtime.');
      return;
    }

    const BookingCalculator = () => {
      const [guests, setGuests] = useState(4);
      const [experience, setExperience] = useState('degustacion'); // 'degustacion' | 'maridaje' | 'rooftop'
      const [date, setDate] = useState('viernes');

      const prices = {
        degustacion: 180000,
        maridaje: 260000,
        rooftop: 140000,
      };

      const experienceNames = {
        degustacion: 'Menú Degustación 6 Pasos',
        maridaje: 'Experiencia con Maridaje de Alta Gama',
        rooftop: 'Cócteles & Tapas en Terraza Rooftop',
      };

      const baseTotal = prices[experience] * guests;
      const formattedTotal = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
      }).format(baseTotal);

      const whatsappText = encodeURIComponent(
        `Hola Origen, coticé en la web una reserva para ${guests} personas (${experienceNames[experience]}) para el día ${date}. Deseo verificar disponibilidad.`
      );

      return h(
        'div',
        { className: 'origen-calc-card' },
        h(
          'div',
          { className: 'origen-calc-header' },
          h('span', { className: 'scrolly-tag scrolly-tag--accent' }, 'Experiencia Interactiva · En Vivo'),
          h('h3', { className: 'origen-calc-title' }, 'Cotizador de Veladas & Reservas VIP'),
          h(
            'p',
            { className: 'origen-calc-subtitle' },
            'Selecciona el número de comensales y el formato gastronómico para calcular al instante tu presupuesto estimado.'
          )
        ),
        h(
          'div',
          { className: 'origen-calc-grid' },
          // Selector de comensales
          h(
            'div',
            { className: 'origen-calc-control' },
            h('label', { className: 'origen-calc-label' }, `Número de Personas: ${guests}`),
            h('input', {
              type: 'range',
              min: 2,
              max: 20,
              step: 1,
              value: guests,
              onChange: (e) => setGuests(parseInt(e.target.value, 10)),
              className: 'origen-calc-range',
            }),
            h(
              'div',
              { className: 'origen-calc-range-ticks' },
              h('span', null, '2 pers.'),
              h('span', null, '10 pers.'),
              h('span', null, '20 pers.')
            )
          ),
          // Selector de experiencia
          h(
            'div',
            { className: 'origen-calc-control' },
            h('label', { className: 'origen-calc-label' }, 'Tipo de Experiencia:'),
            h(
              'div',
              { className: 'origen-calc-options' },
              [
                { id: 'degustacion', label: 'Degustación 6 Tiempos', price: '$180.000 / p' },
                { id: 'maridaje', label: 'Maridaje & Sommelier', price: '$260.000 / p' },
                { id: 'rooftop', label: 'Rooftop Cócteles & Fuego', price: '$140.000 / p' },
              ].map((opt) =>
                h(
                  'button',
                  {
                    key: opt.id,
                    type: 'button',
                    className: `origen-calc-btn-opt ${experience === opt.id ? 'is-active' : ''}`,
                    onClick: () => setExperience(opt.id),
                  },
                  h('span', { className: 'origen-calc-opt-name' }, opt.label),
                  h('span', { className: 'origen-calc-opt-price' }, opt.price)
                )
              )
            )
          )
        ),
        // Resumen y botón directo
        h(
          'div',
          { className: 'origen-calc-footer' },
          h(
            'div',
            { className: 'origen-calc-total-box' },
            h('span', { className: 'origen-calc-total-label' }, 'Inversión Estimada Total:'),
            h('span', { className: 'origen-calc-total-val' }, formattedTotal),
            h('span', { className: 'origen-calc-total-note' }, `(${guests} comensales · ${experienceNames[experience]})`)
          ),
          h(
            'a',
            {
              href: `https://wa.me/573000000000?text=${whatsappText}`,
              target: '_blank',
              rel: 'noopener noreferrer',
              className: 'origen-btn-solid origen-calc-submit',
            },
            h('span', null, 'Apartar Mesa con esta Cotización'),
            h('span', { className: 'origen-btn-solid__arrow', dangerouslySetInnerHTML: { __html: '&rarr;' } })
          )
        )
      );
    };

    render(h(BookingCalculator, null), rootEl);
  };

  // Inicializar en DOM listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initMobileDrawer();
      initReactBookingCalculator();
    });
  } else {
    initMobileDrawer();
    initReactBookingCalculator();
  }
})();
