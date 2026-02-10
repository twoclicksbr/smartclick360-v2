<?php

namespace Database\Seeders;

use App\Models\Landlord\Module;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'CRM',
                'slug' => 'crm',
                'order' => 0,
                'status' => true,
            ],
            [
                'name' => 'Financeiro',
                'slug' => 'finance',
                'order' => 1,
                'status' => true,
            ],
            [
                'name' => 'Estoque',
                'slug' => 'inventory',
                'order' => 2,
                'status' => true,
            ],
            [
                'name' => 'Vendas',
                'slug' => 'sales',
                'order' => 3,
                'status' => true,
            ],
            [
                'name' => 'Compras',
                'slug' => 'purchases',
                'order' => 4,
                'status' => true,
            ],
            [
                'name' => 'RH',
                'slug' => 'hr',
                'order' => 5,
                'status' => true,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
