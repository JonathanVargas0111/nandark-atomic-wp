# Guía de Automatización y Despliegue Remoto (La Puerta)
> **Nandark Atomic Core — Mecanismo de Auto-Actualización y Gestión Asistida por IA**

Este documento explica cómo cualquier desarrollador o agente de IA (**Claude Code**, **Antigravity**, **Cursor**) puede modificar, desplegar y auto-actualizar el plugin en sitios remotos sin acceder manualmente al panel de administración de WordPress.

---

## 🎯 El Problema que Resuelve
En flujos tradicionales de WordPress, cada cambio exige:
1. Comprimir el plugin en un archivo `.zip`.
2. Iniciar sesión en el `/wp-admin` del cliente.
3. Subir el ZIP y confirmar *"Reemplazar plugin existente"*.

**Con este mecanismo ("La Puerta"):**  
Tú o cualquier agente hacen cambios en local o en GitHub, ejecutan un solo comando desde la terminal, y el WordPress del cliente **descarga el código firmado de GitHub y se sobreescribe a sí mismo en caliente.**

---

## ⚙️ Arquitectura del Sistema

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

## 🚀 Cómo Usar el Despliegue Remoto

### 1. Despliegue con 1 solo comando (Script CLI)
Desde la raíz del plugin o de tu proyecto:

```bash
# Despliegue a entorno local
./deploy-update.sh http://nandark-lab.local

# Despliegue a servidor remoto / VPS en producción
./deploy-update.sh https://tusitio.com "tu-token-secreto"
```

### 2. Despliegue manual vía cURL (Para CI/CD o Agentes)
```bash
curl -s -X POST \
  -H "Authorization: Bearer nandark-secure-deploy-key-2026" \
  https://tusitio.com/wp-json/nandark/v1/self-update
```

**Respuesta JSON esperada:**
```json
{
  "success": true,
  "message": "Plugin Nandark Atomic Core actualizado con éxito desde GitHub.",
  "version": "1.0.0",
  "upgraded_at": "2026-09-03 04:15:00"
}
```

### 3. Actualización Nativa en WordPress (Vía Panel Admin)
Si un cliente o administrador entra a `/wp-admin/plugins.php`:
* WordPress compara la versión instalada contra la última release de GitHub (`JonathanVargas0111/nandark-atomic-wp`).
* Muestra el aviso nativo: *"Hay una nueva versión disponible. Actualizar ahora"*.
* Al hacer clic, se actualiza usando el motor de descargas nativo de WordPress.

---

## 🔒 Auditoría de Seguridad Implementada

Para evitar vulnerabilidades comunes en plugins auto-hospedados, se incorporaron 4 capas de protección:

1. **Anti Timing-Attacks (`hash_equals`):**  
   La validación del token no usa comparaciones estándar (`==`), sino comparación en tiempo constante para neutralizar ataques basados en tiempo de respuesta.

2. **Rate Limiting Anti Brute-Force:**  
   El endpoint bloquea peticiones si una IP realiza más de 5 intentos por minuto sin credenciales válidas (`HTTP 429 Too Many Requests`).

3. **Restricción Estricta de Dominio:**  
   WordPress únicamente aceptará paquetes `.zip` provenientes de dominios oficiales de GitHub (`github.com`, `api.github.com`, `codeload.github.com`, `objects.githubusercontent.com`). URLs externas o paquetes no autorizados son rechazados.

4. **Compatibilidad con Usuarios Administradores:**  
   Si una petición incluye cookies de un administrador con capacidad `update_plugins`, se autoriza sin requerir el token bearer.

---

## 📝 Configuración en Producción

Para personalizar el token de despliegue en un cliente específico, agrega esta constante en el archivo `wp-config.php` del servidor:

```php
define('NANDARK_DEPLOY_TOKEN', 'tu_token_super_secreto_generado_aqui');
```

O si el repositorio de GitHub llega a ser privado, configura el token de acceso personal:
```php
define('NANDARK_GITHUB_TOKEN', 'ghp_xxxxxxxxxxxxxxxxxxxx');
```
