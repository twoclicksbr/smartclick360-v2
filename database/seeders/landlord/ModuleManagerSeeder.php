<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleManagerSeeder extends Seeder
{
    public function run(): void
    {
        // A) Inserir módulo "Módulos"
        $moduleId = DB::connection('landlord')->table('modules')->insertGetId([
            'name' => 'Módulos',
            'slug' => 'modules',
            'type' => 'module',
            'scope' => 'landlord',
            'icon' => 'ki-outline ki-element-11',
            'model' => null,
            'service' => null,
            'controller' => null,
            'show_drag' => true,
            'show_checkbox' => true,
            'show_actions' => true,
            'default_sort_field' => 'order',
            'default_sort_direction' => 'asc',
            'per_page' => 25,
            'description_index' => 'Gerencie os módulos do sistema',
            'description_show' => 'Visualize e edite as configurações do módulo',
            'description_create' => 'Crie um novo módulo personalizado',
            'description_edit' => 'Edite as configurações do módulo',
            'view_index' => null,
            'view_show' => 'landlord.pages.modules.show',
            'view_modal' => 'landlord.pages.modules._modal',
            'after_store' => 'index',
            'after_update' => 'index',
            'after_restore' => 'edit',
            'default_checked' => false,
            'origin' => 'system',
            'order' => 1,
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // B) Inserir fields do módulo
        $fields = [
            [
                'name' => 'name',
                'label' => 'Nome',
                'type' => 'string',
                'required' => true,
                'unique' => false,
                'order' => 1,
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
                'type' => 'string',
                'required' => true,
                'unique' => true,
                'order' => 2,
            ],
            [
                'name' => 'type',
                'label' => 'Tipo',
                'type' => 'string',
                'required' => true,
                'unique' => false,
                'order' => 3,
            ],
            [
                'name' => 'scope',
                'label' => 'Escopo',
                'type' => 'string',
                'required' => true,
                'unique' => false,
                'order' => 4,
            ],
            [
                'name' => 'icon',
                'label' => 'Ícone',
                'type' => 'string',
                'required' => false,
                'unique' => false,
                'order' => 5,
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'boolean',
                'required' => false,
                'unique' => false,
                'order' => 6,
            ],
            [
                'name' => 'order',
                'label' => 'Ordem',
                'type' => 'integer',
                'required' => false,
                'unique' => false,
                'order' => 7,
            ],
        ];

        $fieldIds = [];

        foreach ($fields as $field) {
            $fieldIds[$field['name']] = DB::connection('landlord')->table('module_fields')->insertGetId([
                'module_id' => $moduleId,
                'main' => false,
                'is_custom' => false,
                'icon' => null,
                'name' => $field['name'],
                'label' => $field['label'],
                'type' => $field['type'],
                'length' => null,
                'precision' => null,
                'default' => null,
                'nullable' => !$field['required'],
                'required' => $field['required'],
                'unique' => $field['unique'],
                'index' => false,
                'fk_table' => null,
                'fk_column' => null,
                'fk_label' => null,
                'auto_from' => null,
                'auto_type' => null,
                'min' => null,
                'max' => null,
                'order' => $field['order'],
                'status' => true,
                'origin' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // C) Inserir fields_ui para cada field
        $fieldsUi = [
            'name' => [
                'component' => 'input',
                'options' => null,
                'grid_col' => 'col-md-6',
                'visible_index' => true,
                'visible_show' => true,
                'visible_create' => true,
                'visible_edit' => true,
                'searchable' => true,
                'sortable' => true,
                'order' => 1,
            ],
            'slug' => [
                'component' => 'input',
                'options' => null,
                'grid_col' => 'col-md-6',
                'visible_index' => true,
                'visible_show' => true,
                'visible_create' => true,
                'visible_edit' => true,
                'searchable' => true,
                'sortable' => true,
                'order' => 2,
            ],
            'type' => [
                'component' => 'select',
                'options' => json_encode([
                    'module' => 'Módulo',
                    'submodule' => 'Submódulo',
                ]),
                'grid_col' => 'col-md-6',
                'visible_index' => true,
                'visible_show' => true,
                'visible_create' => true,
                'visible_edit' => true,
                'searchable' => false,
                'sortable' => true,
                'order' => 3,
            ],
            'scope' => [
                'component' => 'select',
                'options' => json_encode([
                    'tenant' => 'Tenant',
                    'landlord' => 'Landlord',
                ]),
                'grid_col' => 'col-md-6',
                'visible_index' => true,
                'visible_show' => true,
                'visible_create' => true,
                'visible_edit' => true,
                'searchable' => false,
                'sortable' => true,
                'order' => 4,
            ],
            'icon' => [
                'component' => 'input',
                'options' => null,
                'grid_col' => 'col-md-12',
                'visible_index' => false,
                'visible_show' => true,
                'visible_create' => true,
                'visible_edit' => true,
                'searchable' => false,
                'sortable' => false,
                'order' => 5,
            ],
            'status' => [
                'component' => 'switch',
                'options' => json_encode([
                    '1' => [
                        'label' => 'Ativo',
                        'badge' => 'badge-light-success',
                    ],
                    '0' => [
                        'label' => 'Inativo',
                        'badge' => 'badge-light-danger',
                    ],
                ]),
                'grid_col' => 'col-md-6',
                'visible_index' => true,
                'visible_show' => true,
                'visible_create' => true,
                'visible_edit' => true,
                'searchable' => false,
                'sortable' => true,
                'order' => 6,
            ],
            'order' => [
                'component' => 'input',
                'options' => null,
                'grid_col' => 'col-md-6',
                'visible_index' => false,
                'visible_show' => false,
                'visible_create' => false,
                'visible_edit' => false,
                'searchable' => false,
                'sortable' => true,
                'order' => 7,
            ],
        ];

        foreach ($fieldsUi as $fieldName => $ui) {
            DB::connection('landlord')->table('module_fields_ui')->insert([
                'module_field_id' => $fieldIds[$fieldName],
                'component' => $ui['component'],
                'options' => $ui['options'],
                'placeholder' => null,
                'mask' => null,
                'icon' => null,
                'tooltip' => null,
                'tooltip_direction' => 'top',
                'grid_col' => $ui['grid_col'],
                'visible_index' => $ui['visible_index'],
                'visible_show' => $ui['visible_show'],
                'visible_create' => $ui['visible_create'],
                'visible_edit' => $ui['visible_edit'],
                'width_index' => null,
                'grid_template' => null,
                'grid_link' => null,
                'grid_actions' => null,
                'searchable' => $ui['searchable'],
                'sortable' => $ui['sortable'],
                'order' => $ui['order'],
                'status' => true,
                'origin' => 'system',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
