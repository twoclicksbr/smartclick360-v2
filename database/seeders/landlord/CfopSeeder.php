<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Cfop;
use Illuminate\Database\Seeder;

class CfopSeeder extends Seeder
{
    public function run(): void
    {
        $cfops = [
            ['code' => '1102', 'description' => 'Compra para comercialização', 'type' => 'entry', 'order' => 1, 'status' => true],
            ['code' => '1202', 'description' => 'Devolução de venda de mercadoria', 'type' => 'entry', 'order' => 2, 'status' => true],
            ['code' => '1352', 'description' => 'Aquisição de serviço de transporte', 'type' => 'entry', 'order' => 3, 'status' => true],
            ['code' => '1556', 'description' => 'Compra de material para uso ou consumo', 'type' => 'entry', 'order' => 4, 'status' => true],
            ['code' => '1910', 'description' => 'Entrada de bonificação, doação ou brinde', 'type' => 'entry', 'order' => 5, 'status' => true],
            ['code' => '2102', 'description' => 'Compra para comercialização (interestadual)', 'type' => 'entry', 'order' => 6, 'status' => true],
            ['code' => '2202', 'description' => 'Devolução de venda (interestadual)', 'type' => 'entry', 'order' => 7, 'status' => true],
            ['code' => '5102', 'description' => 'Venda de mercadoria adquirida', 'type' => 'exit', 'order' => 8, 'status' => true],
            ['code' => '5202', 'description' => 'Devolução de compra para comercialização', 'type' => 'exit', 'order' => 9, 'status' => true],
            ['code' => '5405', 'description' => 'Venda de mercadoria com substituição tributária', 'type' => 'exit', 'order' => 10, 'status' => true],
            ['code' => '5910', 'description' => 'Remessa em bonificação, doação ou brinde', 'type' => 'exit', 'order' => 11, 'status' => true],
            ['code' => '5949', 'description' => 'Outra saída de mercadoria não especificada', 'type' => 'exit', 'order' => 12, 'status' => true],
            ['code' => '6102', 'description' => 'Venda de mercadoria adquirida (interestadual)', 'type' => 'exit', 'order' => 13, 'status' => true],
            ['code' => '6202', 'description' => 'Devolução de compra (interestadual)', 'type' => 'exit', 'order' => 14, 'status' => true],
            ['code' => '6949', 'description' => 'Outra saída não especificada (interestadual)', 'type' => 'exit', 'order' => 15, 'status' => true],
        ];

        foreach ($cfops as $cfop) {
            Cfop::create($cfop);
        }
    }
}
