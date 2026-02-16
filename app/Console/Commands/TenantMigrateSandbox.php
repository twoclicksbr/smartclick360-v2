<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantMigrateSandbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-sandbox {slug : O slug do tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Roda migrations do schema sandbox de um tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $slug = $this->argument('slug');

        // Buscar tenant no banco landlord
        $this->info("Buscando tenant com slug: {$slug}...");

        $tenant = DB::connection('landlord')
            ->table('tenants')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        if (!$tenant) {
            $this->error("❌ Tenant '{$slug}' não encontrado.");
            return 1;
        }

        $this->info("✓ Tenant encontrado: {$tenant->name} (DB: {$tenant->database_name})");
        $this->newLine();

        // Configurar conexão tenant em runtime para usar o schema sandbox
        $this->info('Configurando conexão com schema sandbox...');

        Config::set('database.connections.tenant.database', $tenant->database_name);
        Config::set('database.connections.tenant.schema', 'sandbox');
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::connection('tenant')->statement("SET search_path TO sandbox");

        $this->info('✓ Conexão configurada.');
        $this->newLine();

        // Rodar migrations do sandbox
        $this->info('Rodando migrations do schema sandbox...');
        $this->newLine();

        $this->call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant/sandbox',
            '--force' => true,
        ]);

        $this->newLine();

        // Sucesso
        $this->info("🎉 Migrations do sandbox executadas com sucesso!");
        $this->info("Tenant: {$tenant->name}");
        $this->info("Database: {$tenant->database_name}");
        $this->info("Schema: sandbox");

        return 0;
    }
}
