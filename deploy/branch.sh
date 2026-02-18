#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Lista todas as branches feature/ e fix/
branches=($(git branch --list "feature/*" "fix/*" | sed 's/^[* ] //'))

# Verifica se encontrou alguma branch
if [ ${#branches[@]} -eq 0 ]; then
    echo -e "${YELLOW}⚠️  Nenhuma branch de feature ou fix encontrada${NC}"
    exit 0
fi

# Exibe menu numerado
echo ""
echo "Branches disponíveis:"
echo ""
for i in "${!branches[@]}"; do
    echo -e "  ${BLUE}$((i+1)))${NC} ${branches[$i]}"
done
echo ""
echo -n "Escolha (1-${#branches[@]}): "
read choice

# Valida a escolha
if ! [[ "$choice" =~ ^[0-9]+$ ]] || [ "$choice" -lt 1 ] || [ "$choice" -gt "${#branches[@]}" ]; then
    echo ""
    echo -e "${RED}❌ Opção inválida${NC}"
    exit 1
fi

branch="${branches[$((choice-1))]}"

# Verifica se há alterações não commitadas
if ! git diff --quiet 2>/dev/null || ! git diff --staged --quiet 2>/dev/null; then
    echo ""
    echo -e "${YELLOW}⚠️  Alterações não commitadas detectadas. Fazendo stash...${NC}"
    git stash push -m "auto-stash branch.sh $(date +%Y%m%d-%H%M%S)"
    STASHED=1
fi

# Faz checkout da branch escolhida
git checkout "$branch"

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ Mudou para $branch${NC}"

    # Restaura stash se foi feito
    if [ "$STASHED" = "1" ]; then
        echo ""
        echo -e "${YELLOW}🔄 Restaurando alterações do stash...${NC}"
        git stash pop
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✅ Alterações restauradas${NC}"
        else
            echo -e "${RED}⚠️  Conflito ao restaurar stash. Use 'git stash pop' manualmente.${NC}"
        fi
    fi
else
    echo ""
    echo -e "${RED}❌ Erro ao mudar para a branch $branch${NC}"

    # Restaura stash se falhou
    if [ "$STASHED" = "1" ]; then
        git stash pop
    fi
    exit 1
fi
