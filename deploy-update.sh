#!/usr/bin/env bash
set -e

# ==============================================================================
# Nandark Atomic Deployer & Bundle Provisioner
# Permite actualizar el plugin y aprovisionar la suite completa de dependencias
# ==============================================================================

SITE_URL="${1:-http://nandark-lab.local}"
FLAG="${2:-}"
DEPLOY_KEY="${3:-nandark-secure-deploy-key-2026}"

if [ "$FLAG" == "--bundle" ] || [ "$1" == "--bundle" ]; then
  TARGET_URL="$SITE_URL"
  if [ "$1" == "--bundle" ]; then
    TARGET_URL="${2:-http://nandark-lab.local}"
    DEPLOY_KEY="${3:-nandark-secure-deploy-key-2026}"
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
