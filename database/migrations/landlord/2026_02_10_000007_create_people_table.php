<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('first_name');
            $table->string('surname');
            $table->date('birth_date')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('people');
    }
};
