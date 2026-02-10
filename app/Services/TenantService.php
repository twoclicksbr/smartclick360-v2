<?php

namespace App\Services;

use App\Models\Landlord\Contact;
use App\Models\Landlord\Document;
use App\Models\Landlord\Person;
use App\Models\Landlord\Plan;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TypeContact;
use App\Models\Landlord\TypeDocument;
use App\Models\Landlord\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantService
{
    /**
     * Create a new tenant with all related data
     */
    public function create(array $data): Tenant
    {
        return DB::connection('landlord')->transaction(function () use ($data) {
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

            // 7. Create tenant database
            $this->createTenantDatabase($tenant);

            // 8. Create schemas
            $this->createSchemas($tenant);

            return $tenant;
        });
    }

    /**
     * Create tenant record
     */
    protected function createTenant(array $data): Tenant
    {
        $databaseName = 'sc360_' . $data['slug'];

        return Tenant::create([
            'company_name' => $data['company_name'],
            'slug' => $data['slug'],
            'database_name' => $databaseName,
            'domain' => null,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(15),
        ]);
    }

    /**
     * Create person record
     */
    protected function createPerson(Tenant $tenant, array $data): Person
    {
        // Determine if it's a person or company based on CPF/CNPJ
        $cpfCnpj = preg_replace('/[^0-9]/', '', $data['cpf_cnpj']);
        $type = strlen($cpfCnpj) === 11 ? 'person' : 'company';

        return Person::create([
            'tenant_id' => $tenant->id,
            'type' => $type,
            'name' => $data['first_name'] . ' ' . $data['surname'],
            'trade_name' => $type === 'company' ? $data['company_name'] : null,
            'email' => $data['email'],
            'is_active' => true,
        ]);
    }

    /**
     * Create user record
     */
    protected function createUser(Tenant $tenant, Person $person, array $data): User
    {
        return User::create([
            'tenant_id' => $tenant->id,
            'person_id' => $person->id,
            'email' => $data['email'],
            'email_verified_at' => null,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);
    }

    /**
     * Save contacts (WhatsApp and Email)
     */
    protected function saveContacts(Person $person, array $data): void
    {
        // Save WhatsApp
        $whatsappType = TypeContact::where('slug', 'whatsapp')->first();
        if ($whatsappType && !empty($data['whatsapp'])) {
            Contact::create([
                'person_id' => $person->id,
                'type_contact_id' => $whatsappType->id,
                'value' => $data['whatsapp'],
                'is_primary' => true,
            ]);
        }

        // Save Email
        $emailType = TypeContact::where('slug', 'email')->first();
        if ($emailType && !empty($data['email'])) {
            Contact::create([
                'person_id' => $person->id,
                'type_contact_id' => $emailType->id,
                'value' => $data['email'],
                'is_primary' => true,
            ]);
        }
    }

    /**
     * Save document (CPF or CNPJ)
     */
    protected function saveDocument(Person $person, array $data): void
    {
        $cpfCnpj = preg_replace('/[^0-9]/', '', $data['cpf_cnpj']);
        $documentSlug = strlen($cpfCnpj) === 11 ? 'cpf' : 'cnpj';

        $documentType = TypeDocument::where('slug', $documentSlug)->first();
        if ($documentType) {
            Document::create([
                'person_id' => $person->id,
                'type_document_id' => $documentType->id,
                'value' => $data['cpf_cnpj'],
                'is_primary' => true,
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
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addDays(15), // Trial period
                'cancelled_at' => null,
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
            'username' => env('DB_USERNAME', 'root'),
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
    }
}
