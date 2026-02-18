#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
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

# Extrai o tipo (feature ou fix) e o nome
if [[ "$current_branch" =~ ^feature/ ]]; then
    commit_prefix="feat"
    branch_name="${current_branch#feature/}"
elif [[ "$current_branch" =~ ^fix/ ]]; then
    commit_prefix="fix"
    branch_name="${current_branch#fix/}"
fi

# Monta a mensagem do PR
pr_title="$commit_prefix: $branch_name"

# Cria o PR para sandbox
echo -e "${PURPLE}🔀 Criando PR $current_branch → sandbox...${NC}"
echo ""

gh pr create --base sandbox --title "$pr_title" --body ""
if [ $? -ne 0 ]; then
    # Verifica se já existe um PR aberto
    existing_pr=$(gh pr list --base sandbox --head "$current_branch" --state open --json number --jq '.[0].number' 2>/dev/null)

    if [ -n "$existing_pr" ]; then
        echo ""
        echo -e "${YELLOW}⚠️  PR já existe (#$existing_pr). Usando o existente...${NC}"
        echo ""
    else
        echo ""
        echo -e "${RED}❌ Erro ao criar o Pull Request${NC}"
        exit 1
    fi
fi

echo ""
echo -e "${GREEN}✅ PR criado com sucesso!${NC}"
echo ""

# Faz merge do PR
echo -e "${PURPLE}🔀 Mergeando PR...${NC}"
echo ""

gh pr merge --merge --delete-branch
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao fazer merge do Pull Request${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ PR mergeado com sucesso!${NC}"
echo ""

# Volta para sandbox local atualizado
git checkout sandbox
git pull origin sandbox

# Faz deploy no sandbox via SSH
echo -e "${PURPLE}🚀 Fazendo deploy no sandbox...${NC}"
echo ""

ssh root@168.231.64.36 "cd /home/smartclick360.com/sandbox && git fetch origin && git reset --hard origin/sandbox && php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan migrate --database=landlord --path=database/migrations/landlord --force && php artisan tenant:migrate-all --schema=sandbox"
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao fazer deploy no sandbox${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Deploy no sandbox realizado com sucesso!${NC}"
echo ""

# Cria nova branch para próxima tarefa
bash deploy/newBranch.sh
