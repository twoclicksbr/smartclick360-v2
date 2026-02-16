<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->create('tax_situations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3); // CST: 2 dígitos (ex: 00), CSOSN: 3 dígitos (ex: 102)
            $table->string('description'); // Descrição oficial
            $table->string('regime'); // 'normal' (CST) ou 'simples' (CSOSN) - controle interno
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('tax_situations');
    }
};
