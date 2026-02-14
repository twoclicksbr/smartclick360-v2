# SmartClick360 v2 — Contexto do Projeto

**Última atualização:** 14/02/2026

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
| 5 | Login + identificação de tenant por subdomínio | ✅ Concluída |
| 6 | Dashboard inicial do tenant | ✅ Concluída |
| 7 | CRUD de Pessoas completo | ✅ Concluída |
| 8 | Sistema de encoding de IDs (URL-safe) | ✅ Concluída |
| 9 | Backoffice landlord (gestão de tenants) | ✅ Concluída |
| 10 | Componentes reutilizáveis e sistema modular | ✅ Concluída |
| 11+ | Demais módulos do ERP | 🔲 Pendente |

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

## 4. Sistema de Autenticação (Guards)

O Laravel foi configurado com **2 guards** separados para autenticação:

### Guard 'web' — Landlord (Admin)

**Finalidade:** Autenticação do backoffice (equipe SmartClick)

**Configuração:**
```php
'web' => [
    'driver' => 'session',
    'provider' => 'users',
]

'users' => [
    'driver' => 'eloquent',
    'model' => App\Models\Landlord\User::class,
]
```

**Conexão:** `landlord` (banco sc360_main)

**Login:** `http://smartclick360-v2.test/login`

**Middleware:** `auth:web`

**Uso:**
```php
Auth::guard('web')->attempt($credentials);
$user = Auth::guard('web')->user();
```

### Guard 'tenant' — Tenant

**Finalidade:** Autenticação dos usuários do tenant

**Configuração:**
```php
'tenant' => [
    'driver' => 'session',
    'provider' => 'tenant_users',
]

'tenant_users' => [
    'driver' => 'eloquent',
    'model' => App\Models\Tenant\User::class,
]
```

**Conexão:** `tenant` (configurado dinamicamente pelo middleware IdentifyTenant)

**Login:** `http://{slug}.smartclick360-v2.test/login`

**Middleware:** `auth:tenant` (sempre usado com `identify.tenant`)

**Uso:**
```php
Auth::guard('tenant')->attempt($credentials);
$user = Auth::guard('tenant')->user();
```

### Fluxo de Autenticação

**Landlord (Admin):**
1. Acessa `smartclick360-v2.test/login`
2. Submete credenciais
3. LandlordLoginController usa guard 'web'
4. Autentica contra `sc360_main.users`
5. Redirect para `/dashboard`

**Tenant:**
1. Acessa `{slug}.smartclick360-v2.test/login`
2. Middleware IdentifyTenant configura conexão tenant
3. Submete credenciais
4. LoginController usa guard 'tenant'
5. Autentica contra `sc360_{slug}.production.users`
6. Redirect para `/dashboard/main`

---

## 5. Estrutura de Banco de Dados

### 5.1 Tabelas do Landlord (sc360_main)

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

### 5.2 Tabelas do Tenant (schemas production e sandbox)

**Mesma estrutura do landlord, EXCETO:**
- **Não tem:** tenants, plans, subscriptions
- **people NÃO tem** tenant_id (isolamento já é por banco)
- Total: 11 tabelas core (people, users, modules, type_contacts, type_documents, type_addresses, contacts, documents, addresses, files, notes)

### 5.3 Tabela do Tenant (schema log)

#### audit_logs
- id, user_id, action (insert/update/delete), table_name, record_id, old_values (JSON), new_values (JSON), ip_address, user_agent, created_at

### 5.4 Dados de Seed

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

### 5.5 Sistema de Encoding de IDs (URL-Safe)

Para evitar exposição de IDs sequenciais nas URLs, foi implementado um sistema de encoding:

**Funções:**
- `encodeId($id)` — converte ID numérico em string URL-safe
- `decodeId($encoded)` — converte string de volta para ID numérico

**Implementação:**
- Base64 modificado (substitui `+/` por `-_` e remove padding `=`)
- Exemplo: ID `2` vira `Mg`, ID `50` vira `NTA`

**Uso em rotas:**
```php
// Gerar link
route('module.show', ['slug' => $slug, 'module' => 'people', 'code' => encodeId($person->id)])
// Resultado: /people/Mg

// Recuperar ID no controller
$id = decodeId($code);
$person = Person::findOrFail($id);
```

**Benefícios:**
- Oculta quantidade de registros no sistema
- Dificulta enumeração de recursos
- URLs mais profissionais
- Mantém compatibilidade com findOrFail (após decode)

