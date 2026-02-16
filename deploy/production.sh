#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Aviso de confirmação
echo ""
echo -e "${YELLOW}⚠️  Você está prestes a fazer deploy em PRODUÇÃO.${NC}"
echo -n "Continuar? (s/n): "
read confirmation

if [[ ! "$confirmation" =~ ^[sS]$ ]]; then
    echo ""
    echo -e "${RED}❌ Deploy cancelado pelo usuário${NC}"
    exit 0
fi

echo ""
echo -e "${BLUE}📌 Mudando para sandbox...${NC}"
echo ""

# Muda para a branch sandbox
git checkout sandbox
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao fazer checkout da branch sandbox${NC}"
    exit 1
fi

# Atualiza a branch sandbox
git pull origin sandbox
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao fazer pull da branch sandbox${NC}"
    exit 1
fi

echo ""
echo -e "${PURPLE}🔀 Criando PR sandbox → main...${NC}"
echo ""

# Cria o PR para main
gh pr create --base main --title "Deploy: sandbox → production" --body ""
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao criar o Pull Request${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ PR criado com sucesso!${NC}"
echo ""

# Faz merge do PR
echo -e "${PURPLE}🔀 Mergeando PR...${NC}"
echo ""

gh pr merge --merge
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao fazer merge do Pull Request${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ PR mergeado com sucesso!${NC}"
echo ""

# Faz deploy em produção via SSH
echo -e "${PURPLE}🚀 Fazendo deploy em produção...${NC}"
echo ""

ssh root@168.231.64.36 "cd /home/smartclick360.com/production && git fetch origin && git reset --hard origin/main && php artisan config:clear && php artisan route:clear && php artisan view:clear"
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao fazer deploy em produção${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Deploy em PRODUÇÃO realizado com sucesso!${NC}"
