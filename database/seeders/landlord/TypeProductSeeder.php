<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TypeProduct;
use Illuminate\Database\Seeder;

class TypeProductSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Produto Acabado',       'type' => 'product', 'order' => 1],
            ['name' => 'Matéria-Prima',         'type' => 'product', 'order' => 2],
            ['name' => 'Embalagem',             'type' => 'product', 'order' => 3],
            ['name' => 'Uso e Consumo',         'type' => 'product', 'order' => 4],
            ['name' => 'Kit/Combo',             'type' => 'product', 'order' => 5],
            ['name' => 'Serviço de Prestação',  'type' => 'service', 'order' => 6],
            ['name' => 'Serviço de Mão de Obra','type' => 'service', 'order' => 7],
        ];

        foreach ($types as $type) {
            TypeProduct::create($type);
        }
    }
}
