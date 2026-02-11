<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Tenant\PeopleFakeSeeder;

class TenantSeedFake extends Command
{
    protected $signature = 'tenant:seed-fake {slug}';
    protected $description = 'Popula o banco do tenant com dados fake';

    public function handle()
    {
        $slug = $this->argument('slug');

        // Busca o tenant no landlord
        $tenant = DB::connection('landlord')
            ->table('tenants')
            ->where('slug', $slug)
            ->first();

        if (!$tenant) {
            $this->error("✗ Tenant '{$slug}' não encontrado!");
            return 1;
        }

        $databaseName = $tenant->database_name;

        $this->info("📦 Populando banco: {$databaseName}");
        $this->info("🔗 Schema: production");
        $this->newLine();

        // Configura a conexão tenant dinamicamente
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.schema', 'production');

        // Reconecta
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Verifica se a conexão foi bem-sucedida
        try {
            DB::connection('tenant')->getPdo();
            $this->info("✓ Conectado ao banco {$databaseName}");
            $this->newLine();
        } catch (\Exception $e) {
            $this->error("✗ Erro ao conectar: {$e->getMessage()}");
            return 1;
        }

        // Roda o seeder
        try {
            $seeder = new PeopleFakeSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->newLine();
            $this->info("🎉 Dados fake criados com sucesso!");
            return 0;
        } catch (\Exception $e) {
            $this->error("✗ Erro ao popular: {$e->getMessage()}");
            return 1;
        }
    }
}
