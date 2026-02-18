<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $modules = [
            // Módulos
            ['name' => 'Pessoas',       'slug' => 'people',        'type' => 'module',    'scope' => 'both',     'icon' => 'ki-outline ki-people',        'order' => 1],
            ['name' => 'Produtos',      'slug' => 'products',      'type' => 'module',    'scope' => 'both',     'icon' => 'ki-outline ki-package',       'order' => 2],
            ['name' => 'Vendas',        'slug' => 'sales',         'type' => 'module',    'scope' => 'tenant',   'icon' => 'ki-outline ki-handcart',      'order' => 3],
            ['name' => 'Compras',       'slug' => 'purchases',     'type' => 'module',    'scope' => 'tenant',   'icon' => 'ki-outline ki-purchase',      'order' => 4],
            ['name' => 'Financeiro',    'slug' => 'financial',     'type' => 'module',    'scope' => 'tenant',   'icon' => 'ki-outline ki-dollar',        'order' => 5],
            ['name' => 'Tenants',       'slug' => 'tenants',       'type' => 'module',    'scope' => 'landlord', 'icon' => 'ki-outline ki-abstract-26',   'order' => 6],
            ['name' => 'Planos',        'slug' => 'plans',         'type' => 'module',    'scope' => 'landlord', 'icon' => 'ki-outline ki-crown',         'order' => 7],
            ['name' => 'Assinaturas',   'slug' => 'subscriptions', 'type' => 'module',    'scope' => 'landlord', 'icon' => 'ki-outline ki-calendar-tick', 'order' => 8],
            // Submódulos
            ['name' => 'Contatos',      'slug' => 'contacts',      'type' => 'submodule', 'scope' => 'both',     'icon' => 'ki-outline ki-phone',         'order' => 9],
            ['name' => 'Documentos',    'slug' => 'documents',     'type' => 'submodule', 'scope' => 'both',     'icon' => 'ki-outline ki-document',      'order' => 10],
            ['name' => 'Endereços',     'slug' => 'addresses',     'type' => 'submodule', 'scope' => 'both',     'icon' => 'ki-outline ki-geolocation',   'order' => 11],
            ['name' => 'Arquivos',      'slug' => 'files',         'type' => 'submodule', 'scope' => 'both',     'icon' => 'ki-outline ki-folder',        'order' => 12],
            ['name' => 'Notas',         'slug' => 'notes',         'type' => 'submodule', 'scope' => 'both',     'icon' => 'ki-outline ki-notepad',       'order' => 13],
        ];

        foreach ($modules as $module) {
            DB::connection('landlord')->table('modules')->insert(array_merge($module, [
                'model'                  => 'Genérica',
                'show_drag'              => true,
                'show_checkbox'          => true,
                'show_actions'           => true,
                'default_sort_field'     => 'id',
                'default_sort_direction' => 'asc',
                'per_page'              => 25,
                'status'                => true,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]));
        }
    }
}
