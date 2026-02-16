#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Lista todas as branches feature/ e fix/
branches=($(git branch --list "feature/*" "fix/*" | sed 's/^[* ] //'))

# Verifica se encontrou alguma branch
if [ ${#branches[@]} -eq 0 ]; then
    echo -e "${YELLOW}⚠️  Nenhuma branch de feature ou fix encontrada${NC}"
    exit 0
fi

# Exibe menu de seleção
echo "Branches disponíveis:"
select branch in "${branches[@]}"; do
    if [ -n "$branch" ]; then
        # Faz checkout da branch escolhida
        git checkout "$branch"

        if [ $? -eq 0 ]; then
            echo ""
            echo -e "${GREEN}✅ Mudou para $branch${NC}"
        else
            echo ""
            echo -e "${RED}❌ Erro ao mudar para a branch $branch${NC}"
            exit 1
        fi
        break
    else
        echo -e "${RED}❌ Opção inválida${NC}"
        exit 1
    fi
done