### 5.6 Permissões (Planejado)

- Granulares por módulo + ação (checkboxes)
- Sem roles fixas (nada de "admin", "vendedor")
- Tabelas: `permissions` + `user_permissions`

---

## 6. Sistema Modular de Controllers e Componentes

### 6.1 Arquitetura de Roteamento Modular

O sistema usa uma arquitetura de **delegação inteligente** onde:

1. **ModuleController** — roteador principal que recebe todas as requisições de módulos
2. **Controllers específicos** — implementam lógica personalizada por módulo (ex: PeopleController)
3. **Fallback genérico** — se não houver controller específico, usa lógica padrão

**Fluxo:**
```
URL: /people
  ↓
Route: {module} → ModuleController@index
  ↓
ModuleController verifica se existe PeopleController
  ↓
Se SIM: delega para PeopleController@index
Se NÃO: executa lógica genérica (abort 404 por enquanto)
```

**Vantagens:**
- Adicionar novos módulos é simples: basta criar o controller específico
- Rotas genéricas já estão definidas (não precisa duplicar)
- Lógica específica fica isolada no controller do módulo

### 6.2 SubmoduleController — CRUD Genérico

O SubmoduleController implementa CRUD completo para os 5 submódulos globais:
- Contacts (telefone, email, WhatsApp)
- Documents (CPF, CNPJ, RG, IE, IM)
- Addresses (residencial, comercial, entrega, cobrança)
- Files (upload de arquivos)
- Notes (observações)

**Características:**
- Validação específica por tipo de submódulo
- Remoção automática de máscaras antes de salvar
- Suporte a AJAX (retorna JSON)
- Soft delete
- Reordenação (drag and drop)

**Exemplo de uso:**
```javascript
// Adicionar contato
POST /people/Mg/contacts
{
  "type_contact_id": 1,
  "value": "(12) 99769-8040"  // máscara removida automaticamente
}
```

### 6.3 Componentes Blade Reutilizáveis

Foram criados 10 componentes reutilizáveis para evitar duplicação de código:

**Componentes de Tabela:**
- `table-checkbox` — checkbox para seleção em massa
- `table-sortable-handle` — handle de drag and drop (ícone de 6 pontos)
- `table-row-actions` — botões de ação (editar, deletar, restaurar)
- `status-badge` — badge verde/vermelho de status

**Componentes de Interface:**
- `action-button` — botão genérico com ícone + texto
- `bulk-actions` — dropdown de ações em massa
- `quick-search` — campo de busca rápida no header
- `search-modal` — modal de busca avançada
- `pagination-info` — "Mostrando X de Y resultados"

**Componentes Especializados:**
- `people-table` — tabela AJAX de pessoas (carrega via AJAX sem refresh)

**Exemplo de uso:**
```blade
<x-tenant-status-badge :status="$person->status" />
<x-tenant-table-row-actions :module="'people'" :code="encodeId($person->id)" />
```

---

## 7. O Que Já Foi Construído

### 7.1 Arquivos Existentes

**Controllers** (10 arquivos):
- `app/Http/Controllers/PageController.php` — landing pages (home, about, pricing)
- `app/Http/Controllers/Auth/RegisterController.php` — registro + validações AJAX (checkSlug, checkEmail, checkDocument)
- `app/Http/Controllers/Auth/LoginController.php` — login do tenant (autenticação no guard 'tenant')
- `app/Http/Controllers/Auth/LandlordLoginController.php` — login do admin (autenticação no guard 'web')
- `app/Http/Controllers/Landlord/TenantManagementController.php` — gestão de tenants (index, show)
- `app/Http/Controllers/Tenant/TenantController.php` — configurações do tenant
- `app/Http/Controllers/Tenant/PeopleController.php` — CRUD específico de pessoas (index, store, update, show, showFiles)
- `app/Http/Controllers/Tenant/ModuleController.php` — roteador genérico para módulos (delega para controllers específicos)
- `app/Http/Controllers/Tenant/SubmoduleController.php` — CRUD genérico para submódulos (contacts, documents, addresses, files, notes)
- `app/Http/Controllers/Controller.php` — base controller do Laravel

**Middleware** (1 arquivo):
- `app/Http/Middleware/IdentifyTenant.php` — identifica tenant pelo subdomínio, configura conexão dinâmica, valida status

