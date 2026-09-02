# Nandark Atomic Core (WordPress Starter Engine + Scrollytelling)

> **Motor modular de alto rendimiento para WordPress.**  
> Diseñado bajo **Atomic Design**, con soporte para **Scrollytelling interactivo por Canvas a 60 FPS**, capa de **REST API & WPGraphQL**, y compatibilidad nativa con **Agentes de IA vía MCP (Model Context Protocol)**.

---

## ⚡ Características Principales

1. **⚛️ Arquitectura Atomic Design:**
   - **Átomos:** Botones primarios, secundarios, WhatsApp, Badges de estado (`components/atoms/`).
   - **Moléculas:** Tarjetas de servicio, items de contacto, cards interactivas (`components/molecules/`).
   - **Organismos:** Secciones de experiencia, grillas interactivas, cajas de reserva directa (`components/organisms/`).
   - **Templates:** Plantillas completas que se integran limpiamente con cualquier tema activo (`components/templates/`).

2. **🎬 Scrollytelling por Canvas (60 FPS):**
   - Motor JavaScript (`assets/js/scrollytelling.js`) que sincroniza el scroll del usuario con 240 fotogramas generados por IA (Google Flow / FFmpeg).
   - Renderizado en `<canvas>` con aceleración por hardware y soporte Retina (estilo Apple / Orizo.io).
   - Tarjetas de narrativa flotante sincronizadas con los puntos clave del recorrido.

3. **🌐 Backend & Capa de Servicios:**
   - Endpoints propios bajo `/wp-json/nandark/v1/` (`/config`, `/services`, `/book`).
   - Conector con servicios de WhatsApp / Webhooks externos para conversión instantánea.
   - Extensiones de esquema para **WPGraphQL** (listo para Frontends desacoplados o Apps móviles).

4. **🤖 Compatibilidad con Agentes de IA vía MCP:**
   - Compatible con `mcp-adapter` y `Enable Abilities for MCP` para que asistentes como Antigravity, Claude o Cursor puedan crear páginas, modificar templates y gestionar CPTs sin tocar código manualmente.

---

## 📁 Estructura del Proyecto

```
nandark-atomic-core/
├── nandark-atomic-core.php        # Bootstrap, hooks y helper nandark_render()
├── includes/
│   ├── class-cpt-manager.php      # Custom Post Types (Servicios) con soporte REST
│   └── class-assets-loader.php    # Enqueue de estilos y scripts atómicos
├── components/                    # ⚛️ Atomic Design
│   ├── atoms/                     # button.php, badge.php
│   ├── molecules/                 # service-card.php
│   ├── organisms/                 # hero-section.php
│   └── templates/                 # page-home.php, single-service.php
├── api/                           # 🌐 REST API, WPGraphQL y Servicios
│   ├── class-rest-api.php         # Endpoints /wp-json/nandark/v1/
│   ├── class-graphql-schema.php   # Tipos y campos GraphQL
│   └── services/                  # WhatsApp_Service, Booking_Service
├── theme/
│   └── template-loader.php        # Conector con la jerarquía de plantillas de WP
└── assets/
    ├── css/atomic-core.css        # CSS moderno sin dependencias
    ├── js/scrollytelling.js       # Controlador Canvas a 240 frames
    ├── images/                    # Fotogramas clave e imágenes 1080p
    └── frames/                    # 240 frames individuales del recorrido
```

---

## 🚀 Uso Rápido

1. Copiar la carpeta `nandark-atomic-core` dentro de `wp-content/plugins/`.
2. Activar el plugin desde el panel de WordPress.
3. La página de inicio (`/`) cargará automáticamente la plantilla atómica con Scrollytelling.

---

**Desarrollado por Nandark Studio** · [nandark.com](https://nandark.com)
