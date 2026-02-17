<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // controle interno: sale, purchase, return_sale, return_purchase, adjustment_in, adjustment_out, transfer, bonus, quote, consignment
            $table->string('stock_movement'); // 'in', 'out', 'none'
            $table->string('financial_impact'); // 'receivable', 'payable', 'none'
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
