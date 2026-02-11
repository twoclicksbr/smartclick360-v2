# SmartClick360 v2 — Contexto do Projeto

**Última atualização:** 11/02/2026

---

## 1. Papéis e Metodologia

### 1.1 Divisão de Papéis

| Papel | Quem | Responsabilidade |
|-------|------|-----------------|
| **Gerente de Projeto** | Claude (chat) | Documenta, organiza, gera prompts detalhados, valida resultados |
| **Programador** | Claude Code | Executa os prompts, coda, roda comandos |
| **Product Owner** | Alex (humano) | Define requisitos, aprova entregas, testa |

**Regra:** O Chat NUNCA coda diretamente. Ele gera prompts para o Claude Code executar.

### 1.2 Metodologia de Trabalho

**O Claude Code erra quando:**
- Recebe prompts muito longos (perde detalhes no meio)
- "Acha que sabe melhor" e adiciona campos/funcionalidades por conta própria
- Recebe instruções implícitas (interpreta errado)

**Como evitar erros:**

1. **1 prompt = 1 tarefa pequena.** Nunca mandar tudo de uma vez.
2. **Cada fase é quebrada em N tarefas granulares** (ex: Fase 4 teve 15 tarefas).
3. **Instruções explícitas:** sempre incluir "Siga EXATAMENTE o código abaixo. Não adicione, remova ou renomeie NENHUM campo por conta própria."
4. **Validação pós-execução:** após cada tarefa, pedir para mostrar o resultado.
5. **Checklist no final do prompt:** listar exatamente o que deve existir após a execução.

### 1.3 Formato dos Prompts

- **Granularidade:** Prompts pequenos e sequenciais (passo a passo)
- **Formato:** Texto direto no chat (blockquote `>`) para o Alex copiar e colar no Claude Code
- **Idioma:** Português
- **Padrão do prompt:** Contexto → Instrução → Código exato → Validação esperada → "Não altere mais nada"

### 1.4 Fases do Projeto

| Fase | Descrição | Status |
|------|-----------|--------|
| 1 | Estrutura básica de rotas e páginas | ✅ Concluída |
| 2 | Layout padrão com Metronic 8 Demo 34 | ✅ Concluída |
| 3 | Formulário completo de registro com validação | ✅ Concluída |
| 4 | Banco central + criação do BD do tenant | ✅ Concluída |
| 5 | Login + identificação de tenant por subdomínio | 🔲 Pendente |
| 6 | Dashboard inicial do tenant | 🔲 Pendente |
| 7 | CRUD de Pessoas | 🔲 Pendente |
| 8+ | Demais módulos do ERP | 🔲 Pendente |

---

## 2. Visão Geral do Projeto

O SmartClick360 é um **ERP web multi-tenant** SaaS. Cada empresa (tenant) tem seu próprio banco de dados isolado, acessado via subdomínio `{slug}.smartclick360.com`.

### Stack Tecnológica

| Camada | Tecnologia |
|--------|-----------|
| Framework | Laravel 11 |
| PHP | 8.4 |
| Frontend | Blade Templates |
| Tema | Metronic 8 Demo 34 |
| Banco de Dados | PostgreSQL 16 |
| CSS | Bootstrap 5 |
| Ícones | KTIcons |
| Máscaras | Inputmask.js |
| Servidor Local | Laravel Herd |
| Hospedagem (produção) | VPS Hostinger |
| Gateway de Pagamento | Asaas |

### Caminhos Locais

- **Projeto Laravel:** `C:\Herd\smartclick360-v2`
- **Metronic (SOMENTE LEITURA):** `C:\Herd\themeforest\metronic\demo34`
- **URL local:** `http://smartclick360-v2.test`

---

## 3. Arquitetura Multi-Tenant

### Estratégia: Database-per-Tenant

- 1 aplicação Laravel, N bancos de dados PostgreSQL
- Implementação própria (sem pacotes como Tenancy for Laravel)
- Roteamento por subdomínio: `{slug}.smartclick360.com`

### Banco Central (sc360_main) — Landlord

Gerencia tenants, planos, assinaturas e dados do owner.

**14 tabelas:**
- Nível 1 (sem FK): `modules`, `type_contacts`, `type_documents`, `type_addresses`, `plans`, `tenants`
- Nível 2 (com FK): `people`, `users`, `subscriptions`, `contacts`, `documents`, `addresses`, `files`, `notes`

### Banco do Tenant (sc360_{slug})

Cada tenant tem 3 schemas PostgreSQL:

| Schema | Finalidade |
|--------|-----------|
| `production` | Dados reais do cliente — ambiente padrão |
| `sandbox` | Staging interno para equipe SmartClick testar alterações |
| `log` | Auditoria — registra ações (insert, update, delete) |

