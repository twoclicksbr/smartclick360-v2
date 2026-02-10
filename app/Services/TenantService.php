<?php

namespace App\Services;

use App\Models\Landlord\Contact;
use App\Models\Landlord\Document;
use App\Models\Landlord\Module;
use App\Models\Landlord\Person;
use App\Models\Landlord\Plan;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TypeAddress;
use App\Models\Landlord\TypeContact;
use App\Models\Landlord\TypeDocument;
use App\Models\Landlord\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantService
{
    /**
     * Create a new tenant with all related data
     */
    public function create(array $data): Tenant
    {
        // Step 1-6: Create records in central database (with transaction)
        $tenant = DB::connection('landlord')->transaction(function () use ($data) {
            // 1. Create tenant
            $tenant = $this->createTenant($data);

            // 2. Create person
            $person = $this->createPerson($tenant, $data);

            // 3. Create user
            $user = $this->createUser($tenant, $person, $data);

            // 4. Save contacts
            $this->saveContacts($person, $data);

            // 5. Save document
            $this->saveDocument($person, $data);

            // 6. Create subscription
            $this->createSubscription($tenant, $data);

            return $tenant;
        });

        // Step 7-11: Create tenant database, schemas, migrations and replicate data (OUTSIDE transaction)
        try {
            // 7. Create tenant database
            $this->createTenantDatabase($tenant);

            // 8. Create schemas
            $this->createSchemas($tenant);

            // 9. Run migrations in tenant database
            $this->runTenantMigrations($tenant);

            // 10. Seed types (modules, type_contacts, type_documents, type_addresses)
            $this->seedTenantTypes($tenant);

            // 11. Replicate registration data to tenant database
            $person = Person::where('tenant_id', $tenant->id)->first();
            if ($person) {
                $this->replicatePersonToTenant($tenant, $person);
            }

            return $tenant;
        } catch (\Exception $e) {
            // If database/schema creation fails, delete the tenant and related records
            $tenant->forceDelete();
            throw $e;
        }
    }

    /**
     * Create tenant record
     */
    protected function createTenant(array $data): Tenant
    {
        $databaseName = 'sc360_' . $data['slug'];

        return Tenant::create([
            'name' => $data['company_name'],
            'slug' => $data['slug'],
            'database_name' => $databaseName,
            'order' => 0,
            'status' => 'active',
        ]);
    }

    /**
     * Create person record
     */
    protected function createPerson(Tenant $tenant, array $data): Person
    {
        return Person::create([
            'tenant_id' => $tenant->id,
            'first_name' => $data['first_name'],
            'surname' => $data['surname'],
            'order' => 0,
            'status' => true,
        ]);
    }

    /**
     * Create user record
     */
    protected function createUser(Tenant $tenant, Person $person, array $data): User
    {
        return User::create([
            'person_id' => $person->id,
            'email' => $data['email'],
            'password' => $data['password'],
            'order' => 0,
            'status' => true,
        ]);
    }

    /**
     * Save contacts (WhatsApp and Email)
     */
    protected function saveContacts(Person $person, array $data): void
    {
        // Get the 'people' module
        $peopleModule = Module::where('slug', 'people')->first();
        if (!$peopleModule) {
            return;
        }

        // Save WhatsApp
        $whatsappType = TypeContact::where('name', 'WhatsApp')->first();
        if ($whatsappType && !empty($data['whatsapp'])) {
            Contact::create([
                'type_contact_id' => $whatsappType->id,
                'module_id' => $peopleModule->id,
                'register_id' => $person->id,
                'value' => $data['whatsapp'],
                'order' => 1,
                'status' => true,
            ]);
        }

        // Save Email
        $emailType = TypeContact::where('name', 'Email')->first();
        if ($emailType && !empty($data['email'])) {
            Contact::create([
                'type_contact_id' => $emailType->id,
                'module_id' => $peopleModule->id,
                'register_id' => $person->id,
                'value' => $data['email'],
                'order' => 2,
                'status' => true,
            ]);
        }
    }

    /**
     * Save document (CPF or CNPJ)
     */
    protected function saveDocument(Person $person, array $data): void
    {
        // Get the 'people' module
        $peopleModule = Module::where('slug', 'people')->first();
        if (!$peopleModule) {
            return;
        }

        $cpfCnpj = preg_replace('/[^0-9]/', '', $data['cpf_cnpj']);
        $documentName = strlen($cpfCnpj) === 11 ? 'CPF' : 'CNPJ';

        $documentType = TypeDocument::where('name', $documentName)->first();
        if ($documentType) {
            Document::create([
                'type_document_id' => $documentType->id,
                'module_id' => $peopleModule->id,
                'register_id' => $person->id,
                'value' => $data['cpf_cnpj'],
                'order' => 1,
                'status' => true,
            ]);
        }
    }

    /**
     * Create subscription with trial period
     */
    protected function createSubscription(Tenant $tenant, array $data): void
    {
        $plan = Plan::where('slug', $data['plan'])->first();

        if ($plan) {
            Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'cycle' => $data['billing_cycle'] ?? 'monthly',
                'trial_ends_at' => now()->addDays(7),
                'starts_at' => now(),
                'ends_at' => now()->addDays(7),
                'order' => 0,
                'status' => 'trial',
            ]);
        }
    }

    /**
     * Create tenant database
     */
    protected function createTenantDatabase(Tenant $tenant): void
    {
        $databaseName = $tenant->database_name;

        // Create database using PDO to avoid connection issues
        $pdo = DB::connection('landlord')->getPdo();
        $pdo->exec("CREATE DATABASE {$databaseName} ENCODING 'UTF8'");
    }

    /**
     * Create schemas in tenant database
     */
    protected function createSchemas(Tenant $tenant): void
    {
        $databaseName = $tenant->database_name;

        // Configure temporary connection to tenant database
        config(['database.connections.temp_tenant' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => $databaseName,
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ]]);

        // Create schemas
        DB::connection('temp_tenant')->statement('CREATE SCHEMA IF NOT EXISTS production');
        DB::connection('temp_tenant')->statement('CREATE SCHEMA IF NOT EXISTS sandbox');
        DB::connection('temp_tenant')->statement('CREATE SCHEMA IF NOT EXISTS log');

        // Remove public schema
        DB::connection('temp_tenant')->statement('DROP SCHEMA IF EXISTS public CASCADE');
    }

    /**
     * Run migrations in tenant database
     */
    protected function runTenantMigrations(Tenant $tenant): void
    {
        $databaseName = $tenant->database_name;

        // Configure tenant connection
        config(['database.connections.tenant' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => $databaseName,
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'production',
            'sslmode' => 'prefer',
        ]]);

        // Run migrations from tenant folder
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);
    }

    /**
     * Seed types from landlord to tenant database
     */
    protected function seedTenantTypes(Tenant $tenant): void
    {
        // Get all types from landlord
        $modules = Module::all();
        $typeContacts = TypeContact::all();
        $typeDocuments = TypeDocument::all();
        $typeAddresses = TypeAddress::all();

        // Insert into tenant database
        foreach ($modules as $module) {
            DB::connection('tenant')->table('production.modules')->insert([
                'id' => $module->id,
                'name' => $module->name,
                'slug' => $module->slug,
                'order' => $module->order,
                'status' => $module->status,
                'created_at' => $module->created_at,
                'updated_at' => $module->updated_at,
            ]);
        }

        foreach ($typeContacts as $type) {
            DB::connection('tenant')->table('production.type_contacts')->insert([
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
                'order' => $type->order,
                'status' => $type->status,
                'created_at' => $type->created_at,
                'updated_at' => $type->updated_at,
            ]);
        }

        foreach ($typeDocuments as $type) {
            DB::connection('tenant')->table('production.type_documents')->insert([
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
                'order' => $type->order,
                'status' => $type->status,
                'created_at' => $type->created_at,
                'updated_at' => $type->updated_at,
            ]);
        }

        foreach ($typeAddresses as $type) {
            DB::connection('tenant')->table('production.type_addresses')->insert([
                'id' => $type->id,
                'name' => $type->name,
                'slug' => $type->slug,
                'order' => $type->order,
                'status' => $type->status,
                'created_at' => $type->created_at,
                'updated_at' => $type->updated_at,
            ]);
        }
    }

    /**
     * Replicate person and related data to tenant database
     */
    protected function replicatePersonToTenant(Tenant $tenant, Person $person): void
    {
        // Insert person (without tenant_id)
        DB::connection('tenant')->table('production.people')->insert([
            'id' => $person->id,
            'first_name' => $person->first_name,
            'surname' => $person->surname,
            'order' => $person->order,
            'status' => $person->status,
            'created_at' => $person->created_at,
            'updated_at' => $person->updated_at,
        ]);

        // Insert user
        $user = User::where('person_id', $person->id)->first();
        if ($user) {
            DB::connection('tenant')->table('production.users')->insert([
                'id' => $user->id,
                'person_id' => $user->person_id,
                'email' => $user->email,
                'password' => $user->password,
                'order' => $user->order,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }

        // Get the 'people' module
        $peopleModule = Module::where('slug', 'people')->first();
        if (!$peopleModule) {
            return;
        }

        // Insert contacts
        $contacts = Contact::where('module_id', $peopleModule->id)
            ->where('register_id', $person->id)
            ->get();

        foreach ($contacts as $contact) {
            DB::connection('tenant')->table('production.contacts')->insert([
                'id' => $contact->id,
                'type_contact_id' => $contact->type_contact_id,
                'module_id' => $contact->module_id,
                'register_id' => $contact->register_id,
                'value' => $contact->value,
                'order' => $contact->order,
                'status' => $contact->status,
                'created_at' => $contact->created_at,
                'updated_at' => $contact->updated_at,
            ]);
        }

        // Insert documents
        $documents = Document::where('module_id', $peopleModule->id)
            ->where('register_id', $person->id)
            ->get();

        foreach ($documents as $document) {
            DB::connection('tenant')->table('production.documents')->insert([
                'id' => $document->id,
                'type_document_id' => $document->type_document_id,
                'module_id' => $document->module_id,
                'register_id' => $document->register_id,
                'value' => $document->value,
                'order' => $document->order,
                'status' => $document->status,
                'created_at' => $document->created_at,
                'updated_at' => $document->updated_at,
            ]);
        }
    }
}
