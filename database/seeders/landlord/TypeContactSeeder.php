<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TypeContact;
use Illuminate\Database\Seeder;

class TypeContactSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Email',     'mask' => null,                                    'order' => 1],
            ['name' => 'WhatsApp',  'mask' => '(00) 00000-0000',                       'order' => 2],
            ['name' => 'Telefone',  'mask' => '(00) 0000-0000|(00) 00000-0000',        'order' => 3],
            ['name' => 'Celular',   'mask' => '(00) 00000-0000',                       'order' => 4],
        ];

        foreach ($types as $type) {
            TypeContact::create($type);
        }
    }
}
