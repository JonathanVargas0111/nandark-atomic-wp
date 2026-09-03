# Guía de Arquitectura de Temas, Endpoints y Despliegue Remoto
> **Nandark Atomic Core — Documentación Técnica para Desarrolladores, Agentes y Frontends**

Este documento recopila las directrices de arquitectura de temas en WordPress, los endpoints de consumo para herramientas/IA y el sistema de aprovisionamiento de dependencias.

---

## 🏛️ 1. Arquitectura de Temas: ¿Cuál es el Mejor Camino Técnico?

Al construir para clientes y agencias en 2026, la industria y la documentación oficial de WordPress ofrecen tres modelos:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. THEME TRADICIONAL / CONSTRUCTOR (El modelo roto)                        │
│    Depende de Elementor/Divi. 40+ plugins, 350+ peticiones HTTP.           │
│    El cliente o una actualización rompe el CSS. ❌ NO USAR EN NANDARK.      │
├─────────────────────────────────────────────────────────────────────────────┤
│ 2. BLOCK THEMES / FULL SITE EDITING (FSE)                                  │
│    Todo son bloques HTML (`theme.json`). Muy bueno para blogs limpios, pero │
│    demasiado rígido para experiencias complejas de Scrollytelling/Canvas.   │
├─────────────────────────────────────────────────────────────────────────────┤
│ 3. PLUGIN-DRIVEN ATOMIC THEME / HYBRID HEADLESS (La arquitectura Nandark)  │
│    El Theme es un caparazón ultraliviano de 10 líneas. Toda la ingeniería   │
│    y diseño vive en el plugin `nandark-atomic-core` mediante componentes.    │
│    ✅ 100% portable, indestructible para el cliente y versionable en Git.   │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Por qué elegimos la arquitectura "Plugin-Driven":
1. **Inmunidad a cambios de Theme:** Si el cliente cambia o actualiza el tema activo de WordPress (ej. Twenty Twenty-Five o Astra), **la web no se rompe**, porque `Template_Loader` toma el control de las rutas clave (`/`, `/servicios/`).
2. **Versionable en Git con CI/CD:** El código de componentes atómicos vive en el plugin, lo que nos permite auto-actualizarlo con "La Puerta" (`Self_Updater`) en 1 comando.
3. **Multi-plataforma:** La misma lógica sirve para renderizar en PHP en el servidor, o para consumirse vía JSON en Next.js/Astro/móvil.

---

## 🔌 2. Catálogo de Endpoints REST para Herramientas, Chatbots e IA

El plugin expone rutas bajo el namespace `nandark/v1` diseñadas para ser consumidas por agentes (Claude Code, Antigravity, n8n, chatbots de WhatsApp o frontends desacoplados):

### A. `/wp-json/nandark/v1/landing` (GET - Público)
Devuelve toda la estructura de la landing en formato JSON limpio (hero steps, destacados de carta, experiencias y addons):
```bash
curl -s "https://tusitio.com/wp-json/nandark/v1/landing"
```

### B. `/wp-json/nandark/v1/quote` (POST - Público)
Calcula cotizaciones de reservas VIP en tiempo real para cualquier bot de mensajería o aplicación externa:
```bash
curl -s -X POST -H "Content-Type: application/json" \
  -d '{"guests": 6, "experience": "maridaje", "sommelier_addon": true, "turn": "cena"}' \
  "https://tusitio.com/wp-json/nandark/v1/quote"
```
**Respuesta JSON:**
```json
{
  "success": true,
  "quote": {
    "guests": 6,
    "experience": "Experiencia con Maridaje de Alta Gama",
    "turn": "Turno Noche (7:30 PM)",
    "sommelier_addon": true,
    "price_per_person": 305000,
    "total": 1830000,
    "formatted_total": "$ 1.830.000",
    "whatsapp_url": "https://wa.me/573000000000?text=..."
  }
}
```

### C. `/wp-json/nandark/v1/bundle-status` (GET - Requiere Bearer Token)
Inspecciona si están instalados y activos `mcp-adapter`, `enable-abilities-for-mcp` y `wp-graphql`.

### D. `/wp-json/nandark/v1/install-bundle` (POST - Requiere Bearer Token)
Descarga, instala y activa automáticamente toda la suite de plugins oficiales.

### E. `/wp-json/nandark/v1/self-update` (POST - Requiere Bearer Token)
Descarga la última versión de GitHub y sobreescribe el plugin en caliente.

---

## 🚀 3. Comandos de Despliegue y Aprovisionamiento

```bash
# 1. Aprovisionar bundle completo (mcp, abilities, graphql) en 1 paso:
./deploy-update.sh http://nandark-lab.local --bundle

# 2. Actualizar el código del plugin desde GitHub:
./deploy-update.sh http://nandark-lab.local

# 3. Desplegar en producción con clave secreta:
./deploy-update.sh https://cliente.com --bundle "tu-token-secreto"
```

---

## ⚡ 4. Optimización de Rendimiento Nativa (Anti-Bloat)

El módulo `Performance_Optimizer` limpia automáticamente la basura de WordPress Core sin requerir plugins pesados:
* **Emojis de WP:** Remueve scripts, estilos y llamadas DNS innecesarias a `s.w.org`.
* **Gutenberg CSS Innecesario:** Dequeue automático de `wp-block-library`, `wp-block-library-theme` y estilos globales en páginas atómicas, ahorrando más de 50KB de CSS sin uso.
* **Seguridad & Limpieza:** Deshabilita XML-RPC, oEmbeds en frontend y oculta la versión de WordPress (`wp_generator`) de las cabeceras HTML.

---

## 🧠 5. Motor de SEO Estructurado & AEO (Search & AI Ready)

El módulo `Seo_Schema_Manager` inyecta automáticamente en `<head>`:
* **Schema.org JSON-LD:** Entidad compuesta `Restaurant` y `BarOrPub` con horarios precisos, geolocalización en Bogotá, platos principales con precios en COP y políticas de reservas. Optimizado para indexación en Google y respuestas precisas en ChatGPT, Gemini y Perplexity.
* **Open Graph / Tarjetas Sociales:** Metadatos `og:title`, `og:description`, `og:image` (1200x630px) y Twitter Cards para que cualquier enlace compartido en WhatsApp o redes luzca impecable.
