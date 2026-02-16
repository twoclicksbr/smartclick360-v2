#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Lista todas as branches feature/ e fix/
branches=($(git branch --list "feature/*" "fix/*" | sed 's/^[* ] //'))

# Verifica se encontrou alguma branch
if [ ${#branches[@]} -eq 0 ]; then
    echo -e "${YELLOW}⚠️  Nenhuma branch de feature ou fix encontrada${NC}"
    exit 0
fi

# Variáveis de controle
selected=0
total=${#branches[@]}

# Função para desenhar o menu
draw_menu() {
    # Move cursor para o início
    tput cup 0 0

    echo -e "${CYAN}Branches disponíveis (↑↓ para navegar, Enter para selecionar):${NC}"
    echo ""

    for i in "${!branches[@]}"; do
        if [ $i -eq $selected ]; then
            echo -e "${GREEN}> ${branches[$i]}${NC}"
        else
            echo "  ${branches[$i]}"
        fi
    done
}

# Limpa a tela e desenha o menu inicial
clear
draw_menu

# Loop de navegação
while true; do
    # Lê uma tecla
    read -rsn1 key

    # Se for ESC, lê mais 2 caracteres para pegar a sequência completa
    if [[ $key == $'\x1b' ]]; then
        read -rsn2 key
    fi

    case $key in
        '[A') # Seta para cima
            ((selected--))
            if [ $selected -lt 0 ]; then
                selected=$((total - 1))
            fi
            draw_menu
            ;;
        '[B') # Seta para baixo
            ((selected++))
            if [ $selected -ge $total ]; then
                selected=0
            fi
            draw_menu
            ;;
        '') # Enter
            break
            ;;
    esac
done

# Limpa a tela após seleção
clear

# Branch selecionada
branch="${branches[$selected]}"

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
