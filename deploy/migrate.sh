#!/bin/bash

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

echo -e "${PURPLE}🔄 Rodando migrations no localhost...${NC}"
echo ""

# Migration landlord
echo -e "${PURPLE}📌 Migrando landlord...${NC}"
php artisan migrate --database=landlord --path=database/migrations/landlord
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao migrar landlord${NC}"
    exit 1
fi

echo ""

# Migration tenants
echo -e "${PURPLE}📌 Migrando tenants...${NC}"
php artisan tenant:migrate-all --schema=production
if [ $? -ne 0 ]; then
    echo ""
    echo -e "${RED}❌ Erro ao migrar tenants${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}✅ Migrations executadas com sucesso!${NC}"
