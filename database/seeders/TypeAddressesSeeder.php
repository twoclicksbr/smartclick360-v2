<?php

namespace Database\Seeders;

use App\Models\Landlord\TypeAddress;
use Illuminate\Database\Seeder;

class TypeAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Residencial', 'order' => 0, 'status' => true],
            ['name' => 'Comercial', 'order' => 1, 'status' => true],
            ['name' => 'Cobrança', 'order' => 2, 'status' => true],
            ['name' => 'Entrega', 'order' => 3, 'status' => true],
        ];

        foreach ($types as $type) {
            TypeAddress::create($type);
        }
    }
}
