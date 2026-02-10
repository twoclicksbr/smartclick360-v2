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
        Schema::connection('landlord')->create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_document_id')->constrained('type_documents');
            $table->foreignId('module_id')->constrained('modules');
            $table->unsignedBigInteger('register_id');
            $table->string('value');
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('documents');
    }
};
