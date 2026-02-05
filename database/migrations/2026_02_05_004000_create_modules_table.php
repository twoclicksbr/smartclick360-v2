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
        Schema::connection('tenant')->create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('Identificador fixo do módulo');
            $table->string('name')->comment('Nome customizável pelo cliente');
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('active')->default(true);
            $table->enum('type', ['module', 'submodule'])->default('module');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('modules');
    }
};
