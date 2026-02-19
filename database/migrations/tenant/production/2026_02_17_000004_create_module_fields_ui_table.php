<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_fields_ui', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_field_id')->constrained('module_fields')->cascadeOnDelete();
            $table->string('component', 50);
            $table->json('options')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('mask', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->text('tooltip')->nullable();
            $table->string('tooltip_direction', 20)->default('top');
            $table->string('grid_col', 20)->default('col-md-12');
            $table->boolean('visible_index')->default(false);
            $table->boolean('visible_show')->default(false);
            $table->boolean('visible_create')->default(true);
            $table->boolean('visible_edit')->default(true);
            $table->string('width_index', 20)->nullable();
            $table->string('grid_template')->nullable();
            $table->string('grid_link')->nullable();
            $table->json('grid_actions')->nullable();
            $table->boolean('searchable')->default(false);
            $table->boolean('sortable')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->string('origin')->default('system');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_fields_ui');
    }
};