- O schema `public` é **removido** do banco do tenant
- Production e sandbox tem **mesma estrutura** (11 tabelas core + cache/jobs)
- Log tem apenas `audit_logs`

### Conexões (config/database.php)

```php
'landlord' => [
    'driver'   => 'pgsql',
    'database' => env('DB_DATABASE', 'sc360_main'),
    'schema'   => 'public',
]

'tenant' => [
    'driver'   => 'pgsql',
    'database' => null,         // definido em runtime
    'schema'   => 'production', // alterado em runtime
]
```

`.env`: `DB_CONNECTION=landlord`

---

## 4. Estrutura de Banco de Dados

### 4.1 Tabelas do Landlord (sc360_main)

#### tenants
- id, name, slug (unique), database_name (unique), order, status (active/suspended/cancelled), timestamps, softDeletes

#### people
- id, **tenant_id** (FK→tenants), first_name, surname, order, status, timestamps, softDeletes

#### users
- id, person_id (FK→people), email (unique), password (bcrypt), order, status, timestamps, softDeletes

#### plans
- id, name, slug (unique), description, price_monthly, price_yearly, features (JSON), max_users, order, status, timestamps, softDeletes

#### subscriptions
- id, tenant_id (FK→tenants), plan_id (FK→plans), cycle (monthly/yearly), trial_ends_at, starts_at, ends_at, order, status (trial/active/expired/cancelled), timestamps, softDeletes

#### contacts
- id, type_contact_id (FK), module_id (FK), register_id (polimórfico), value, order, status, timestamps, softDeletes

#### documents
- id, type_document_id (FK), module_id (FK), register_id (polimórfico), value, expiration_date (nullable), order, status, timestamps, softDeletes
- **SEM constraint unique** (permite CNPJ duplicado)

#### addresses
- id, type_address_id (FK), module_id (FK), register_id (polimórfico), zip_code, street, number, complement, neighborhood, city, state, country, is_main (boolean), order, status, timestamps, softDeletes

#### modules
- id, name, slug, type (module/submodule), parent_id (nullable, FK→modules), order, status, timestamps, softDeletes

#### type_contacts
- id, name, mask (nullable), order, status, timestamps, softDeletes

#### type_documents
- id, name, mask (nullable), order, status, timestamps, softDeletes

#### type_addresses
- id, name, order, status, timestamps, softDeletes

#### files
- id, module_id (FK), register_id, name, path, type, size, order, status, timestamps, softDeletes

#### notes
- id, module_id (FK), register_id, user_id (FK), title, content, order, status, timestamps, softDeletes

### 4.2 Tabelas do Tenant (schemas production e sandbox)

**Mesma estrutura do landlord, EXCETO:**
- **Não tem:** tenants, plans, subscriptions
- **people NÃO tem** tenant_id (isolamento já é por banco)
- Total: 11 tabelas core (people, users, modules, type_contacts, type_documents, type_addresses, contacts, documents, addresses, files, notes)

### 4.3 Tabela do Tenant (schema log)

#### audit_logs
- id, user_id, action (insert/update/delete), table_name, record_id, old_values (JSON), new_values (JSON), ip_address, user_agent, created_at

### 4.4 Dados de Seed

#### Modules (12 registros)
- Módulos: Pessoas, Tenants, Usuários, Produtos, Vendas, Compras, Financeiro
- Submódulos: Contatos, Documentos, Endereços, Arquivos, Notas

#### Type Contacts (4 registros)
- Email, WhatsApp (mask: (99) 99999-9999), Telefone (mask: (99) 9999-9999), Celular (mask: (99) 99999-9999)

#### Type Documents (6 registros)
- CPF (mask: 999.999.999-99), CNPJ (mask: 99.999.999/9999-99), RG, IE, IM, Passaporte

#### Type Addresses (4 registros)
- Residencial, Comercial, Entrega, Cobrança

#### Plans (3 registros)
| Plano | Mensal | Anual | Max Users | Features |
|-------|--------|-------|-----------|----------|
| Starter | R$ 97 | R$ 970 | 3 | modules: ["Pessoas","Vendas"], priority_support: false |
| Professional | R$ 197 | R$ 1.970 | 10 | modules: ["all"], priority_support: true |
| Enterprise | R$ 397 | R$ 3.970 | 50 | modules: ["all"], priority_support: true, dedicated_support: true, api_access: true |

---

## 5. Padrões de Desenvolvimento

### 5.1 Colunas Padrão em Tabelas

Todas as tabelas têm: `id`, `order`, `status`, `created_at`, `updated_at`, `deleted_at` (soft delete)