**Services** (1 arquivo):
- `app/Services/TenantService.php` — provisionamento completo de tenant (create database, schemas, migrations, seeds, audit)

**Helpers** (1 arquivo):
- `app/Helpers/helpers.php` — funções auxiliares:
  - `format_phone()` — formata telefone BR
  - `format_document()` — formata CPF/CNPJ
  - `format_cep()` — formata CEP
  - `encodeId()` — codifica ID para URL-safe (base64 modificado)
  - `decodeId()` — decodifica ID de URL-safe

**Models Landlord** (14 arquivos em `app/Models/Landlord/`):
- Tenant, Person, User, Contact, Document, Address, File, Note, Subscription, Plan, Module, TypeContact, TypeDocument, TypeAddress

**Models Tenant** (11 arquivos em `app/Models/Tenant/`):
- Person (sem tenant_id), User, Contact, Document, Address, File, Note, Module, TypeContact, TypeDocument, TypeAddress

**Migrations Landlord** (15 arquivos em `database/migrations/landlord/`):
- 14 tabelas + 1 migration de índices de performance

**Migrations Tenant:**
- `database/migrations/tenant/production/` — 14 arquivos (11 tabelas + cache + jobs + índices)
- `database/migrations/tenant/sandbox/` — 14 arquivos (idênticos aos de production)
- `database/migrations/tenant/log/` — 1 arquivo (audit_logs)

**Seeders** (14 arquivos):
- `database/seeders/landlord/` — 7 seeders (LandlordDatabaseSeeder, ModuleSeeder, TypeContactSeeder, TypeDocumentSeeder, TypeAddressSeeder, PlanSeeder, AlexSeeder)
- `database/seeders/tenant/` — 1 seeder (PeopleFakeSeeder - gera 50 pessoas fake com contatos)
- `database/seeders/` — 6 seeders (DatabaseSeeder, ModulesSeeder, TypeContactsSeeder, TypeDocumentsSeeder, TypeAddressesSeeder, PlansSeeder)

**Comandos Artisan** (2 arquivos):
- `app/Console/Commands/TenantReset.php` — reset completo (dropa tenants + migrate:fresh + seed)
- `app/Console/Commands/TenantSeedFake.php` — popula tenant com dados fake (`php artisan tenant:seed-fake {slug}`)

**Layouts Blade** (4 arquivos):
- `resources/views/layouts/landing.blade.php` — layout das páginas públicas
- `resources/views/layouts/dashboard.blade.php` — (deprecated, não usado)
- `resources/views/layouts/tenant.blade.php` — (deprecated, não usado)
- `resources/views/tenant/layouts/app.blade.php` — layout principal do tenant
- `resources/views/landlord/layouts/app.blade.php` — layout principal do landlord

**Componentes Tenant** (10 componentes reutilizáveis em `resources/views/tenant/components/`):
- `action-button.blade.php` — botão de ação genérico
- `bulk-actions.blade.php` — ações em massa (deletar, exportar)
- `pagination-info.blade.php` — informação de paginação
- `people-table.blade.php` — tabela de pessoas (AJAX)
- `quick-search.blade.php` — busca rápida no header
- `search-modal.blade.php` — modal de busca avançada
- `status-badge.blade.php` — badge de status (ativo/inativo)
- `table-checkbox.blade.php` — checkbox para seleção em massa
- `table-row-actions.blade.php` — ações de linha (editar, deletar, restaurar)
- `table-sortable-handle.blade.php` — handle de drag and drop

**Views Auth** (3 arquivos):
- `resources/views/auth/register.blade.php` — formulário de registro (~940 linhas com JS)
- `resources/views/auth/login.blade.php` — login do tenant
- `resources/views/auth/landlord-login.blade.php` — login do admin

**Views Landing** (3 arquivos):
- `resources/views/pages/home.blade.php`
- `resources/views/pages/about.blade.php`
- `resources/views/pages/pricing.blade.php`

**Views Landlord** (3 arquivos):
- `resources/views/landlord/dashboard.blade.php` — dashboard do admin
- `resources/views/landlord/tenants/index.blade.php` — listagem de tenants (grid com cards)
- `resources/views/landlord/tenants/show.blade.php` — detalhes de um tenant

