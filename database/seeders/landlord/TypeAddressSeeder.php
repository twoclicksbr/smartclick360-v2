<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TypeAddress;
use Illuminate\Database\Seeder;

class TypeAddressSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Residencial', 'order' => 1, 'status' => true],
            ['name' => 'Comercial', 'order' => 2, 'status' => true],
            ['name' => 'Entrega', 'order' => 3, 'status' => true],
            ['name' => 'Cobrança', 'order' => 4, 'status' => true],
        ];

        foreach ($types as $type) {
            TypeAddress::create($type);
        }
    }
}
