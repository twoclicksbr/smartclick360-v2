<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'discount' ou 'addition' - controle interno
            $table->decimal('percentage', 8, 2)->default(0); // percentual de desconto ou acréscimo
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('price_lists');
    }
};
