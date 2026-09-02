(function () {
  'use strict';

  const container = document.getElementById('scrollytelling-container');
  const canvas = document.getElementById('scrollytelling-canvas');
  if (!container || !canvas) return;

  const ctx = canvas.getContext('2d');
  const frameCount = 240;
  const frames = [];
  let currentFrameIndex = 0;
  let isCanvasReady = false;

  // URL base de los fotogramas (pasada desde PHP via data-attribute)
  const framesBaseUrl = container.dataset.framesUrl || '';

  const getFrameUrl = (index) => {
    const pad = String(index).padStart(4, '0');
    return `${framesBaseUrl}frame_${pad}.jpg`;
  };

  // Pre-cargar imágenes progresivamente
  const preloadImages = () => {
    for (let i = 1; i <= frameCount; i++) {
      const img = new Image();
      img.src = getFrameUrl(i);
      if (i === 1) {
        img.onload = () => {
          isCanvasReady = true;
          resizeCanvas();
          renderFrame(0);
        };
      }
      frames.push(img);
    }
  };

  // Ajustar tamaño del canvas con soporte Retina
  const resizeCanvas = () => {
    const dpr = window.devicePixelRatio || 1;
    canvas.width = window.innerWidth * dpr;
    canvas.height = window.innerHeight * dpr;
    ctx.scale(dpr, dpr);
    renderFrame(currentFrameIndex);
  };

  // Dibujar el fotograma manteniendo proporción (cover)
  const renderFrame = (index) => {
    const img = frames[index];
    if (!img || !img.complete) return;

    const w = window.innerWidth;
    const h = window.innerHeight;

    // Cover math
    const imgRatio = 1920 / 1080;
    const screenRatio = w / h;
    let drawW, drawH, drawX, drawY;

    if (screenRatio > imgRatio) {
      drawW = w;
      drawH = w / imgRatio;
      drawX = 0;
      drawY = (h - drawH) / 2;
    } else {
      drawH = h;
      drawW = h * imgRatio;
      drawX = (w - drawW) / 2;
      drawY = 0;
    }

    ctx.clearRect(0, 0, w, h);
    ctx.drawImage(img, drawX, drawY, drawW, drawH);
  };

  // Control de scroll y sincronización
  const updateScroll = () => {
    const rect = container.getBoundingClientRect();
    const scrollProgress = Math.min(
      Math.max(-rect.top / (rect.height - window.innerHeight), 0),
      1
    );

    const targetFrame = Math.min(
      Math.floor(scrollProgress * (frameCount - 1)),
      frameCount - 1
    );

    if (targetFrame !== currentFrameIndex) {
      currentFrameIndex = targetFrame;
      requestAnimationFrame(() => renderFrame(currentFrameIndex));
    }

    // Actualizar visibilidad de los pasos de texto narrativo
    const steps = container.querySelectorAll('.scrolly-step');
    steps.forEach((step) => {
      const stepStart = parseFloat(step.dataset.start || 0);
      const stepEnd = parseFloat(step.dataset.end || 1);
      if (scrollProgress >= stepStart && scrollProgress <= stepEnd) {
        step.classList.add('is-active');
      } else {
        step.classList.remove('is-active');
      }
    });
  };

  window.addEventListener('resize', resizeCanvas);
  window.addEventListener('scroll', updateScroll, { passive: true });

  preloadImages();
})();
