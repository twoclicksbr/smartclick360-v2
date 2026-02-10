<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TypeContact;
use Illuminate\Database\Seeder;

class TypeContactSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Email', 'order' => 1, 'status' => true],
            ['name' => 'WhatsApp', 'order' => 2, 'status' => true],
            ['name' => 'Telefone', 'order' => 3, 'status' => true],
            ['name' => 'Celular', 'order' => 4, 'status' => true],
        ];

        foreach ($types as $type) {
            TypeContact::create($type);
        }
    }
}
