# Guía de Automatización, Bundle y Despliegue Remoto (La Puerta)
> **Nandark Atomic Core — Suite Todo-en-Uno (All-in-One Plugin Bundle)**

Este documento explica cómo cualquier desarrollador o agente de IA (**Claude Code**, **Antigravity**, **Cursor**) puede modificar, desplegar y aprovisionar la suite completa de plugins sin acceder manualmente a buscar ni subir múltiples ZIPs al panel de administración de WordPress.

---

## 🎯 El Problema que Resuelve
Para que este ecosistema funcione al 100% se requiere una tríada de herramientas:
1. `nandark-atomic-core`: El motor visual, scrollytelling y arquitectura de componentes.
2. `mcp-adapter`: El puente de protocolo MCP oficial para Claude/Gemini.
3. `enable-abilities-for-mcp`: El catálogo de 101 habilidades de WordPress.
4. `wp-graphql`: La capa de consultas de alta velocidad.

**El dolor habitual:** Ir a repositorios de GitHub y a WordPress.org, descargar 3 o 4 ZIPs separados y subirlos uno por uno.  
**La Solución Nandark ("Plugin Bundle / Suite Installer"):**  
Subes **un solo plugin** (`nandark-atomic-core`). Este actúa como el **Host / Orquestador Maestro** y automáticamente descarga, instala y activa las demás dependencias oficiales por ti.

---

## ⚙️ Arquitectura del Sistema y Bundle

```
TU CONSOLA (CLAUDE / AGENTE)                 GITHUB (RELEASES)              WORDPRESS CLIENTE
┌──────────────────────────────┐        ┌─────────────────────────┐       ┌────────────────────────┐
│ 1. git commit & push         │ ────>  │ Repositorio Oficial     │       │ Endpoint REST Seguro   │
│ 2. ./deploy-update.sh <url>  │        │ (Releases / Zipball)    │       │ /nandark/v1/self-update│
└──────────────┬───────────────┘        └───────────┬─────────────┘       └───────────┬────────────┘
               │                                    │                                 │
               │ POST con Bearer Token              │                                 │
               └────────────────────────────────────┼────────────────────────────────>│
                                                    │ Descarga paquete HTTPS          │
                                                    │ Validado (objects.github.com)   │
                                                    └─────────────────────────────────┘
                                                                ▼
                                                    Plugin se actualiza solo
                                                    y se reactiva automáticamente
```

---

## 🚀 Comandos de Despliegue y Aprovisionamiento

### 1. Aprovisionar Todo el Bundle en 1 Comando (`--bundle`)
Si instalaste `nandark-atomic-core` en un WordPress nuevo y quieres que él mismo descargue y active `mcp-adapter`, `enable-abilities-for-mcp` y `wp-graphql`:

```bash
# Aprovisionar bundle en local
./deploy-update.sh http://nandark-lab.local --bundle

# Aprovisionar bundle en producción / VPS remoto
./deploy-update.sh https://tusitio.com --bundle "tu-token-secreto"
```

**Respuesta JSON esperada:**
```json
{
  "success": true,
  "message": "Proceso de aprovisionamiento de bundle completado.",
  "results": {
    "mcp-adapter": "Ya instalado y activo",
    "enable-abilities-for-mcp": "Ya instalado y activo",
    "wp-graphql": "Instalado y activado exitosamente"
  }
}
```

### 2. Auto-Actualización del Plugin en Caliente
```bash
# Actualizar plugin a la última versión de GitHub
./deploy-update.sh http://nandark-lab.local
```

### 3. Consultar Estado de la Suite vía API
```bash
curl -s -H "Authorization: Bearer nandark-secure-deploy-key-2026" \
  https://tusitio.com/wp-json/nandark/v1/bundle-status
```

---

## 🔒 Seguridad Implementada

1. **Anti Timing-Attacks (`hash_equals`):** Comparación criptográfica en tiempo constante del Bearer token.
2. **Rate Limiting Anti Brute-Force:** Bloqueo con `HTTP 429` a partir del 5º intento fallido por IP en 60 segundos.
3. **Restricción Estricta de Origen:** Solo se admiten descargas de paquetes oficiales de `github.com` y `wordpress.org` sobre HTTPS.
4. **Verificación de Capacidades:** Si el usuario es administrador autenticado con permiso `update_plugins`, se aprueba automáticamente.

---

## 📝 Configuración en `wp-config.php` (Servidor de Producción)

```php
// Token secreto para comandos CLI remotos
define('NANDARK_DEPLOY_TOKEN', 'clave_super_segura_de_tu_cliente');

// Opcional: Token para repositorios privados de GitHub
define('NANDARK_GITHUB_TOKEN', 'ghp_xxxxxxxxxxxxxxxxxxxx');
```
