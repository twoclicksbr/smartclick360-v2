-- =========================================
-- INSTRUÇÕES PARA CRIAR O BANCO CENTRAL
-- =========================================
--
-- Execute este comando usando pgAdmin, DBeaver, ou psql:
--
-- Opção 1 - Usando pgAdmin:
-- 1. Abra o pgAdmin
-- 2. Conecte ao servidor PostgreSQL
-- 3. Clique com botão direito em "Databases"
-- 4. Selecione "Create" > "Database..."
-- 5. Nome: sc360_main
-- 6. Encoding: UTF8
-- 7. Clique em "Save"
--
-- Opção 2 - Usando psql (se configurado):
-- psql -U postgres -c "CREATE DATABASE sc360_main ENCODING 'UTF8';"
--
-- Opção 3 - Usando DBeaver ou outro cliente SQL:
-- Execute o comando abaixo:

CREATE DATABASE sc360_main
    ENCODING = 'UTF8'
    LC_COLLATE = 'Portuguese_Brazil.1252'
    LC_CTYPE = 'Portuguese_Brazil.1252'
    TEMPLATE = template0;

-- =========================================
-- APÓS CRIAR O BANCO
-- =========================================
-- Execute as migrations com:
-- php artisan migrate
