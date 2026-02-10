<?php

namespace Database\Seeders;

use App\Models\Landlord\TypeDocument;
use Illuminate\Database\Seeder;

class TypeDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'CPF', 'order' => 0, 'status' => true],
            ['name' => 'CNPJ', 'order' => 1, 'status' => true],
            ['name' => 'RG', 'order' => 2, 'status' => true],
            ['name' => 'CNH', 'order' => 3, 'status' => true],
            ['name' => 'Passaporte', 'order' => 4, 'status' => true],
        ];

        foreach ($types as $type) {
            TypeDocument::create($type);
        }
    }
}
