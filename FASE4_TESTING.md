# Fase 4 - Multi-tenancy Implementation - Testing Guide

## ✅ Completed Implementation

### 1. Database Configuration
- ✅ Configured `landlord` connection (central database)
- ✅ Configured `tenant` connection (dynamic tenant databases)
- ✅ Updated [.env](.env) to use `landlord` as default connection

### 2. Central Database
- ⚠️ **Action Required:** Create the database `sc360_main` manually
- 📄 See instructions in [database/create_central_database.sql](database/create_central_database.sql)

### 3. Migrations
Created 14 tables in the central database:

**Level 1 (No Dependencies):**
- `modules` - ERP modules (CRM, Finance, etc.)
- `type_contacts` - Contact types (Email, Phone, WhatsApp)
- `type_documents` - Document types (CPF, CNPJ, RG)
- `type_addresses` - Address types (Home, Work, Billing)
- `plans` - Subscription plans (Starter, Professional, Enterprise)
- `tenants` - Client companies

**Level 2 (With Foreign Keys):**
- `people` - Persons and companies
- `users` - System users
- `subscriptions` - Plan subscriptions
- `contacts` - Contact information
- `documents` - Documents (CPF/CNPJ, etc.)
- `addresses` - Addresses
- `files` - File attachments
- `notes` - Notes and comments

### 4. Models
Created 14 Eloquent models in `app/Models/Landlord/`:
- All using `landlord` connection
- All with SoftDeletes
- All relationships properly defined

### 5. Seeders
Created seeders for initial data:
- `ModulesSeeder` - 6 ERP modules
- `TypeContactsSeeder` - 4 contact types
- `TypeDocumentsSeeder` - 5 document types
- `TypeAddressesSeeder` - 4 address types
- `PlansSeeder` - 3 subscription plans

### 6. TenantService
Created `app/Services/TenantService.php` with complete tenant creation flow:
1. ✅ Create tenant record
2. ✅ Create person record
3. ✅ Create user record
4. ✅ Save contacts (WhatsApp, Email)
5. ✅ Save document (CPF/CNPJ)
6. ✅ Create subscription (15-day trial)
7. ✅ Create tenant database (`sc360_{slug}`)
8. ✅ Create 3 schemas (production, sandbox, log)
9. ✅ Transaction rollback on error

### 7. RegisterController
Updated to use TenantService:
- ✅ Dependency injection
- ✅ Validation with unique checks
- ✅ Error handling
- ✅ Success redirect to login

---

## 🚀 Testing Steps

### Step 1: Create Central Database

Option 1 - Using pgAdmin:
1. Open pgAdmin
2. Right-click on "Databases" → "Create" → "Database"
3. Name: `sc360_main`
4. Encoding: UTF8
5. Click "Save"

Option 2 - Using psql:
```bash
psql -U postgres -c "CREATE DATABASE sc360_main ENCODING 'UTF8';"
```

Option 3 - Using SQL client (DBeaver, TablePlus, etc.):
```sql
CREATE DATABASE sc360_main
    ENCODING = 'UTF8'
    LC_COLLATE = 'Portuguese_Brazil.1252'
    LC_CTYPE = 'Portuguese_Brazil.1252'
    TEMPLATE = template0;
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

This will create all 14 tables in the `sc360_main` database.

### Step 3: Run Seeders

```bash
php artisan db:seed
```

This will populate:
- 6 modules
- 4 contact types
- 5 document types
- 4 address types
- 3 plans (Starter, Professional, Enterprise)

### Step 4: Test Registration Flow

1. Access: http://smartclick360-v2.test/register

2. Fill in the form:
   - Company Name: `Test Company`
   - Slug: `test-company` (will create database `sc360_test-company`)
   - First Name: `John`
   - Surname: `Doe`
   - WhatsApp: `(11) 98765-4321`
   - CPF/CNPJ: `123.456.789-00` or `12.345.678/0001-90`
   - Email: `john@test.com`
   - Password: `password123`
   - Plan: Choose any
   - Billing Cycle: Monthly or Yearly

3. Click "Criar conta"

4. Expected result:
   - Success message on login page
   - Check `sc360_main` database:
     - New record in `tenants` table
     - New record in `people` table
     - New record in `users` table
     - New records in `contacts` table (2: WhatsApp + Email)
     - New record in `documents` table (CPF or CNPJ)
     - New record in `subscriptions` table
   - New database created: `sc360_test-company`
   - Inside `sc360_test-company`:
     - Schema `production` exists
     - Schema `sandbox` exists
     - Schema `log` exists

### Step 5: Verify Database Structure

```sql
-- Check tenant was created
SELECT * FROM tenants WHERE slug = 'test-company';

-- Check person was created
SELECT * FROM people WHERE name LIKE '%John%';

-- Check user was created
SELECT * FROM users WHERE email = 'john@test.com';

-- Check contacts were saved
SELECT * FROM contacts WHERE person_id = (SELECT id FROM people WHERE name LIKE '%John%');

-- Check document was saved
SELECT * FROM documents WHERE person_id = (SELECT id FROM people WHERE name LIKE '%John%');

-- Check subscription was created
SELECT * FROM subscriptions WHERE tenant_id = (SELECT id FROM tenants WHERE slug = 'test-company');

-- List all databases
SELECT datname FROM pg_database WHERE datname LIKE 'sc360_%';

-- Check schemas in tenant database (connect to sc360_test-company first)
\dn
-- or
SELECT schema_name FROM information_schema.schemata WHERE schema_name IN ('production', 'sandbox', 'log');
```

---

## 📝 Important Notes

1. **Database Naming**: Tenant databases follow the pattern `sc360_{slug}`
2. **Trial Period**: All new registrations get 15 days of free trial
3. **Slug Validation**: Must be lowercase, alphanumeric, with hyphens only
4. **Unique Validations**: Slug and email must be unique
5. **Transaction Safety**: If any step fails, everything is rolled back
6. **Connection Usage**:
   - `landlord` connection for central database
   - `tenant` connection for tenant-specific operations (dynamic)

---

## 🐛 Troubleshooting

### Error: "Database sc360_main does not exist"
**Solution:** Create the database manually (see Step 1)

### Error: "could not connect to server"
**Solution:** Ensure PostgreSQL is running and credentials in `.env` are correct

### Error: "relation does not exist"
**Solution:** Run migrations: `php artisan migrate`

### Error: "No query results for model Plan"
**Solution:** Run seeders: `php artisan db:seed`

### Error: "permission denied to create database"
**Solution:** Ensure PostgreSQL user has CREATEDB privilege:
```sql
ALTER USER root CREATEDB;
```

---

## 🎯 Next Steps (Future Phases)

- Implement login system with tenant identification
- Create tenant dashboard
- Implement tenant data isolation
- Add tenant switching for admin users
- Implement billing and subscription management
