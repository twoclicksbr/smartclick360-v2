<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type', 20)->default('module');
            $table->string('scope', 20);
            $table->string('icon', 100)->nullable();
            $table->string('model')->nullable();
            $table->string('service')->nullable();
            $table->string('controller')->nullable();
            $table->boolean('show_drag')->default(true);
            $table->boolean('show_checkbox')->default(true);
            $table->boolean('show_actions')->default(true);
            $table->string('default_sort_field')->default('id');
            $table->string('default_sort_direction', 4)->default('asc');
            $table->integer('per_page')->default(25);
            $table->string('view_index')->nullable();
            $table->string('view_show')->nullable();
            $table->string('view_modal')->nullable();
            $table->string('after_store')->default('index');
            $table->string('after_update')->default('index');
            $table->string('after_restore')->default('edit');
            $table->boolean('default_checked')->default(false);
            $table->string('origin')->default('system');
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
