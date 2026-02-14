<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->create('people', function (Blueprint $table) {
            $table->id();
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
        Schema::connection('tenant')->dropIfExists('people');
    }
};
