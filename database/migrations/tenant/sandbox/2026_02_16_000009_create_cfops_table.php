<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('cfops', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4); // Código CFOP: 4 dígitos (ex: 5102)
            $table->string('description'); // Descrição (ex: Venda de mercadoria adquirida)
            $table->string('type'); // 'entry' ou 'exit' - controle interno
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('cfops');
    }
};