### 5.2 Gravação sem Máscara

Todos os campos com máscara são gravados **apenas com números** no banco:
- Telefone: `12997698040` (não `(12) 99769-8040`)
- CPF: `35564485807` (não `355.644.858-07`)
- CNPJ: `12345678000199` (não `12.345.678/0001-99`)
- CEP: `12345678` (não `12345-678`)

A máscara é aplicada apenas na **exibição**, usando o campo `mask` das tabelas `type_contacts` e `type_documents`.

### 5.3 Submódulos Globais (Polimórficos)

Reutilizáveis em qualquer módulo via `module_id` + `register_id`:
- **Contacts** — telefones, emails, WhatsApp
- **Documents** — CPF, CNPJ, RG, IE, IM
- **Addresses** — endereços múltiplos
- **Files** — anexos
- **Notes** — anotações

### 5.4 Controller Genérica (BaseController)

| Método | Rota | Descrição |
|--------|------|-----------|
| `index()` | GET /resource | Listagem |
| `show($id)` | GET /resource/{id} | Detalhe |
| `store(Request)` | POST /resource | Criar |
| `update(Request, $id)` | PUT /resource/{id} | Atualizar |
| `destroy($id)` | DELETE /resource/{id} | Soft delete |
| `restore($id)` | PATCH /resource/{id}/restore | Restaurar |

### 5.5 Permissões

- Granulares por módulo + ação (checkboxes)
- Sem roles fixas (nada de "admin", "vendedor")
- Tabelas: `permissions` + `user_permissions`

---

## 6. O Que Já Foi Construído

### 6.1 Arquivos Existentes

**Controllers:**
- `app/Http/Controllers/Auth/RegisterController.php` — registro + validações AJAX (checkSlug, checkEmail, checkDocument)
- `app/Http/Controllers/Auth/LoginController.php` — placeholder
- `app/Http/Controllers/PageController.php` — home, about, pricing

**Services:**
- `app/Services/TenantService.php` — provisionamento completo de tenant (411 linhas)

**Models Landlord** (14 arquivos em `app/Models/Landlord/`):
- Tenant, Person, User, Contact, Document, Address, File, Note, Subscription, Plan, Module, TypeContact, TypeDocument, TypeAddress

**Models Tenant** (11 arquivos em `app/Models/Tenant/`):
- Person (sem tenant_id), User, Contact, Document, Address, File, Note, Module, TypeContact, TypeDocument, TypeAddress

**Migrations Landlord** (14 arquivos em `database/migrations/landlord/`)

**Migrations Tenant:**
- `database/migrations/tenant/production/` — 13 arquivos
- `database/migrations/tenant/sandbox/` — 13 arquivos (idênticos)
- `database/migrations/tenant/log/` — 1 arquivo (audit_logs)

**Seeders** (7 arquivos em `database/seeders/landlord/`):
- LandlordDatabaseSeeder, ModuleSeeder, TypeContactSeeder, TypeDocumentSeeder, TypeAddressSeeder, PlanSeeder, AlexSeeder

**Comando Artisan:**
- `app/Console/Commands/TenantReset.php` — reset completo (dropa tenants + migrate:fresh + seed)

**Views:**
- `resources/views/layouts/landing.blade.php`
- `resources/views/auth/register.blade.php` (~940 linhas com JS)
- `resources/views/auth/login.blade.php`
- `resources/views/pages/home.blade.php`
- `resources/views/pages/about.blade.php`
- `resources/views/pages/pricing.blade.php`

**Rotas:**
```
GET  /              → home
GET  /about         → about
GET  /pricing       → pricing
GET  /register      → showForm
POST /register      → store
GET  /login         → showForm
POST /check-slug    → checkSlug
POST /check-email   → checkEmail
POST /check-document → checkDocument
```

### 6.2 Fluxo de Registro (Funcionando)

1. Usuário preenche formulário em `/register`
2. Validação em tempo real: slug, email, CPF/CNPJ (AJAX com debounce 500ms)
3. Máscaras removidas antes do submit (só números)
4. `RegisterController::store()` valida e chama `TenantService::createTenant()`
5. TenantService executa 6 partes:
   - A: Grava no sc360_main (tenant, person, user, 2 contacts, 1 document, subscription trial 7 dias)
   - B: Cria banco `sc360_{slug}` + 3 schemas + remove public
   - C: Roda migrations nos 3 schemas
   - D: Roda seeds em production e sandbox
   - E: Popula dados do registro em production e sandbox
   - F: Registra no audit_logs (schema log)
