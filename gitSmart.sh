#!/usr/bin/env bash
set -e

# ================================
# 🚀 Deploy rápido - SmartClick360
# ================================

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Nome do branch atual
BRANCH=$(git branch --show-current)

# Mensagem do commit
MSG="$1"
if [ -z "$MSG" ]; then
  MSG="auto(SmartClick360): $(date '+%Y-%m-%d %H:%M:%S')"
fi

echo -e "${CYAN}--------------------------------------------${NC}"
echo -e "${CYAN}🧭 Projeto: SmartClick360${NC}"
echo -e "${CYAN}🌿 Branch: ${YELLOW}$BRANCH${NC}"
echo -e "${CYAN}--------------------------------------------${NC}"

echo -e "${CYAN}➜ Adicionando alterações...${NC}"
git add .

# Se não houver mudanças, evitar erro
if git diff --cached --quiet; then
  echo -e "${YELLOW}⚠️  Nenhuma alteração para commitar.${NC}"
else
  echo -e "${YELLOW}➜ Commit:${NC} $MSG"
  git commit -m "$MSG"
fi

# Verifica se o branch remoto está configurado
UPSTREAM=$(git rev-parse --abbrev-ref --symbolic-full-name @{u} 2>/dev/null || echo "")

if [ -z "$UPSTREAM" ]; then
  echo -e "${YELLOW}⚠️  Nenhum upstream configurado. Criando vínculo com origin/$BRANCH...${NC}"
  git push -u origin "$BRANCH"
else
  echo -e "${CYAN}➜ Enviando para o repositório remoto...${NC}"
  git push
fi

echo -e "${GREEN}✔ SmartClick360 sincronizado com sucesso no GitHub.${NC}"
echo -e "${CYAN}--------------------------------------------${NC}"
