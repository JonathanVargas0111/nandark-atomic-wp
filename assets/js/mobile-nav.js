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

    // Barra de navegación flotante con efecto cristal al scroll
    const navEl = document.querySelector('.origen-nav');
    if (navEl) {
      const handleNavScroll = () => {
        if (window.scrollY > 80) {
          navEl.classList.add('is-scrolled');
        } else {
          navEl.classList.remove('is-scrolled');
        }
      };
      window.addEventListener('scroll', handleNavScroll, { passive: true });
      handleNavScroll();
    }
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
      const [turn, setTurn] = useState('cena'); // 'almuerzo' | 'cena'
      const [sommelierAddon, setSommelierAddon] = useState(false);

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

      const sommelierPricePerPerson = 45000;
      const calculatedPersonPrice = prices[experience] + (sommelierAddon ? sommelierPricePerPerson : 0);
      const baseTotal = calculatedPersonPrice * guests;

      const formattedTotal = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
      }).format(baseTotal);

      const addonText = sommelierAddon ? ' + Cava Privada de Vinos' : '';
      const turnName = turn === 'cena' ? 'Turno Noche (7:30 PM)' : 'Turno Tarde (1:00 PM)';

      const whatsappText = encodeURIComponent(
        `Hola Origen, coticé en la web una reserva para ${guests} personas (${experienceNames[experience]}${addonText}) en ${turnName}. Deseo verificar disponibilidad de mesa.`
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
            'Selecciona el turno, número de comensales y formato gastronómico para calcular al instante tu presupuesto estimado.'
          )
        ),
        h(
          'div',
          { className: 'origen-calc-grid' },
          // Columna 1: Turno y Comensales
          h(
            'div',
            { className: 'origen-calc-col-left' },
            // Selector de turno
            h(
              'div',
              { className: 'origen-calc-control' },
              h('label', { className: 'origen-calc-label' }, 'Turno de Servicio:'),
              h(
                'div',
                { className: 'origen-calc-turns' },
                [
                  { id: 'almuerzo', label: 'Tarde · 1:00 PM' },
                  { id: 'cena', label: 'Noche · 7:30 PM' },
                ].map((t) =>
                  h(
                    'button',
                    {
                      key: t.id,
                      type: 'button',
                      className: `origen-calc-turn-btn ${turn === t.id ? 'is-active' : ''}`,
                      onClick: () => setTurn(t.id),
                    },
                    t.label
                  )
                )
              )
            ),
            // Slider de comensales
            h(
              'div',
              { className: 'origen-calc-control', style: { marginTop: '1.75rem' } },
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
            )
          ),
          // Columna 2: Selector de experiencia y Extra Sommelier
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
            ),
            // Checkbox Addon Sommelier
            h(
              'label',
              { className: 'origen-calc-addon' },
              h('input', {
                type: 'checkbox',
                checked: sommelierAddon,
                onChange: (e) => setSommelierAddon(e.target.checked),
                className: 'origen-calc-addon-check',
              }),
              h(
                'div',
                { className: 'origen-calc-addon-info' },
                h('span', { className: 'origen-calc-addon-title' }, 'Acceso a Cava Privada de Vinos'),
                h('span', { className: 'origen-calc-addon-price' }, '+$45.000 COP / persona')
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
            h('span', { className: 'origen-calc-total-note' }, `(${guests} comensales · ${experienceNames[experience]}${addonText})`)
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
