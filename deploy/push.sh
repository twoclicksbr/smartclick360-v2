#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Detecta a branch atual
current_branch=$(git branch --show-current)

if [ -z "$current_branch" ]; then
    echo -e "${RED}❌ Erro ao detectar a branch atual${NC}"
    exit 1
fi

echo -e "${BLUE}📌 Branch atual: $current_branch${NC}"
echo ""

# Valida se é uma branch feature/ ou fix/
if [[ ! "$current_branch" =~ ^(feature|fix)/ ]]; then
    echo -e "${RED}❌ Você não está em uma branch de feature ou fix. Branch atual: $current_branch${NC}"
    exit 1
fi

# Se for branch padrão, pede nome real
if [[ "$current_branch" =~ ^feature/padrao- ]]; then
    echo -e "${YELLOW}⚠️  Branch com nome padrão detectada.${NC}"
    echo -n "Digite o nome real da branch: "
    read branch_input

    # Validação: não pode ser vazio
    if [ -z "$branch_input" ]; then
        echo -e "${RED}❌ Nome da branch não pode ser vazio${NC}"
        exit 1
    fi

    # Converte para minúsculo
    branch_input=$(echo "$branch_input" | tr '[:upper:]' '[:lower:]')

    # Remove acentos manualmente
    branch_input=$(echo "$branch_input" | sed '
      s/[áàâã]/a/g
      s/[éèê]/e/g
      s/[íìî]/i/g
      s/[óòôõ]/o/g
      s/[úùû]/u/g
      s/[ÁÀÂÃ]/a/g
      s/[ÉÈÊË]/e/g
      s/[ÍÌÎ]/i/g
      s/[ÓÒÔÕ]/o/g
      s/[ÚÙÛ]/u/g
      s/ç/c/g
      s/Ç/c/g
      s/ñ/n/g
      s/Ñ/n/g
    ')

    # Detecta se é fix ou feature
    prefix="feature"
    if [[ "$branch_input" =~ (bug|fix|erro|correcao|correção) ]]; then
        prefix="fix"
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
    new_branch="${prefix}/${branch_input}"

    # Renomeia a branch local
    git branch -m "$new_branch"
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Erro ao renomear a branch para $new_branch${NC}"
        exit 1
    fi

    echo -e "${GREEN}✅ Branch renomeada para $new_branch${NC}"
    echo ""

    current_branch="$new_branch"
fi

# Extrai o tipo (feature ou fix) e o nome
if [[ "$current_branch" =~ ^feature/ ]]; then
    commit_prefix="feat"
    branch_name="${current_branch#feature/}"
elif [[ "$current_branch" =~ ^fix/ ]]; then
    commit_prefix="fix"
    branch_name="${current_branch#fix/}"
fi

# Monta a mensagem do commit
commit_message="$commit_prefix: $branch_name"

# Verifica se há alterações para commitar
git diff --quiet && git diff --staged --quiet
if [ $? -eq 0 ]; then
    echo -e "${YELLOW}⚠️  Nenhuma alteração para commitar${NC}"
    exit 0
fi

# Executa os comandos git
echo "Executando comandos git..."
echo ""

# git add .
git add .
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao adicionar arquivos (git add .)${NC}"
    exit 1
fi

# git commit
git commit -m "$commit_message"
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao fazer commit${NC}"
    exit 1
fi

# git push
git push origin "$current_branch"
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro ao fazer push para origin/$current_branch${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Push da branch $current_branch realizado com sucesso!${NC}"
