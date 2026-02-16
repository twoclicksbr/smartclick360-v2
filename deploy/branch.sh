#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Pergunta o nome da branch
echo -n "Nome da branch: "
read branch_input

# Validação: não pode ser vazio
if [ -z "$branch_input" ]; then
    echo -e "${RED}❌ Nome da branch não pode ser vazio${NC}"
    exit 1
fi

# Converte para minúsculo
branch_input=$(echo "$branch_input" | tr '[:upper:]' '[:lower:]')

# Remove acentos
branch_input=$(echo "$branch_input" | iconv -f utf-8 -t ascii//TRANSLIT 2>/dev/null || echo "$branch_input")

# Detecta se é fix ou feature
prefix="feature"
if [[ "$branch_input" =~ (bug|fix|erro|correcao|correção) ]]; then
    prefix="fix"
    # Remove a palavra-chave do nome
    branch_input=$(echo "$branch_input" | sed -E 's/(bug|fix|erro|correcao|correção)//g')
fi

# Remove espaços do início e fim
branch_input=$(echo "$branch_input" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')

# Substitui espaços e underscores por hífens
branch_input=$(echo "$branch_input" | sed 's/[[:space:]_]/-/g')

# Remove hífens duplicados
branch_input=$(echo "$branch_input" | sed 's/-\+/-/g')

# Remove hífens do início e fim
branch_input=$(echo "$branch_input" | sed 's/^-//;s/-$//')

# Nome final da branch
branch_name="${prefix}/${branch_input}"

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
