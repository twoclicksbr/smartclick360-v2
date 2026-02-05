<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Ativo',
                'slug' => 'active',
                'color' => '#10b981',
                'icon' => 'check-circle',
                'description' => 'Registro ativo',
                'order' => 1,
                'is_default' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Inativo',
                'slug' => 'inactive',
                'color' => '#6b7280',
                'icon' => 'x-circle',
                'description' => 'Registro inativo',
                'order' => 2,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bloqueado',
                'slug' => 'blocked',
                'color' => '#ef4444',
                'icon' => 'ban',
                'description' => 'Registro bloqueado',
                'order' => 3,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pendente',
                'slug' => 'pending',
                'color' => '#f59e0b',
                'icon' => 'clock',
                'description' => 'Aguardando aprovação',
                'order' => 4,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('statuses')->insert($statuses);
    }
}
