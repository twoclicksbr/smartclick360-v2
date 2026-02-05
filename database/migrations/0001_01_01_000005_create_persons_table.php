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
        Schema::connection('tenant')->create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table for person types (many-to-many)
        Schema::connection('tenant')->create('person_person_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('persons')->onDelete('cascade');
            $table->foreignId('person_type_id')->constrained('person_types')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['person_id', 'person_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('person_person_type');
        Schema::connection('tenant')->dropIfExists('persons');
    }
};