**Views Tenant** (28+ arquivos):
- Dashboard: `resources/views/tenant/pages/dashboard/main.blade.php`
- Settings: `resources/views/tenant/pages/settings.blade.php`
- People (5 arquivos):
  - `resources/views/tenant/pages/people/index.blade.php` — listagem com busca avançada
  - `resources/views/tenant/pages/people/show.blade.php` — detalhes (abas: visão geral, documentos, endereços, observações)
  - `resources/views/tenant/pages/people/show-files.blade.php` — aba de arquivos
  - `resources/views/tenant/pages/people/_navbar.blade.php` — navbar de navegação entre abas
  - `resources/views/tenant/pages/people/forms/people.blade.php` — formulário de pessoa
- Layouts (19 arquivos):
  - `resources/views/tenant/layouts/app.blade.php` — layout principal
  - `resources/views/tenant/layouts/head.blade.php` — meta tags e CSS
  - `resources/views/tenant/layouts/header.blade.php` — header com menu
  - `resources/views/tenant/layouts/toolbar.blade.php` — toolbar de breadcrumb
  - `resources/views/tenant/layouts/footer.blade.php`
  - `resources/views/tenant/layouts/scrolltop.blade.php`
  - `resources/views/tenant/layouts/script.blade.php` — scripts JS
  - Drawers (4 arquivos): `index.blade.php`, `activities.blade.php`, `chat.blade.php`, `shopping-cart.blade.php`
  - Modals (6 arquivos):
    - `index.blade.php`, `help.blade.php`, `modal-module.blade.php`, `modal-submodule.blade.php`
    - Forms: `contact.blade.php`, `document.blade.php`, `address.blade.php`, `note.blade.php`, `file.blade.php`
  - Menu: `wrapper/user.blade.php`

**Rotas** (`routes/web.php`):

Domínio principal (`smartclick360-v2.test`):
```
GET  /              → home
GET  /about         → about
GET  /pricing       → pricing
GET  /register      → showForm
POST /register      → store
POST /check-slug    → checkSlug (AJAX)
POST /check-email   → checkEmail (AJAX)
POST /check-document → checkDocument (AJAX)
GET  /login         → landlord login form
POST /login         → landlord authenticate
POST /logout        → landlord logout
GET  /dashboard     → landlord dashboard (auth:web)
GET  /tenants       → lista tenants (auth:web)
GET  /tenants/{code} → detalhes tenant (auth:web)
```

Subdomínio tenant (`{slug}.smartclick360-v2.test`):
```
Middleware: identify.tenant (todos)

GET  /              → redirect to login
GET  /login         → tenant login form
POST /login         → tenant authenticate
POST /logout        → tenant logout

Área protegida (auth:tenant):
GET  /dashboard/main     → dashboard
GET  /settings           → configurações

Submódulos (rotas genéricas AJAX para contacts, documents, addresses, files, notes):
POST   {module}/{code}/{submodule}/reorder
GET    {module}/{code}/{submodule}
POST   {module}/{code}/{submodule}
GET    {module}/{code}/{submodule}/{s_code}
PUT    {module}/{code}/{submodule}/{s_code}
DELETE {module}/{code}/{submodule}/{s_code}
PATCH  {module}/{code}/{submodule}/{s_code}/restore

Módulos (rotas genéricas delegadas para controllers específicos):
GET    {module}                → index (ex: /people)
GET    {module}/create         → create
POST   {module}                → store
GET    {module}/{code}         → show (ex: /people/Mg)
GET    {module}/{code}/edit    → edit
PUT    {module}/{code}         → update
DELETE {module}/{code}         → destroy
POST   {module}/reorder        → reorder (drag and drop)
PATCH  {module}/{code}/restore → restore

Rotas específicas:
GET  people/{code}/files → showFiles
```

### 7.2 Fluxo de Registro (Funcionando)

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

### 7.3 AlexSeeder (Tenant de teste)

Cria automaticamente:
- Tenant: SmartClick360, slug: smartclick360, db: sc360_main
- Person: Alex Bethel
- User: alex@smartclick360.com, senha: 12345678
- Contact WhatsApp: 12997698040
- Contact Email: alex@smartclick360.com
- Document CPF: 35564485807

### 7.4 Comandos Artisan

#### tenant:reset

```bash
php artisan tenant:reset --force
```

Faz: lista bancos de tenant → dropa cada um → migrate:fresh no landlord → roda seeders. Evita bancos órfãos.

#### tenant:seed-fake

```bash
php artisan tenant:seed-fake {slug}
```

Popula o banco do tenant com 50 pessoas fake (nomes brasileiros + WhatsApp). Útil para testes de performance e UI.

