<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Origin;
use Illuminate\Database\Seeder;

class OriginSeeder extends Seeder
{
    public function run(): void
    {
        $origins = [
            ['code' => '0', 'description' => 'Nacional, exceto as indicadas nos códigos 3, 4, 5 e 8', 'order' => 1],
            ['code' => '1', 'description' => 'Estrangeira - Importação direta, exceto a indicada no código 6', 'order' => 2],
            ['code' => '2', 'description' => 'Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7', 'order' => 3],
            ['code' => '3', 'description' => 'Nacional, mercadoria ou bem com Conteúdo de Importação superior a 40% e inferior ou igual a 70%', 'order' => 4],
            ['code' => '4', 'description' => 'Nacional, cuja produção tenha sido feita em conformidade com os processos produtivos básicos (PPB)', 'order' => 5],
            ['code' => '5', 'description' => 'Nacional, mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%', 'order' => 6],
            ['code' => '6', 'description' => 'Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX', 'order' => 7],
            ['code' => '7', 'description' => 'Estrangeira - Adquirida no mercado interno, sem similar nacional, constante em lista da CAMEX', 'order' => 8],
            ['code' => '8', 'description' => 'Nacional, mercadoria ou bem com Conteúdo de Importação superior a 70%', 'order' => 9],
        ];

        foreach ($origins as $origin) {
            Origin::create($origin);
        }
    }
}
