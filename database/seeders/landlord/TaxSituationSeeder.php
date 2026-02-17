<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\TaxSituation;
use Illuminate\Database\Seeder;

class TaxSituationSeeder extends Seeder
{
    public function run(): void
    {
        $taxSituations = [
            // CST — Regime Normal
            ['code' => '00', 'description' => 'Tributada integralmente', 'regime' => 'normal', 'order' => 1, 'status' => true],
            ['code' => '10', 'description' => 'Tributada e com cobrança do ICMS por substituição tributária', 'regime' => 'normal', 'order' => 2, 'status' => true],
            ['code' => '20', 'description' => 'Com redução da base de cálculo', 'regime' => 'normal', 'order' => 3, 'status' => true],
            ['code' => '30', 'description' => 'Isenta ou não tributada e com cobrança do ICMS por substituição tributária', 'regime' => 'normal', 'order' => 4, 'status' => true],
            ['code' => '40', 'description' => 'Isenta', 'regime' => 'normal', 'order' => 5, 'status' => true],
            ['code' => '41', 'description' => 'Não tributada', 'regime' => 'normal', 'order' => 6, 'status' => true],
            ['code' => '50', 'description' => 'Suspensão', 'regime' => 'normal', 'order' => 7, 'status' => true],
            ['code' => '51', 'description' => 'Diferimento', 'regime' => 'normal', 'order' => 8, 'status' => true],
            ['code' => '60', 'description' => 'ICMS cobrado anteriormente por substituição tributária', 'regime' => 'normal', 'order' => 9, 'status' => true],
            ['code' => '70', 'description' => 'Com redução da base de cálculo e cobrança do ICMS por substituição tributária', 'regime' => 'normal', 'order' => 10, 'status' => true],
            ['code' => '90', 'description' => 'Outras', 'regime' => 'normal', 'order' => 11, 'status' => true],

            // CSOSN — Simples Nacional
            ['code' => '101', 'description' => 'Tributada com permissão de crédito', 'regime' => 'simples', 'order' => 12, 'status' => true],
            ['code' => '102', 'description' => 'Tributada sem permissão de crédito', 'regime' => 'simples', 'order' => 13, 'status' => true],
            ['code' => '103', 'description' => 'Isenção do ICMS para faixa de receita bruta', 'regime' => 'simples', 'order' => 14, 'status' => true],
            ['code' => '201', 'description' => 'Tributada com permissão de crédito e com cobrança do ICMS por ST', 'regime' => 'simples', 'order' => 15, 'status' => true],
            ['code' => '202', 'description' => 'Tributada sem permissão de crédito e com cobrança do ICMS por ST', 'regime' => 'simples', 'order' => 16, 'status' => true],
            ['code' => '203', 'description' => 'Isenção do ICMS para faixa de receita bruta e com cobrança do ICMS por ST', 'regime' => 'simples', 'order' => 17, 'status' => true],
            ['code' => '300', 'description' => 'Imune', 'regime' => 'simples', 'order' => 18, 'status' => true],
            ['code' => '400', 'description' => 'Não tributada', 'regime' => 'simples', 'order' => 19, 'status' => true],
            ['code' => '500', 'description' => 'ICMS cobrado anteriormente por ST ou por antecipação', 'regime' => 'simples', 'order' => 20, 'status' => true],
            ['code' => '900', 'description' => 'Outros', 'regime' => 'simples', 'order' => 21, 'status' => true],
        ];

        foreach ($taxSituations as $taxSituation) {
            TaxSituation::create($taxSituation);
        }
    }
}
