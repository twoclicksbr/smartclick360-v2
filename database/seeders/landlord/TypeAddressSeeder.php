<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TypeAddress;
use Illuminate\Database\Seeder;

class TypeAddressSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Residencial', 'order' => 1],
            ['name' => 'Comercial',   'order' => 2],
            ['name' => 'Entrega',     'order' => 3],
            ['name' => 'Cobrança',    'order' => 4],
        ];

        foreach ($types as $type) {
            TypeAddress::create($type);
        }
    }
}
