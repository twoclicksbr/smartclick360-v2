<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSubmoduleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Buscar IDs dos módulos pelo slug
        $modules = DB::connection('landlord')
            ->table('modules')
            ->pluck('id', 'slug');

        $links = [
            // People → 5 submódulos
            ['module' => 'people', 'submodule' => 'contacts',  'order' => 1],
            ['module' => 'people', 'submodule' => 'documents', 'order' => 2],
            ['module' => 'people', 'submodule' => 'addresses', 'order' => 3],
            ['module' => 'people', 'submodule' => 'files',     'order' => 4],
            ['module' => 'people', 'submodule' => 'notes',     'order' => 5],
            // Products → 2 submódulos
            ['module' => 'products', 'submodule' => 'files', 'order' => 1],
            ['module' => 'products', 'submodule' => 'notes', 'order' => 2],
        ];

        foreach ($links as $link) {
            $moduleId = $modules[$link['module']] ?? null;
            $submoduleId = $modules[$link['submodule']] ?? null;

            if ($moduleId && $submoduleId) {
                DB::connection('landlord')->table('module_submodules')->insert([
                    'module_id'    => $moduleId,
                    'submodule_id' => $submoduleId,
                    'order'        => $link['order'],
                    'status'       => true,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }
    }
}
