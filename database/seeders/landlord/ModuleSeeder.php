<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Tenants',            'slug' => 'tenants',         'type' => 'module',    'order' => 1],
            ['name' => 'Pessoas',            'slug' => 'people',          'type' => 'module',    'order' => 2],
            ['name' => 'Usuários',           'slug' => 'users',           'type' => 'module',    'order' => 3],
            ['name' => 'Planos',             'slug' => 'plans',           'type' => 'module',    'order' => 4],
            ['name' => 'Tipos de Contato',   'slug' => 'type_contacts',   'type' => 'module',    'order' => 5],
            ['name' => 'Tipos de Documento', 'slug' => 'type_documents',  'type' => 'module',    'order' => 6],
            ['name' => 'Tipos de Endereço',  'slug' => 'type_addresses',  'type' => 'module',    'order' => 7],
            ['name' => 'Contatos',           'slug' => 'contacts',        'type' => 'submodule', 'order' => 8],
            ['name' => 'Documentos',         'slug' => 'documents',       'type' => 'submodule', 'order' => 9],
            ['name' => 'Endereços',          'slug' => 'addresses',       'type' => 'submodule', 'order' => 10],
            ['name' => 'Arquivos',           'slug' => 'files',           'type' => 'submodule', 'order' => 11],
            ['name' => 'Notas',              'slug' => 'notes',           'type' => 'submodule', 'order' => 12],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
