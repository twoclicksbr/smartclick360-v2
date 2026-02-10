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
            ['name' => 'CPF', 'slug' => 'cpf', 'icon' => 'ki-profile-user', 'is_active' => true],
            ['name' => 'CNPJ', 'slug' => 'cnpj', 'icon' => 'ki-bank', 'is_active' => true],
            ['name' => 'RG', 'slug' => 'rg', 'icon' => 'ki-badge', 'is_active' => true],
            ['name' => 'CNH', 'slug' => 'cnh', 'icon' => 'ki-car', 'is_active' => true],
            ['name' => 'Passaporte', 'slug' => 'passport', 'icon' => 'ki-passport', 'is_active' => true],
        ];

        foreach ($types as $type) {
            TypeDocument::create($type);
        }
    }
}
