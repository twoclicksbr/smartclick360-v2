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
            'type_products' => [
                ['name' => 'Produto Acabado',       'type' => 'product', 'order' => 1, 'status' => true],
                ['name' => 'Matéria-Prima',         'type' => 'product', 'order' => 2, 'status' => true],
                ['name' => 'Embalagem',             'type' => 'product', 'order' => 3, 'status' => true],
                ['name' => 'Uso e Consumo',         'type' => 'product', 'order' => 4, 'status' => true],
                ['name' => 'Kit/Combo',             'type' => 'product', 'order' => 5, 'status' => true],
                ['name' => 'Serviço de Prestação',  'type' => 'service', 'order' => 6, 'status' => true],
                ['name' => 'Serviço de Mão de Obra','type' => 'service', 'order' => 7, 'status' => true],
            ],
            'units' => [
                ['name' => 'Unidade',    'abbreviation' => 'un',  'decimal_places' => 0, 'order' => 1, 'status' => true],
                ['name' => 'Quilograma', 'abbreviation' => 'kg',  'decimal_places' => 3, 'order' => 2, 'status' => true],
                ['name' => 'Grama',      'abbreviation' => 'g',   'decimal_places' => 2, 'order' => 3, 'status' => true],
                ['name' => 'Litro',      'abbreviation' => 'L',   'decimal_places' => 3, 'order' => 4, 'status' => true],
                ['name' => 'Mililitro',  'abbreviation' => 'ml',  'decimal_places' => 0, 'order' => 5, 'status' => true],
                ['name' => 'Metro',      'abbreviation' => 'm',   'decimal_places' => 2, 'order' => 6, 'status' => true],
                ['name' => 'Caixa',      'abbreviation' => 'cx',  'decimal_places' => 0, 'order' => 7, 'status' => true],
                ['name' => 'Pacote',     'abbreviation' => 'pct', 'decimal_places' => 0, 'order' => 8, 'status' => true],
            ],
            'origins' => [
                ['code' => '0', 'description' => 'Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8', 'order' => 1, 'status' => true],
                ['code' => '1', 'description' => 'Estrangeira - Importação direta, exceto a indicada no código 6', 'order' => 2, 'status' => true],
                ['code' => '2', 'description' => 'Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'order' => 3, 'status' => true],
                ['code' => '3', 'description' => 'Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%', 'order' => 4, 'status' => true],
                ['code' => '4', 'description' => 'Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos (PPB)', 'order' => 5, 'status' => true],
                ['code' => '5', 'description' => 'Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'order' => 6, 'status' => true],
                ['code' => '6', 'description' => 'Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'order' => 7, 'status' => true],
                ['code' => '7', 'description' => 'Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'order' => 8, 'status' => true],
                ['code' => '8', 'description' => 'Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%', 'order' => 9, 'status' => true],
            ],
            'cfops' => [
                ['code' => '1102', 'description' => 'Compra para comercialização', 'type' => 'entry', 'order' => 1, 'status' => true],
                ['code' => '1202', 'description' => 'Devolução de venda de mercadoria', 'type' => 'entry', 'order' => 2, 'status' => true],
                ['code' => '1352', 'description' => 'Aquisição de serviço de transporte', 'type' => 'entry', 'order' => 3, 'status' => true],
                ['code' => '1556', 'description' => 'Compra de material para uso ou consumo', 'type' => 'entry', 'order' => 4, 'status' => true],
                ['code' => '1910', 'description' => 'Entrada de bonificação, doação ou brinde', 'type' => 'entry', 'order' => 5, 'status' => true],
                ['code' => '2102', 'description' => 'Compra para comercialização (interestadual)', 'type' => 'entry', 'order' => 6, 'status' => true],
                ['code' => '2202', 'description' => 'Devolução de venda (interestadual)', 'type' => 'entry', 'order' => 7, 'status' => true],
                ['code' => '5102', 'description' => 'Venda de mercadoria adquirida', 'type' => 'exit', 'order' => 8, 'status' => true],
                ['code' => '5202', 'description' => 'Devolução de compra para comercialização', 'type' => 'exit', 'order' => 9, 'status' => true],
                ['code' => '5405', 'description' => 'Venda de mercadoria com substituição tributária', 'type' => 'exit', 'order' => 10, 'status' => true],
                ['code' => '5910', 'description' => 'Remessa em bonificação, doação ou brinde', 'type' => 'exit', 'order' => 11, 'status' => true],
                ['code' => '5949', 'description' => 'Outra saída de mercadoria não especificada', 'type' => 'exit', 'order' => 12, 'status' => true],
                ['code' => '6102', 'description' => 'Venda de mercadoria adquirida (interestadual)', 'type' => 'exit', 'order' => 13, 'status' => true],
                ['code' => '6202', 'description' => 'Devolução de compra (interestadual)', 'type' => 'exit', 'order' => 14, 'status' => true],
                ['code' => '6949', 'description' => 'Outra saída não especificada (interestadual)', 'type' => 'exit', 'order' => 15, 'status' => true],
            ],
            'tax_situations' => [
                // CST — Regime Normal
                ['code' => '00', 'description' => 'Tributada integralmente', 'regime' => 'normal', 'order' => 1, 'status' => true],
                ['code' => '10', 'description' => 'Tributada e com cobrança do ICMS por substituição tributária', 'regime' => 'normal', 'order' => 2, 'status' => true],
                ['code' => '20', 'description' => 'Com redução da base de cálculo', 'regime' => 'normal', 'order' => 3, 'status' => true],
                ['code' => '30', 'description' => 'Isenta ou não tributada e com cobrança do ICMS por substituição tributária', 'regime' => 'normal', 'order' => 4, 'status' => true],
                ['code' => '40', 'description' => 'Isenta', 'regime' => 'normal', 'order' => 5, 'status' => true],
                ['code' => '41', 'description' => 'Não tributada', 'regime' => 'normal', 'order' => 6, 'status' => true],
                ['code' => '50', 'description' => 'Suspensão', 'regime' => 'normal', 'order' => 7, 'status' => true],
                ['code' => '51', 'description' => 'Diferimento', 'regime' => 'normal', 'order' => 8, 'status' => true],
                ['code' => '60', 'description' => 'ICMS cobrado anteriormente por substituição tributária', 'regime' => 'normal', 'order' => 9, 'status' => true],
                ['code' => '70', 'description' => 'Com redução da base de cálculo e cobrança do ICMS por substituição tributária', 'regime' => 'normal', 'order' => 10, 'status' => true],
                ['code' => '90', 'description' => 'Outras', 'regime' => 'normal', 'order' => 11, 'status' => true],
                // CSOSN — Simples Nacional
                ['code' => '101', 'description' => 'Tributada com permissão de crédito', 'regime' => 'simples', 'order' => 12, 'status' => true],
                ['code' => '102', 'description' => 'Tributada sem permissão de crédito', 'regime' => 'simples', 'order' => 13, 'status' => true],
                ['code' => '103', 'description' => 'Isenção do ICMS para faixa de receita bruta', 'regime' => 'simples', 'order' => 14, 'status' => true],
                ['code' => '201', 'description' => 'Tributada com permissão de crédito e com cobrança do ICMS por ST', 'regime' => 'simples', 'order' => 15, 'status' => true],
                ['code' => '202', 'description' => 'Tributada sem permissão de crédito e com cobrança do ICMS por ST', 'regime' => 'simples', 'order' => 16, 'status' => true],
                ['code' => '203', 'description' => 'Isenção do ICMS para faixa de receita bruta e com cobrança do ICMS por ST', 'regime' => 'simples', 'order' => 17, 'status' => true],
                ['code' => '300', 'description' => 'Imune', 'regime' => 'simples', 'order' => 18, 'status' => true],
                ['code' => '400', 'description' => 'Não tributada', 'regime' => 'simples', 'order' => 19, 'status' => true],
                ['code' => '500', 'description' => 'ICMS cobrado anteriormente por ST ou por antecipação', 'regime' => 'simples', 'order' => 20, 'status' => true],
                ['code' => '900', 'description' => 'Outros', 'regime' => 'simples', 'order' => 21, 'status' => true],
            ],
            'transactions' => [
                ['name' => 'Venda', 'type' => 'sale', 'stock_movement' => 'out', 'financial_impact' => 'receivable', 'order' => 1, 'status' => true],
                ['name' => 'Compra', 'type' => 'purchase', 'stock_movement' => 'in', 'financial_impact' => 'payable', 'order' => 2, 'status' => true],
                ['name' => 'Devolução de Venda', 'type' => 'return_sale', 'stock_movement' => 'in', 'financial_impact' => 'none', 'order' => 3, 'status' => true],
                ['name' => 'Devolução de Compra', 'type' => 'return_purchase', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 4, 'status' => true],
                ['name' => 'Ajuste de Entrada', 'type' => 'adjustment_in', 'stock_movement' => 'in', 'financial_impact' => 'none', 'order' => 5, 'status' => true],
                ['name' => 'Ajuste de Saída', 'type' => 'adjustment_out', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 6, 'status' => true],
                ['name' => 'Transferência', 'type' => 'transfer', 'stock_movement' => 'none', 'financial_impact' => 'none', 'order' => 7, 'status' => true],
                ['name' => 'Bonificação', 'type' => 'bonus', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 8, 'status' => true],
                ['name' => 'Orçamento', 'type' => 'quote', 'stock_movement' => 'none', 'financial_impact' => 'none', 'order' => 9, 'status' => true],
                ['name' => 'Consignação', 'type' => 'consignment', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 10, 'status' => true],
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
