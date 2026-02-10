<?php

namespace Database\Seeders;

use App\Models\Landlord\TypeContact;
use Illuminate\Database\Seeder;

class TypeContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Email', 'order' => 0, 'status' => true],
            ['name' => 'Telefone', 'order' => 1, 'status' => true],
            ['name' => 'WhatsApp', 'order' => 2, 'status' => true],
            ['name' => 'Celular', 'order' => 3, 'status' => true],
        ];

        foreach ($types as $type) {
            TypeContact::create($type);
        }
    }
}