---

## 8. Regras de Negócio

### 8.1 Multi-tenancy
- Cada tenant = 1 banco PostgreSQL exclusivo (`sc360_{slug}`)
- 3 schemas: production (dados reais), sandbox (testes internos), log (auditoria)
- Schema public é removido

### 8.2 Planos e Assinatura
- Trial: 7 dias gratuitos em todos os planos
- Após expirar: mantém acesso + exibe aviso
- Ciclos: mensal e anual
- Gateway: Asaas (cartão, boleto, PIX)

### 8.3 CPF/CNPJ
- Permite duplicação (mesmo CNPJ em múltiplas contas)
- Auto-detecta CPF (11 dígitos) ou CNPJ (14 dígitos) pelo tamanho

### 8.4 Submódulos Globais
- Contacts, Documents, Addresses, Files, Notes
- Vinculados via `module_id` + `register_id` (polimórfico por tabela modules)

---

## 9. Decisões de Arquitetura

1. **Database-per-Tenant** — isolamento total, conformidade LGPD, facilidade de backup
2. **PostgreSQL** — suporte nativo a schemas, performance, JSON, full-text search
3. **3 schemas** — production (real), sandbox (testes internos), log (auditoria sem impactar performance)
4. **Sem pacotes de multi-tenancy** — implementação própria para controle total
5. **Submódulos globais via module_id + register_id** — em vez de morphMany/polimorfismo Laravel, usa module_id para saber a qual módulo pertence e register_id para o ID do registro
6. **Gravação sem máscara** — facilita buscas e comparações
7. **Metronic 8 Demo 34** — tema profissional, só leitura na pasta fonte

---

## 10. Módulos do ERP (Planejados)

| Módulo | Descrição |
|--------|-----------|
| Pessoas | Clientes, fornecedores, usuários, vendedores + auxiliares |
| Produtos | Cadastro + auxiliares (tipo, marca, família, grupo) |
| Vendas | Pedidos, orçamentos |
| Compras | Pedidos de compra |
| Financeiro | Contas a pagar / receber + auxiliares |

---

## 11. Painel Admin (Backoffice)

### Status: Implementação Básica ✅

**Funcionalidades Implementadas:**
- ✅ Login exclusivo para admin (guard 'web', autenticação no sc360_main)
- ✅ Dashboard do landlord
- ✅ Listagem de tenants (grid com cards mostrando nome, slug, status, plano)
- ✅ Visualização de detalhes do tenant (dados, assinatura, plano)

**Acesso:**
- URL local: `http://smartclick360-v2.test/login`
- Credenciais (AlexSeeder): `alex@smartclick360.com` / `12345678`

**Funcionalidades Planejadas:**
- [ ] Gestão de planos (criar, editar, desativar)
- [ ] Impersonate (se passar por tenant)
- [ ] Gestão de assinaturas (pausar, cancelar, trocar plano)
- [ ] Dashboard com métricas (MRR, churn, novos tenants)
- [ ] Fluxo Sandbox: dump production → sandbox → testar → aplicar em produção
- [ ] Logs de ações do admin

---

## 12. Commits (Últimos 20)

```
a77306d - feat: implement URL-safe ID encoding system
9c76d40 - feat: add person detail page with charts, file management and reusable components
fffd6d2 - feat: add tenant components, people CRUD and fake data seeder
a3e5b4a - feat: implementar gestão de credenciais e dashboard do tenant
7813d7f - chore: add PROJETO.md to gitignore
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
5230efb - Initial commit - SmartClick360 v2 project setup
```

---

## 13. .gitignore (Regras Adicionais)

```
.claude/
settings.local.json
test_report.md
PROJETO.md
```

---

## 14. Funcionalidades Implementadas

### 14.1 Módulo de Pessoas (CRUD Completo)

**Listagem:**
- Tabela com paginação (25, 50, 100 registros por página)
- Busca rápida (header) — busca por nome ou ID
- Busca avançada (modal) — filtros por ID, nome, status, data, deletados
- Ordenação por coluna (ID, nome, status, data)
- Seleção em massa (checkboxes)
- Ações em massa (deletar múltiplos)
- Drag and drop para reordenar (campo `order`)
- Badge de status (ativo/inativo)

