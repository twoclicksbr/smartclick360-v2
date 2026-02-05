<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personTypes = [
            [
                'name' => 'Cliente',
                'slug' => 'customer',
                'color' => '#3b82f6',
                'icon' => 'user',
                'description' => 'Pessoa que compra produtos ou serviços',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fornecedor',
                'slug' => 'supplier',
                'color' => '#8b5cf6',
                'icon' => 'truck',
                'description' => 'Pessoa que fornece produtos ou serviços',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Funcionário',
                'slug' => 'employee',
                'color' => '#10b981',
                'icon' => 'briefcase',
                'description' => 'Colaborador da empresa',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Parceiro',
                'slug' => 'partner',
                'color' => '#f59e0b',
                'icon' => 'handshake',
                'description' => 'Parceiro de negócios',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lead',
                'slug' => 'lead',
                'color' => '#ec4899',
                'icon' => 'star',
                'description' => 'Potencial cliente',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::connection('tenant')->table('person_types')->insert($personTypes);
    }
}
