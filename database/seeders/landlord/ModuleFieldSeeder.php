<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleFieldSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $peopleId = DB::connection('landlord')
            ->table('modules')
            ->where('slug', 'people')
            ->value('id');

        if (!$peopleId) {
            return;
        }

        $fields = [
            // Campos automáticos (main = true)
            [
                'module_id' => $peopleId,
                'main'      => true,
                'is_custom' => false,
                'name'      => 'id',
                'label'     => 'ID',
                'type'      => 'integer',
                'nullable'  => false,
                'required'  => false,
                'unique'    => false,
                'index'     => false,
                'order'     => 1,
            ],
            [
                'module_id' => $peopleId,
                'main'      => true,
                'is_custom' => false,
                'name'      => 'created_at',
                'label'     => 'Criado em',
                'type'      => 'datetime',
                'nullable'  => true,
                'required'  => false,
                'unique'    => false,
                'index'     => false,
                'order'     => 997,
            ],
            [
                'module_id' => $peopleId,
                'main'      => true,
                'is_custom' => false,
                'name'      => 'updated_at',
                'label'     => 'Atualizado em',
                'type'      => 'datetime',
                'nullable'  => true,
                'required'  => false,
                'unique'    => false,
                'index'     => false,
                'order'     => 998,
            ],
            [
                'module_id' => $peopleId,
                'main'      => true,
                'is_custom' => false,
                'name'      => 'deleted_at',
                'label'     => 'Deletado em',
                'type'      => 'datetime',
                'nullable'  => true,
                'required'  => false,
                'unique'    => false,
                'index'     => false,
                'order'     => 999,
            ],
            // Campos do desenvolvedor (main = false)
            [
                'module_id' => $peopleId,
                'main'      => false,
                'is_custom' => false,
                'name'      => 'first_name',
                'label'     => 'Nome',
                'type'      => 'string',
                'length'    => 255,
                'nullable'  => false,
                'required'  => true,
                'unique'    => false,
                'index'     => false,
                'order'     => 2,
            ],
            [
                'module_id' => $peopleId,
                'main'      => false,
                'is_custom' => false,
                'name'      => 'surname',
                'label'     => 'Sobrenome',
                'type'      => 'string',
                'length'    => 255,
                'nullable'  => false,
                'required'  => true,
                'unique'    => false,
                'index'     => false,
                'order'     => 3,
            ],
            [
                'module_id' => $peopleId,
                'main'      => false,
                'is_custom' => false,
                'name'      => 'birth_date',
                'label'     => 'Data Nascimento',
                'type'      => 'date',
                'nullable'  => true,
                'required'  => false,
                'unique'    => false,
                'index'     => false,
                'order'     => 4,
            ],
            [
                'module_id' => $peopleId,
                'main'      => false,
                'is_custom' => false,
                'name'      => 'status',
                'label'     => 'Status',
                'type'      => 'boolean',
                'nullable'  => false,
                'required'  => true,
                'unique'    => false,
                'index'     => false,
                'default'   => 'true',
                'order'     => 5,
            ],
        ];

        foreach ($fields as $field) {
            DB::connection('landlord')->table('module_fields')->insert(array_merge([
                'icon'       => null,
                'length'     => null,
                'precision'  => null,
                'default'    => null,
                'fk_table'   => null,
                'fk_column'  => null,
                'fk_label'   => null,
                'auto_from'  => null,
                'auto_type'  => null,
                'min'        => null,
                'max'        => null,
                'status'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], $field));
        }
    }
}
