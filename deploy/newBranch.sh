#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Gera nome da branch com data/hora atual
branch_name="feature/padrao-$(date +%Y-%m-%d-%H%M%S)"

# Executa os comandos git
echo ""
echo "Executando comandos git..."
echo ""

git checkout sandbox
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao fazer checkout da branch sandbox${NC}"
    exit 1
fi

git pull origin sandbox
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao fazer pull da branch sandbox${NC}"
    exit 1
fi

git checkout -b "$branch_name"
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao criar a branch $branch_name${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Branch $branch_name criada com sucesso!${NC}"
