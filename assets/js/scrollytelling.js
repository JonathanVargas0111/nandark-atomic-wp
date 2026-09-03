(function () {
  'use strict';

  const container = document.getElementById('scrollytelling-container');
  const canvas = document.getElementById('scrollytelling-canvas');
  if (!container || !canvas) return;

  const ctx = canvas.getContext('2d', { alpha: false });
  const frameCount = 240;
  const frames = new Array(frameCount);
  let currentFrameIndex = 0;
  let targetFrameIndex = 0;
  let isTicking = false;

  const framesBaseUrl = container.dataset.framesUrl || '';

  const getFrameUrl = (index) => {
    const pad = String(index).padStart(4, '0');
    return `${framesBaseUrl}frame_${pad}.jpg`;
  };

  const loadSingleFrame = (index) => {
    return new Promise((resolve) => {
      if (frames[index] && frames[index].complete) {
        return resolve(frames[index]);
      }
      const img = new Image();
      img.decoding = 'async';
      img.src = getFrameUrl(index + 1);
      img.onload = () => {
        frames[index] = img;
        resolve(img);
      };
      img.onerror = () => resolve(null);
    });
  };

  // Pre-carga progresiva en 3 fases:
  // Fase 1: Primer frame inmediato
  // Fase 2: Muestreo de cada 5 frames (recorrido suave mientras baja el resto)
  // Fase 3: Relleno en background usando requestIdleCallback
  const preloadProgressive = async () => {
    await loadSingleFrame(0);
    resizeCanvas();
    renderFrame(0);

    const keyframes = [];
    for (let i = 0; i < frameCount; i += 5) {
      keyframes.push(loadSingleFrame(i));
    }
    await Promise.all(keyframes);

    const loadRemaining = (startIdx = 0) => {
      let i = startIdx;
      const batchSize = 10;
      while (i < frameCount && i < startIdx + batchSize) {
        if (!frames[i]) loadSingleFrame(i);
        i++;
      }
      if (i < frameCount) {
        if ('requestIdleCallback' in window) {
          window.requestIdleCallback(() => loadRemaining(i));
        } else {
          setTimeout(() => loadRemaining(i), 16);
        }
      }
    };

    loadRemaining(0);
  };

  let cachedWidth = 0;
  let cachedHeight = 0;

  const resizeCanvas = () => {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    cachedWidth = window.innerWidth;
    cachedHeight = window.innerHeight;
    canvas.width = cachedWidth * dpr;
    canvas.height = cachedHeight * dpr;
    ctx.scale(dpr, dpr);
    renderFrame(currentFrameIndex);
  };

  const renderFrame = (index) => {
    let img = frames[index];
    if (!img || !img.complete) {
      // Si el frame exacto no ha cargado, buscar el frame más cercano disponible
      for (let offset = 1; offset < 20; offset++) {
        if (frames[index - offset] && frames[index - offset].complete) {
          img = frames[index - offset];
          break;
        } else if (frames[index + offset] && frames[index + offset].complete) {
          img = frames[index + offset];
          break;
        }
      }
    }

    if (!img || !img.complete) return;

    const w = cachedWidth || window.innerWidth;
    const h = cachedHeight || window.innerHeight;
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

    ctx.drawImage(img, drawX, drawY, drawW, drawH);
  };

  const onScroll = () => {
    if (!isTicking) {
      window.requestAnimationFrame(() => {
        const rect = container.getBoundingClientRect();
        const maxScroll = rect.height - window.innerHeight;
        const scrollProgress = maxScroll > 0 ? Math.min(Math.max(-rect.top / maxScroll, 0), 1) : 0;

        targetFrameIndex = Math.min(
          Math.floor(scrollProgress * (frameCount - 1)),
          frameCount - 1
        );

        if (targetFrameIndex !== currentFrameIndex) {
          currentFrameIndex = targetFrameIndex;
          renderFrame(currentFrameIndex);
        }

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

        isTicking = false;
      });
      isTicking = true;
    }
  };

  let resizeTimeout;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(resizeCanvas, 100);
  }, { passive: true });

  preloadProgressive();

  // 📜 Tabs interactivos para filtrado del menú
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
          if (filter === 'all' || filter === colType) {
            col.classList.remove('is-hidden');
          } else {
            col.classList.add('is-hidden');
          }
        });
      });
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMenuTabs);
  } else {
    initMenuTabs();
  }
})();

