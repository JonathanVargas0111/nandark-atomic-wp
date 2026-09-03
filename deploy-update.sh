#!/usr/bin/env bash
set -e

SITE_URL="${1:-http://nandark-lab.local}"
DEPLOY_KEY="${2:-nandark-secure-deploy-key-2026}"

echo "🚀 Disparando auto-actualización remota en: $SITE_URL"

RESPONSE=$(curl -s -X POST \
  -H "Authorization: Bearer $DEPLOY_KEY" \
  "$SITE_URL/wp-json/nandark/v1/self-update")

echo "📦 Respuesta del servidor:"
echo "$RESPONSE" | grep -o '"message":[^,]*' || echo "$RESPONSE"
echo "✅ Proceso finalizado."
