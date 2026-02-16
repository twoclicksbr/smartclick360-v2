<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class TenantMigrateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-all {--schema=production : Schema para migrar (production ou sandbox)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Roda migrations em todos os tenants ativos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Buscar todos os tenants ativos
        $this->info('Buscando tenants ativos...');

        $tenants = DB::connection('landlord')->table('tenants')
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->get();

        if ($tenants->isEmpty()) {
            $this->warn('⚠️  Nenhum tenant ativo encontrado.');
            return 0;
        }

        $total = $tenants->count();
        $this->info("✓ {$total} tenant(s) encontrado(s).");
        $this->newLine();

        // Validar schema
        $schema = $this->option('schema');

        if (!in_array($schema, ['production', 'sandbox'])) {
            $this->error("❌ Schema inválido: '{$schema}'. Use 'production' ou 'sandbox'.");
            return 1;
        }

        $this->info("Schema: {$schema}");
        $this->newLine();

        // Contadores
        $success = 0;
        $errors = 0;

        // Processar cada tenant
        foreach ($tenants as $index => $tenant) {
            $current = $index + 1;
            $this->info("[{$current}/{$total}] Migrando: {$tenant->name} ({$tenant->database_name})...");

            try {
                // Configurar conexão tenant em runtime
                Config::set('database.connections.tenant.database', $tenant->database_name);
                Config::set('database.connections.tenant.schema', $schema);
                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::connection('tenant')->statement("SET search_path TO {$schema}");

                // Rodar migrations
                $path = "database/migrations/tenant/{$schema}";

                $this->call('migrate', [
                    '--database' => 'tenant',
                    '--path' => $path,
                    '--force' => true,
                ]);

                $this->info("✓ {$tenant->name} migrado com sucesso.");
                $success++;

            } catch (Exception $e) {
                $this->error("❌ Erro ao migrar {$tenant->name}: " . $e->getMessage());
                $errors++;
            }

            $this->newLine();
        }

        // Resumo final
        $this->info('═══════════════════════════════════════');
        $this->info("🎉 Migração concluída!");
        $this->info("Schema: {$schema}");
        $this->info("Total de tenants: {$total}");
        $this->info("Sucesso: {$success}");

        if ($errors > 0) {
            $this->error("Erros: {$errors}");
        } else {
            $this->info("Erros: 0");
        }

        $this->info('═══════════════════════════════════════');

        return $errors > 0 ? 1 : 0;
    }
}
