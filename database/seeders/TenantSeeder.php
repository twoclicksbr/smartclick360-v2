<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar ou atualizar tenant de demonstração
        $tenant = Tenant::updateOrCreate(
            ['slug' => 'demo'],
            [
                'database_name' => 'smartclick_demo',
                'database_logs_name' => 'smartclick_demo_logs',
                'status' => 'active',
            ]
        );

        $this->command->info("Tenant '{$tenant->slug}' criado/atualizado com sucesso!");

        // Criar os bancos de dados do tenant
        $this->createTenantDatabases($tenant);
    }

    /**
     * Criar os bancos de dados do tenant
     */
    private function createTenantDatabases(Tenant $tenant): void
    {
        // Dropar e criar banco de dados principal do tenant
        DB::statement("DROP DATABASE IF EXISTS {$tenant->database_name}");
        DB::statement("CREATE DATABASE {$tenant->database_name} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->command->info("Banco '{$tenant->database_name}' criado!");

        // Dropar e criar banco de logs do tenant
        DB::statement("DROP DATABASE IF EXISTS {$tenant->database_logs_name}");
        DB::statement("CREATE DATABASE {$tenant->database_logs_name} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->command->info("Banco '{$tenant->database_logs_name}' criado!");
    }
}
