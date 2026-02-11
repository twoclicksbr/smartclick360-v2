<?php

namespace App\Services;

use App\Models\Landlord\Contact;
use App\Models\Landlord\Document;
use App\Models\Landlord\Module;
use App\Models\Landlord\Person;
use App\Models\Landlord\Subscription;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\TypeContact;
use App\Models\Landlord\TypeDocument;
use App\Models\Landlord\User;
use App\Models\Landlord\Plan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantService
{
    /**
     * Cria um novo tenant completo:
     * - Grava no sc360_main (tenant, person, user, contacts, document, subscription)
     * - Cria banco de dados do tenant
     * - Cria 3 schemas (production, sandbox, log)
     * - Roda migrations em cada schema
     * - Roda seeds em production e sandbox
     * - Popula dados do registro em production e sandbox
     * - Registra audit logs
     */
    public function createTenant(array $data): Tenant
    {
        $databaseName = 'sc360_' . $data['slug'];

        DB::connection('landlord')->beginTransaction();

        try {
            // ========================================
            // PARTE A — Gravar no sc360_main
            // ========================================

            // 1. Criar tenant
            $tenant = Tenant::create([
                'name'          => $data['company_name'],
                'slug'          => $data['slug'],
                'database_name' => $databaseName,
                'order'         => 1,
                'status'        => 'active',
            ]);

            // 2. Criar person
            $person = Person::create([
                'tenant_id'  => $tenant->id,
                'first_name' => $data['first_name'],
                'surname'    => $data['surname'],
                'order'      => 1,
            ]);

            // 3. Criar user
            $user = User::create([
                'person_id' => $person->id,
                'email'     => $data['email'],
                'password'  => $data['password'],
                'order'     => 1,
            ]);

            // 4. Buscar IDs de referência
            $modulePeople = Module::where('slug', 'people')->first();
            $typeWhatsApp = TypeContact::where('name', 'WhatsApp')->first();
            $typeEmail    = TypeContact::where('name', 'Email')->first();

            // Detectar tipo de documento pelo tamanho (sem pontuação)
            $docClean = preg_replace('/\D/', '', $data['document']);
            if (strlen($docClean) <= 11) {
                $typeDoc = TypeDocument::where('name', 'CPF')->first();
            } else {
                $typeDoc = TypeDocument::where('name', 'CNPJ')->first();
            }

            // 5. Criar contact WhatsApp
            Contact::create([
                'type_contact_id' => $typeWhatsApp->id,
                'module_id'       => $modulePeople->id,
                'register_id'     => $person->id,
                'value'           => $data['phone'],
                'order'           => 1,
            ]);

            // 6. Criar contact Email
            Contact::create([
                'type_contact_id' => $typeEmail->id,
                'module_id'       => $modulePeople->id,
                'register_id'     => $person->id,
                'value'           => $data['email'],
                'order'           => 2,
            ]);

            // 7. Criar document
            Document::create([
                'type_document_id' => $typeDoc->id,
                'module_id'        => $modulePeople->id,
                'register_id'      => $person->id,
                'value'            => $data['document'],
                'order'            => 1,
            ]);

            // 8. Criar subscription (trial 7 dias)
            $plan = Plan::where('slug', $data['plan'])->first();

            Subscription::create([
                'tenant_id'     => $tenant->id,
                'plan_id'       => $plan->id,
                'cycle'         => $data['billing_cycle'],
                'trial_ends_at' => now()->addDays(7),
                'starts_at'     => now(),
                'order'         => 1,
                'status'        => 'trial',
            ]);

            DB::connection('landlord')->commit();

            // ========================================
            // PARTE B — Criar banco do tenant
            // ========================================

            DB::connection('landlord')->statement("CREATE DATABASE \"{$databaseName}\"");

            // Configurar conexão tenant dinamicamente
            $this->setTenantConnection($databaseName);

            // Criar schemas
            DB::connection('tenant')->statement('CREATE SCHEMA IF NOT EXISTS production');
            DB::connection('tenant')->statement('CREATE SCHEMA IF NOT EXISTS sandbox');
            DB::connection('tenant')->statement('CREATE SCHEMA IF NOT EXISTS log');

            // Remover schema public
            DB::connection('tenant')->statement('DROP SCHEMA IF EXISTS public CASCADE');

            // ========================================
            // PARTE C — Rodar migrations
            // ========================================

            // Migrations no schema production
            $this->setTenantSchema('production');
            Artisan::call('migrate', [
                '--path'       => 'database/migrations/tenant/production',
                '--database'   => 'tenant',
                '--force'      => true,
            ]);

            // Migrations no schema sandbox
            $this->setTenantSchema('sandbox');
            Artisan::call('migrate', [
                '--path'       => 'database/migrations/tenant/sandbox',
                '--database'   => 'tenant',
                '--force'      => true,
            ]);

            // Migration no schema log
            $this->setTenantSchema('log');
            Artisan::call('migrate', [
                '--path'       => 'database/migrations/tenant/log',
                '--database'   => 'tenant',
                '--force'      => true,
            ]);

            // ========================================
            // PARTE D — Seeds em production e sandbox
            // ========================================

            $seedData = $this->getSeedData();

            // Seeds no production
            $this->setTenantSchema('production');
            $this->runSeeds($seedData);

            // Seeds no sandbox
            $this->setTenantSchema('sandbox');
            $this->runSeeds($seedData);

            // ========================================
            // PARTE E — Popular dados do registro
            // ========================================

            $registrationData = [
                'person'   => ['first_name' => $data['first_name'], 'surname' => $data['surname']],
                'email'    => $data['email'],
                'password' => $data['password'],
                'phone'    => $data['phone'],
                'document' => $data['document'],
            ];

            // Dados no production
            $this->setTenantSchema('production');
            $productionUserId = $this->populateRegistrationData($registrationData);

            // Dados no sandbox
            $this->setTenantSchema('sandbox');
            $sandboxUserId = $this->populateRegistrationData($registrationData);

            // ========================================
            // PARTE F — Audit logs
            // ========================================

            $this->setTenantSchema('log');
            $this->logTenantCreation($productionUserId, $tenant);

            return $tenant;

        } catch (\Exception $e) {
            DB::connection('landlord')->rollBack();

            // Tentar dropar o banco se foi criado
            try {
                DB::connection('landlord')->statement("DROP DATABASE IF EXISTS \"{$databaseName}\"");
            } catch (\Exception $dropException) {
                Log::error("Falha ao dropar banco {$databaseName}: " . $dropException->getMessage());
            }

            Log::error('Erro ao criar tenant: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Configura a conexão tenant para apontar para um banco específico.
     */
    private function setTenantConnection(string $database): void
    {
        config(["database.connections.tenant.database" => $database]);
        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    /**
     * Altera o search_path do schema na conexão tenant.
     */
    private function setTenantSchema(string $schema): void
    {
        config(["database.connections.tenant.schema" => $schema]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::connection('tenant')->statement("SET search_path TO \"{$schema}\"");
    }

    /**
     * Retorna os dados de seed padrão para schemas production e sandbox.
     */
    private function getSeedData(): array
    {
        return [
            'modules' => [
                ['name' => 'Tenants',            'slug' => 'tenants',         'type' => 'module',    'order' => 1,  'status' => true],
                ['name' => 'Pessoas',            'slug' => 'people',          'type' => 'module',    'order' => 2,  'status' => true],
                ['name' => 'Usuários',           'slug' => 'users',           'type' => 'module',    'order' => 3,  'status' => true],
                ['name' => 'Planos',             'slug' => 'plans',           'type' => 'module',    'order' => 4,  'status' => true],
                ['name' => 'Tipos de Contato',   'slug' => 'type_contacts',   'type' => 'module',    'order' => 5,  'status' => true],
                ['name' => 'Tipos de Documento', 'slug' => 'type_documents',  'type' => 'module',    'order' => 6,  'status' => true],
                ['name' => 'Tipos de Endereço',  'slug' => 'type_addresses',  'type' => 'module',    'order' => 7,  'status' => true],
                ['name' => 'Contatos',           'slug' => 'contacts',        'type' => 'submodule', 'order' => 8,  'status' => true],
                ['name' => 'Documentos',         'slug' => 'documents',       'type' => 'submodule', 'order' => 9,  'status' => true],
                ['name' => 'Endereços',          'slug' => 'addresses',       'type' => 'submodule', 'order' => 10, 'status' => true],
                ['name' => 'Arquivos',           'slug' => 'files',           'type' => 'submodule', 'order' => 11, 'status' => true],
                ['name' => 'Notas',              'slug' => 'notes',           'type' => 'submodule', 'order' => 12, 'status' => true],
            ],
            'type_contacts' => [
                ['name' => 'Email',     'mask' => null,                               'order' => 1, 'status' => true],
                ['name' => 'WhatsApp',  'mask' => '(00) 00000-0000',                  'order' => 2, 'status' => true],
                ['name' => 'Telefone',  'mask' => '(00) 0000-0000|(00) 00000-0000',   'order' => 3, 'status' => true],
                ['name' => 'Celular',   'mask' => '(00) 00000-0000',                  'order' => 4, 'status' => true],
            ],
            'type_documents' => [
                ['name' => 'CPF',      'mask' => '000.000.000-00',                                'order' => 1, 'status' => true],
                ['name' => 'CNPJ',     'mask' => '00.000.000/0000-00',                             'order' => 2, 'status' => true],
                ['name' => 'CPF/CNPJ', 'mask' => '000.000.000-00|00.000.000/0000-00',              'order' => 3, 'status' => true],
                ['name' => 'RG',       'mask' => null,                                              'order' => 4, 'status' => true],
                ['name' => 'IE',       'mask' => null,                                              'order' => 5, 'status' => true],
                ['name' => 'IM',       'mask' => null,                                              'order' => 6, 'status' => true],
            ],
            'type_addresses' => [
                ['name' => 'Residencial', 'order' => 1, 'status' => true],
                ['name' => 'Comercial',   'order' => 2, 'status' => true],
                ['name' => 'Entrega',     'order' => 3, 'status' => true],
                ['name' => 'Cobrança',    'order' => 4, 'status' => true],
            ],
        ];
    }

    /**
     * Insere os seeds no schema atual da conexão tenant.
     */
    private function runSeeds(array $seedData): void
    {
        $now = now();

        foreach ($seedData as $table => $rows) {
            foreach ($rows as $row) {
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                DB::connection('tenant')->table($table)->insert($row);
            }
        }
    }

    /**
     * Popula os dados do registro (person, user, contacts, document) no schema atual.
     * Retorna o user_id criado.
     */
    private function populateRegistrationData(array $data): int
    {
        $now = now();

        // Buscar IDs no schema atual
        $modulePeople = DB::connection('tenant')->table('modules')->where('slug', 'people')->first();
        $typeWhatsApp = DB::connection('tenant')->table('type_contacts')->where('name', 'WhatsApp')->first();
        $typeEmail    = DB::connection('tenant')->table('type_contacts')->where('name', 'Email')->first();

        // Detectar tipo de documento
        $docClean = preg_replace('/\D/', '', $data['document']);
        if (strlen($docClean) <= 11) {
            $typeDoc = DB::connection('tenant')->table('type_documents')->where('name', 'CPF')->first();
        } else {
            $typeDoc = DB::connection('tenant')->table('type_documents')->where('name', 'CNPJ')->first();
        }

        // Criar person
        $personId = DB::connection('tenant')->table('people')->insertGetId([
            'first_name' => $data['person']['first_name'],
            'surname'    => $data['person']['surname'],
            'order'      => 1,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Criar user
        $userId = DB::connection('tenant')->table('users')->insertGetId([
            'person_id'  => $personId,
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
            'order'      => 1,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Criar contact WhatsApp
        DB::connection('tenant')->table('contacts')->insert([
            'type_contact_id' => $typeWhatsApp->id,
            'module_id'       => $modulePeople->id,
            'register_id'     => $personId,
            'value'           => $data['phone'],
            'order'           => 1,
            'status'          => true,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // Criar contact Email
        DB::connection('tenant')->table('contacts')->insert([
            'type_contact_id' => $typeEmail->id,
            'module_id'       => $modulePeople->id,
            'register_id'     => $personId,
            'value'           => $data['email'],
            'order'           => 2,
            'status'          => true,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        // Criar document
        DB::connection('tenant')->table('documents')->insert([
            'type_document_id' => $typeDoc->id,
            'module_id'        => $modulePeople->id,
            'register_id'      => $personId,
            'value'            => $data['document'],
            'order'            => 1,
            'status'           => true,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        return $userId;
    }

    /**
     * Registra os logs de criação do tenant no schema log.
     */
    private function logTenantCreation(int $userId, Tenant $tenant): void
    {
        $now = now();

        DB::connection('tenant')->table('audit_logs')->insert([
            'user_id'     => $userId,
            'action'      => 'insert',
            'table_name'  => 'tenants',
            'record_id'   => $tenant->id,
            'old_values'  => null,
            'new_values'  => json_encode([
                'name'          => $tenant->name,
                'slug'          => $tenant->slug,
                'database_name' => $tenant->database_name,
            ]),
            'status_code' => 200,
            'ip_address'  => request()->ip() ?? '127.0.0.1',
            'user_agent'  => request()->userAgent() ?? 'system',
            'created_at'  => $now,
        ]);
    }
}
