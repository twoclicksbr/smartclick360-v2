<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $transactions = [
            ['name' => 'Venda', 'type' => 'sale', 'stock_movement' => 'out', 'financial_impact' => 'receivable', 'order' => 1, 'status' => true],
            ['name' => 'Compra', 'type' => 'purchase', 'stock_movement' => 'in', 'financial_impact' => 'payable', 'order' => 2, 'status' => true],
            ['name' => 'Devolução de Venda', 'type' => 'return_sale', 'stock_movement' => 'in', 'financial_impact' => 'none', 'order' => 3, 'status' => true],
            ['name' => 'Devolução de Compra', 'type' => 'return_purchase', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 4, 'status' => true],
            ['name' => 'Ajuste de Entrada', 'type' => 'adjustment_in', 'stock_movement' => 'in', 'financial_impact' => 'none', 'order' => 5, 'status' => true],
            ['name' => 'Ajuste de Saída', 'type' => 'adjustment_out', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 6, 'status' => true],
            ['name' => 'Transferência', 'type' => 'transfer', 'stock_movement' => 'none', 'financial_impact' => 'none', 'order' => 7, 'status' => true],
            ['name' => 'Bonificação', 'type' => 'bonus', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 8, 'status' => true],
            ['name' => 'Orçamento', 'type' => 'quote', 'stock_movement' => 'none', 'financial_impact' => 'none', 'order' => 9, 'status' => true],
            ['name' => 'Consignação', 'type' => 'consignment', 'stock_movement' => 'out', 'financial_impact' => 'none', 'order' => 10, 'status' => true],
        ];

        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }
    }
}
