<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('tenant')->create('module_submodules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->foreignId('submodule_id')->constrained('modules')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Evitar duplicação
            $table->unique(['module_id', 'submodule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('module_submodules');
    }
};