**Detalhes:**
- Página de detalhes com abas:
  - **Visão Geral:** avatar, nome, data nascimento, botões de ação
  - **Contatos:** lista de contatos (email, telefone, WhatsApp) — CRUD via modal AJAX
  - **Documentos:** lista de documentos (CPF, CNPJ, RG, IE) — CRUD via modal AJAX
  - **Endereços:** lista de endereços (residencial, comercial, etc) — CRUD via modal AJAX
  - **Arquivos:** upload e gestão de arquivos — página separada
  - **Observações:** anotações livres — CRUD via modal AJAX
- Navegação entre abas sem reload
- Formulários modais para criar/editar submódulos
- Validações em tempo real

**Criação/Edição:**
- Formulário modal
- Upload de avatar (jpeg, png, jpg — max 2MB)
- Preview de avatar
- Validações de campos obrigatórios
- Status ativo/inativo
- Redirect para página de detalhes após salvar

**Soft Delete:**
- Exclusão lógica (campo `deleted_at`)
- Opção de incluir deletados na busca
- Restauração de registros deletados

### 14.2 Sistema de Upload de Arquivos

**Características:**
- Upload via formulário ou drag and drop
- Armazenamento em `storage/app/public/tenants/{slug}/`
- Controle de tipos MIME
- Limite de tamanho configurável
- Registro na tabela `files` (nome, path, mime_type, size)
- Download de arquivos
- Exclusão física e lógica

**Tipos de Upload:**
- Avatar de pessoa (pasta `avatars/`)
- Arquivos gerais (pasta `files/`)

### 14.3 Sistema de Busca Avançada

**Implementação:**
- Modal com formulário de filtros
- Filtros combinados (AND)
- Busca case-insensitive (ILIKE no PostgreSQL)
- Operadores de busca: contains, starts_with, exact
- Filtro por range de datas (daterangepicker)
- Filtro por status
- Opção de incluir deletados
- Persistência de filtros na paginação (query string)
- Reset de filtros

**Performance:**
- Índices criados em colunas de busca frequente
- Eager loading de relacionamentos
- Paginação eficiente

### 14.4 Validações em Tempo Real

**Registro:**
- Validação de slug (AJAX com debounce 500ms)
- Validação de email (AJAX)
- Validação de CPF/CNPJ (AJAX)
- Feedback visual (ícones de sucesso/erro)

**Submódulos:**
- Validação de email único por pessoa
- Validação de campos obrigatórios
- Validação de formato de email
- Remoção automática de máscaras antes de salvar

### 14.5 Gestão de Assinaturas

**Trial:**
- 7 dias gratuitos em todos os planos
- Criado automaticamente no registro
- Campo `trial_ends_at` no banco

**Planos:**
- 3 planos (Starter, Professional, Enterprise)
- Ciclos mensais e anuais
- Features em JSON (módulos disponíveis, suporte prioritário, API)
- Max users por plano

**Status:**
- trial, active, expired, cancelled
- Validação na autenticação (planejado)

---

## 15. Próximos Passos

### Fase 11 — Módulo de Produtos
- [ ] Tabelas: products, product_categories, product_brands
- [ ] CRUD completo de produtos
- [ ] Gestão de estoque básica
- [ ] Upload de imagens de produtos

### Fase 12 — Módulo de Vendas
- [ ] Tabelas: sales, sale_items
- [ ] Criação de orçamentos
- [ ] Conversão de orçamento em venda
- [ ] Relatório de vendas

### Fase 13 — Módulo Financeiro
- [ ] Tabelas: financial_accounts, transactions
- [ ] Contas a pagar
- [ ] Contas a receber
- [ ] Fluxo de caixa

### Fase 14 — Integração Asaas
- [ ] Webhook para atualização de status de pagamento
- [ ] Criação de assinaturas no Asaas
- [ ] Gestão de cartão de crédito
- [ ] Boleto e PIX

### Melhorias e Features Futuras
- [ ] Recuperação de senha (tenant e landlord)
- [ ] Autenticação em dois fatores (2FA)
- [ ] Sistema de permissões granulares
- [ ] Módulo de relatórios (charts e gráficos)
- [ ] Exportação de dados (CSV, Excel, PDF)
- [ ] Auditoria completa (logs de todas as ações)
- [ ] Notificações em tempo real (websockets)
- [ ] API REST para integrações
- [ ] Rate limiting
- [ ] Backup automático diário
- [ ] Impersonate (admin se passar por tenant)
- [ ] Modo sandbox completo no landlord
- [ ] Testes automatizados (Pest/PHPUnit)
