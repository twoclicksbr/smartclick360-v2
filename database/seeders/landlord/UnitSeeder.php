<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unidade',    'abbreviation' => 'un',  'decimal_places' => 0, 'order' => 1],
            ['name' => 'Quilograma', 'abbreviation' => 'kg',  'decimal_places' => 3, 'order' => 2],
            ['name' => 'Grama',      'abbreviation' => 'g',   'decimal_places' => 2, 'order' => 3],
            ['name' => 'Litro',      'abbreviation' => 'L',   'decimal_places' => 3, 'order' => 4],
            ['name' => 'Mililitro',  'abbreviation' => 'ml',  'decimal_places' => 0, 'order' => 5],
            ['name' => 'Metro',      'abbreviation' => 'm',   'decimal_places' => 2, 'order' => 6],
            ['name' => 'Caixa',      'abbreviation' => 'cx',  'decimal_places' => 0, 'order' => 7],
            ['name' => 'Pacote',     'abbreviation' => 'pct', 'decimal_places' => 0, 'order' => 8],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
