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
                'icon' => 'ki-profile-circle',
                'description' => 'Gestão de Relacionamento com Cliente',
                'is_active' => true,
            ],
            [
                'name' => 'Financeiro',
                'slug' => 'finance',
                'icon' => 'ki-dollar',
                'description' => 'Gestão Financeira e Contábil',
                'is_active' => true,
            ],
            [
                'name' => 'Estoque',
                'slug' => 'inventory',
                'icon' => 'ki-package',
                'description' => 'Controle de Estoque e Produtos',
                'is_active' => true,
            ],
            [
                'name' => 'Vendas',
                'slug' => 'sales',
                'icon' => 'ki-chart-line-up',
                'description' => 'Gestão de Vendas e Pedidos',
                'is_active' => true,
            ],
            [
                'name' => 'Compras',
                'slug' => 'purchases',
                'icon' => 'ki-basket',
                'description' => 'Gestão de Compras e Fornecedores',
                'is_active' => true,
            ],
            [
                'name' => 'RH',
                'slug' => 'hr',
                'icon' => 'ki-people',
                'description' => 'Recursos Humanos e Folha de Pagamento',
                'is_active' => true,
            ],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
