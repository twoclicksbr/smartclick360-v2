<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleFieldUiSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Buscar ID do módulo People
        $peopleId = DB::connection('landlord')
            ->table('modules')
            ->where('slug', 'people')
            ->value('id');

        if (!$peopleId) {
            return;
        }

        // Buscar IDs dos fields pelo name
        $fields = DB::connection('landlord')
            ->table('module_fields')
            ->where('module_id', $peopleId)
            ->pluck('id', 'name');

        $uiFields = [
            // Campos automáticos
            [
                'module_field_id' => $fields['id'] ?? null,
                'component'       => 'input',
                'visible_index'   => true,
                'visible_show'    => true,
                'visible_create'  => false,
                'visible_edit'    => false,
                'width_index'     => 'w-5',
                'sortable'        => true,
                'searchable'      => true,
                'order'           => 1,
            ],
            [
                'module_field_id' => $fields['created_at'] ?? null,
                'component'       => 'datetime',
                'visible_index'   => false,
                'visible_show'    => true,
                'visible_create'  => false,
                'visible_edit'    => false,
                'order'           => 997,
            ],
            [
                'module_field_id' => $fields['updated_at'] ?? null,
                'component'       => 'datetime',
                'visible_index'   => false,
                'visible_show'    => true,
                'visible_create'  => false,
                'visible_edit'    => false,
                'order'           => 998,
            ],
            [
                'module_field_id' => $fields['deleted_at'] ?? null,
                'component'       => 'datetime',
                'visible_index'   => false,
                'visible_show'    => false,
                'visible_create'  => false,
                'visible_edit'    => false,
                'order'           => 999,
            ],
            // Campos do desenvolvedor
            [
                'module_field_id' => $fields['first_name'] ?? null,
                'component'       => 'input',
                'placeholder'     => 'Digite o nome',
                'grid_col'        => 'col-md-6',
                'visible_index'   => true,
                'visible_show'    => true,
                'visible_create'  => true,
                'visible_edit'    => true,
                'width_index'     => 'w-25',
                'grid_template'   => '{first_name} {surname}',
                'grid_link'       => '{show}',
                'searchable'      => true,
                'sortable'        => true,
                'order'           => 2,
            ],
            [
                'module_field_id' => $fields['surname'] ?? null,
                'component'       => 'input',
                'placeholder'     => 'Digite o sobrenome',
                'grid_col'        => 'col-md-6',
                'visible_index'   => false,
                'visible_show'    => true,
                'visible_create'  => true,
                'visible_edit'    => true,
                'searchable'      => true,
                'sortable'        => true,
                'order'           => 3,
            ],
            [
                'module_field_id' => $fields['birth_date'] ?? null,
                'component'       => 'date',
                'placeholder'     => 'DD/MM/AAAA',
                'grid_col'        => 'col-md-6',
                'visible_index'   => false,
                'visible_show'    => true,
                'visible_create'  => true,
                'visible_edit'    => true,
                'searchable'      => false,
                'sortable'        => false,
                'order'           => 4,
            ],
            [
                'module_field_id' => $fields['status'] ?? null,
                'component'       => 'switch',
                'options'         => json_encode([
                    '1' => ['label' => 'Ativo',   'badge' => 'success'],
                    '0' => ['label' => 'Inativo', 'badge' => 'danger'],
                ]),
                'grid_col'        => 'col-md-6',
                'visible_index'   => true,
                'visible_show'    => true,
                'visible_create'  => true,
                'visible_edit'    => true,
                'width_index'     => 'w-10',
                'searchable'      => true,
                'sortable'        => false,
                'order'           => 5,
            ],
        ];

        foreach ($uiFields as $ui) {
            if (!$ui['module_field_id']) {
                continue;
            }

            DB::connection('landlord')->table('module_fields_ui')->insert(array_merge([
                'options'           => null,
                'placeholder'       => null,
                'mask'              => null,
                'icon'              => null,
                'tooltip'           => null,
                'tooltip_direction' => 'top',
                'grid_col'          => 'col-md-12',
                'visible_index'     => false,
                'visible_show'      => false,
                'visible_create'    => true,
                'visible_edit'      => true,
                'width_index'       => null,
                'grid_template'     => null,
                'grid_link'         => null,
                'grid_actions'      => null,
                'searchable'        => false,
                'sortable'          => false,
                'status'            => true,
                'created_at'        => $now,
                'updated_at'        => $now,
            ], $ui));
        }
    }
}
