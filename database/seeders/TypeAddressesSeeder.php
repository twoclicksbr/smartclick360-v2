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
            ['name' => 'Residencial', 'slug' => 'home', 'icon' => 'ki-home', 'is_active' => true],
            ['name' => 'Comercial', 'slug' => 'work', 'icon' => 'ki-office-bag', 'is_active' => true],
            ['name' => 'Cobrança', 'slug' => 'billing', 'icon' => 'ki-bill', 'is_active' => true],
            ['name' => 'Entrega', 'slug' => 'delivery', 'icon' => 'ki-delivery', 'is_active' => true],
        ];

        foreach ($types as $type) {
            TypeAddress::create($type);
        }
    }
}
