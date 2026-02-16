<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

class TenantSyncSandbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:sync-sandbox {slug : O slug do tenant} {--force : Pular confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copia dados do schema production para sandbox de um tenant';

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

        // Pedir confirmação (a menos que --force seja usado)
        if (!$this->option('force')) {
            $confirmed = $this->confirm(
                "⚠️  Isso vai APAGAR todos os dados do sandbox do tenant '{$tenant->name}' e substituir pelos dados de production. Continuar?",
                false
            );

            if (!$confirmed) {
                $this->warn('Operação cancelada.');
                return 0;
            }
        }

        $this->newLine();
        $this->info('Iniciando sincronização...');
        $this->newLine();

        // Configurações do banco
        $database = $tenant->database_name;
        $host = config('database.connections.landlord.host');
        $port = config('database.connections.landlord.port');
        $username = config('database.connections.landlord.username');
        $password = config('database.connections.landlord.password');

        // Etapa 1: Dropar schema sandbox
        $this->info('[1/2] Dropando schema sandbox...');

        $dropCommand = "PGPASSWORD='{$password}' psql -h {$host} -p {$port} -U {$username} -d {$database} -c \"DROP SCHEMA IF EXISTS sandbox CASCADE;\"";

        $result = Process::run($dropCommand);

        if ($result->failed()) {
            $this->error('❌ Erro ao dropar schema sandbox:');
            $this->error($result->errorOutput());
            return 1;
        }

        $this->info('✓ Schema sandbox dropado com sucesso.');
        $this->newLine();

        // Etapa 2: Copiar production para sandbox
        $this->info('[2/2] Copiando dados de production para sandbox...');

        $dumpAndRestoreCommand = "PGPASSWORD='{$password}' pg_dump -h {$host} -p {$port} -U {$username} -n production {$database} | sed 's/production/sandbox/g' | PGPASSWORD='{$password}' psql -h {$host} -p {$port} -U {$username} -d {$database}";

        $result = Process::run($dumpAndRestoreCommand);

        if ($result->failed()) {
            $this->error('❌ Erro ao copiar dados:');
            $this->error($result->errorOutput());
            return 1;
        }

        $this->info('✓ Dados copiados com sucesso.');
        $this->newLine();

        // Sucesso
        $this->info("🎉 Sincronização concluída!");
        $this->info("Tenant: {$tenant->name}");
        $this->info("Database: {$database}");
        $this->info("Schema sandbox agora é uma cópia exata de production.");

        return 0;
    }
}
