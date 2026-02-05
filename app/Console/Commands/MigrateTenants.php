<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MigrateTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate {--fresh : Drop all tables and re-run all migrations} {--seed : Seed the database after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa migrations em todos os bancos de dados dos tenants ativos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenants = Tenant::active()->get();

        if ($tenants->isEmpty()) {
            $this->warn('Nenhum tenant ativo encontrado.');
            return self::FAILURE;
        }

        $this->info("Encontrados {$tenants->count()} tenant(s) ativo(s).");
        $this->newLine();

        foreach ($tenants as $tenant) {
            $this->info("📦 Processando tenant: {$tenant->slug}");
            $this->line("   Banco: {$tenant->database_name}");

            try {
                // Configurar a conexão para o banco do tenant
                Config::set('database.connections.tenant.database', $tenant->database_name);
                DB::purge('tenant');

                // Executar migrations
                if ($this->option('fresh')) {
                    $this->line("   → Executando migrate:fresh...");
                    Artisan::call('migrate:fresh', [
                        '--database' => 'tenant',
                        '--force' => true,
                    ]);
                } else {
                    $this->line("   → Executando migrate...");
                    Artisan::call('migrate', [
                        '--database' => 'tenant',
                        '--force' => true,
                    ]);
                }

                $this->info("   ✓ Migrations executadas com sucesso!");

                // Executar seeders se solicitado
                if ($this->option('seed')) {
                    $this->line("   → Executando seeders...");
                    Artisan::call('db:seed', [
                        '--database' => 'tenant',
                        '--force' => true,
                    ]);
                    $this->info("   ✓ Seeders executados com sucesso!");
                }

                $this->newLine();

            } catch (\Exception $e) {
                $this->error("   ✗ Erro ao processar tenant {$tenant->slug}: {$e->getMessage()}");
                $this->newLine();
                continue;
            }
        }

        $this->info('✅ Processo concluído!');
        return self::SUCCESS;
    }
}
