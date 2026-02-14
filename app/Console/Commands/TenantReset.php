<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:reset {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset completo do sistema: dropa todos os bancos de tenant e reseta o sc360_main';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Confirmação
        if (!$this->option('force')) {
            if (!$this->confirm('Isso vai APAGAR todos os bancos de tenant e resetar o sc360_main. Tem certeza?')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        $this->newLine();
        $this->info('🔥 Iniciando reset completo do sistema...');
        $this->newLine();

        // ==================================================
        // PARTE 1: Listar e dropar bancos de tenant
        // ==================================================
        $this->warn('📋 PARTE 1: Dropando bancos de tenant...');
        $this->newLine();

        $tenants = DB::connection('landlord')
            ->table('tenants')
            ->where('database_name', '!=', 'sc360_main')
            ->pluck('database_name');

        $droppedCount = 0;
        $errorCount = 0;

        if ($tenants->isEmpty()) {
            $this->info('   ℹ️  Nenhum banco de tenant encontrado.');
        } else {
            foreach ($tenants as $databaseName) {
                try {
                    DB::connection('landlord')->statement("DROP DATABASE IF EXISTS \"{$databaseName}\"");
                    $this->line("   ✅ Banco dropado: {$databaseName}");
                    $droppedCount++;
                } catch (\Exception $e) {
                    $this->error("   ❌ Erro ao dropar {$databaseName}: " . $e->getMessage());
                    Log::error("Erro ao dropar banco {$databaseName}: " . $e->getMessage());
                    $errorCount++;
                }
            }

            $this->newLine();

            // SE HOUVER ERRO, PARA A EXECUÇÃO IMEDIATAMENTE
            if ($errorCount > 0) {
                $this->error("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
                $this->error("❌ ERRO: Não foi possível dropar {$errorCount} banco(s)");
                $this->error("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
                $this->newLine();
                $this->warn('⚠️  ROLLBACK: Operação cancelada. Nenhuma migration foi executada.');
                $this->newLine();
                $this->info('💡 Dica: Feche todas as conexões com os bancos de tenant e tente novamente.');
                $this->info('   Isso inclui: navegador, DBeaver, pgAdmin, etc.');
                $this->newLine();
                return 1; // Código de erro
            }

            $this->line("   ✅ Total dropados: {$droppedCount}");
        }

        $this->newLine();

        // ==================================================
        // PARTE 2: Migrate:fresh no landlord
        // ==================================================
        $this->warn('📋 PARTE 2: Executando migrate:fresh no landlord...');
        $this->newLine();

        $this->call('migrate:fresh', [
            '--database' => 'landlord',
            '--path' => 'database/migrations/landlord',
            '--force' => true,
        ]);

        $this->newLine();

        // ==================================================
        // PARTE 3: Seeders
        // ==================================================
        $this->warn('📋 PARTE 3: Executando seeders...');
        $this->newLine();

        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\Landlord\\LandlordDatabaseSeeder',
            '--database' => 'landlord',
            '--force' => true,
        ]);

        $this->newLine();

        // ==================================================
        // RESUMO FINAL
        // ==================================================
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ RESET COMPLETO FINALIZADO COM SUCESSO!');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->line('📊 <fg=cyan>Resumo da operação:</>');
        $this->line('   • Bancos de tenant dropados: <fg=red>' . ($droppedCount ?? 0) . '</>');
        $this->line('   • Migrations executadas: <fg=yellow>landlord (fresh)</>');
        $this->line('   • Seeders executados: <fg=green>LandlordDatabaseSeeder</>');
        $this->newLine();

        $this->info('🎉 Sistema resetado e pronto para uso!');

        return 0;
    }
}