6. Redirect para `http://{slug}.smartclick360-v2.test/login`
7. Em caso de erro: rollback + DROP DATABASE

### 6.3 AlexSeeder (Tenant de teste)

Cria automaticamente:
- Tenant: SmartClick360, slug: smartclick360, db: sc360_main
- Person: Alex Bethel
- User: alex@smartclick360.com, senha: 12345678
- Contact WhatsApp: 12997698040
- Contact Email: alex@smartclick360.com
- Document CPF: 35564485807

### 6.4 Comando tenant:reset

```bash
php artisan tenant:reset --force
```

Faz: lista bancos de tenant → dropa cada um → migrate:fresh no landlord → roda seeders. Evita bancos órfãos.

---

## 7. Regras de Negócio

### 7.1 Multi-tenancy
- Cada tenant = 1 banco PostgreSQL exclusivo (`sc360_{slug}`)
- 3 schemas: production (dados reais), sandbox (testes internos), log (auditoria)
- Schema public é removido

### 7.2 Planos e Assinatura
- Trial: 7 dias gratuitos em todos os planos
- Após expirar: mantém acesso + exibe aviso
- Ciclos: mensal e anual
- Gateway: Asaas (cartão, boleto, PIX)

### 7.3 CPF/CNPJ
- Permite duplicação (mesmo CNPJ em múltiplas contas)
- Auto-detecta CPF (11 dígitos) ou CNPJ (14 dígitos) pelo tamanho

### 7.4 Submódulos Globais
- Contacts, Documents, Addresses, Files, Notes
- Vinculados via `module_id` + `register_id` (polimórfico por tabela modules)

---

## 8. Decisões de Arquitetura

1. **Database-per-Tenant** — isolamento total, conformidade LGPD, facilidade de backup
2. **PostgreSQL** — suporte nativo a schemas, performance, JSON, full-text search
3. **3 schemas** — production (real), sandbox (testes internos), log (auditoria sem impactar performance)
4. **Sem pacotes de multi-tenancy** — implementação própria para controle total
5. **Submódulos globais via module_id + register_id** — em vez de morphMany/polimorfismo Laravel, usa module_id para saber a qual módulo pertence e register_id para o ID do registro
6. **Gravação sem máscara** — facilita buscas e comparações
7. **Metronic 8 Demo 34** — tema profissional, só leitura na pasta fonte

---

## 9. Módulos do ERP (Planejados)

| Módulo | Descrição |
|--------|-----------|
| Pessoas | Clientes, fornecedores, usuários, vendedores + auxiliares |
| Produtos | Cadastro + auxiliares (tipo, marca, família, grupo) |
| Vendas | Pedidos, orçamentos |
| Compras | Pedidos de compra |
| Financeiro | Contas a pagar / receber + auxiliares |

---

## 10. Painel Admin (Backoffice) — Planejado

- **URL:** `admin.smartclick360.com`
- **Acesso:** Exclusivo equipe SmartClick
- **Funcionalidades:** listagem de tenants, gestão de planos, impersonate
- **Fluxo Sandbox:** dump production → sandbox → testar → aplicar em produção

---

## 11. Commits

```
904a2b5 - Fase 4: Ajustes finais - validação real-time, remoção de máscaras, comando tenant:reset
bceb867 - refactor: simplify models and add tenant schema migrations
be2998a - fix: correct field mapping and mask removal in registration
cfdd54d - feat: complete registration form with real-time validation
35f0f16 - feat: implement tenant database replication on registration
fbf8d69 - Fix: Remove duplicate users migration
6554527 - Fase 4: Implement multi-tenancy system
699ad78 - Fase 3: Implementar formulário completo de registro
3443d52 - Fase 2: Implementar layout padrão com Metronic 8 Demo 34
1a9f1ab - Fase 1: Criar estrutura básica de rotas e páginas
5230efb - Initial commit
```

---

## 12. .gitignore (Regras Adicionais)

```
.claude/
settings.local.json
test_report.md
PROJETO.md
```

---

## 13. Próximos Passos

### Fase 5 — Login + Tenant por Subdomínio
- [ ] Sistema de login completo
- [ ] Middleware IdentifyTenant (detecta tenant pelo subdomínio)
- [ ] Configuração dinâmica da conexão tenant
- [ ] Redirect pós-login para dashboard

### Fase 6 — Dashboard
- [ ] Layout dashboard com Metronic
- [ ] Métricas básicas
- [ ] Menu lateral com módulos

### Futuro
- [ ] Recuperação de senha
- [ ] 2FA
- [ ] CRUD de Pessoas
- [ ] Integração Asaas
- [ ] Painel admin (backoffice)
- [ ] Rate limiting
- [ ] Backup automático
