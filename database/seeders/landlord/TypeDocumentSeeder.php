<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TypeDocument;
use Illuminate\Database\Seeder;

class TypeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'CPF', 'order' => 1, 'status' => true],
            ['name' => 'CNPJ', 'order' => 2, 'status' => true],
            ['name' => 'RG', 'order' => 3, 'status' => true],
            ['name' => 'IE', 'order' => 4, 'status' => true],
            ['name' => 'IM', 'order' => 5, 'status' => true],
        ];

        foreach ($types as $type) {
            TypeDocument::create($type);
        }
    }
}
