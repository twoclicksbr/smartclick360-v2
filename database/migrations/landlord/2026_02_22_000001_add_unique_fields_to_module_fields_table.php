<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_fields', function (Blueprint $table) {
            $table->string('unique_table')->nullable()->after('index');
            $table->string('unique_column')->nullable()->after('unique_table');
        });
    }

    public function down(): void
    {
        Schema::table('module_fields', function (Blueprint $table) {
            $table->dropColumn(['unique_table', 'unique_column']);
        });
    }
};
