<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->boolean('main')->default(false);
            $table->boolean('is_custom')->default(false);
            $table->string('icon', 100)->nullable();
            $table->string('name');
            $table->string('label');
            $table->string('type', 50);
            $table->integer('length')->nullable();
            $table->integer('precision')->nullable();
            $table->string('default')->nullable();
            $table->boolean('nullable')->default(false);
            $table->boolean('required')->default(false);
            $table->boolean('unique')->default(false);
            $table->boolean('index')->default(false);
            $table->string('fk_table')->nullable();
            $table->string('fk_column')->nullable();
            $table->string('fk_label')->nullable();
            $table->string('auto_from')->nullable();
            $table->string('auto_type', 50)->nullable();
            $table->string('min', 50)->nullable();
            $table->string('max', 50)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->string('origin')->default('system');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_fields');
    }
};
