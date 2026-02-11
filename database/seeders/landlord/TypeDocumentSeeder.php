<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TypeDocument;
use Illuminate\Database\Seeder;

class TypeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'CPF',      'mask' => '000.000.000-00',                                   'order' => 1],
            ['name' => 'CNPJ',     'mask' => '00.000.000/0000-00',                                'order' => 2],
            ['name' => 'CPF/CNPJ', 'mask' => '000.000.000-00|00.000.000/0000-00',                 'order' => 3],
            ['name' => 'RG',       'mask' => null,                                                 'order' => 4],
            ['name' => 'IE',       'mask' => null,                                                 'order' => 5],
            ['name' => 'IM',       'mask' => null,                                                 'order' => 6],
        ];

        foreach ($types as $type) {
            TypeDocument::create($type);
        }
    }
}
