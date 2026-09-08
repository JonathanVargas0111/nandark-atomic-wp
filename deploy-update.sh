#!/usr/bin/env bash
set -e

# ==============================================================================
# Nandark Atomic Deployer & Bundle Provisioner
# Permite actualizar el plugin y aprovisionar la suite completa de dependencias
# ==============================================================================

SITE_URL="${1:-http://nandark-lab.local}"
FLAG="${2:-}"

# El token NUNCA vive en este archivo: el repo es publico. Se toma del entorno.
#   export NANDARK_DEPLOY_TOKEN="....."   (el mismo valor que en wp-config.php)
DEPLOY_KEY="${3:-${NANDARK_DEPLOY_TOKEN:-}}"

if [ -z "$DEPLOY_KEY" ]; then
  echo "ERROR: falta el token de deploy." >&2
  echo "  export NANDARK_DEPLOY_TOKEN='<el valor de NANDARK_DEPLOY_TOKEN en wp-config.php>'" >&2
  echo "  o pasalo como tercer argumento." >&2
  exit 1
fi

if [ "$FLAG" == "--bundle" ] || [ "$1" == "--bundle" ]; then
  TARGET_URL="$SITE_URL"
  if [ "$1" == "--bundle" ]; then
    TARGET_URL="${2:-http://nandark-lab.local}"
    DEPLOY_KEY="${3:-${NANDARK_DEPLOY_TOKEN:-$DEPLOY_KEY}}"
  fi

  echo "📦 Aprovisionando Bundle Completo de Nandark en: $TARGET_URL"
  echo "👉 Instalando y activando: mcp-adapter, enable-abilities-for-mcp, wp-graphql..."

  RESPONSE=$(curl -s -X POST \
    -H "Authorization: Bearer $DEPLOY_KEY" \
    "$TARGET_URL/wp-json/nandark/v1/install-bundle")

  echo "📋 Resultado del Aprovisionamiento:"
  echo "$RESPONSE" | grep -o '"results":{[^}]*}' || echo "$RESPONSE"
  echo "✅ Suite de plugins aprovisionada y activa."
  exit 0
fi

echo "🚀 Disparando auto-actualización de Nandark Atomic Core en: $SITE_URL"

RESPONSE=$(curl -s -X POST \
  -H "Authorization: Bearer $DEPLOY_KEY" \
  "$SITE_URL/wp-json/nandark/v1/self-update")

echo "📦 Respuesta del servidor:"
echo "$RESPONSE" | grep -o '"message":[^,]*' || echo "$RESPONSE"
echo "✅ Proceso finalizado."
