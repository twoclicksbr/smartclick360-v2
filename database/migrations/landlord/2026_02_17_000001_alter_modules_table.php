<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // Remover parent_id
            if (Schema::hasColumn('modules', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }

            // Novos campos - adicionar após 'type'
            $table->string('scope', 20)->default('both')->after('type');
            $table->string('icon', 100)->nullable()->after('scope');
            $table->string('model')->default('Genérica')->after('icon');
            $table->string('request')->nullable()->after('model');
            $table->string('service')->nullable()->after('request');
            $table->string('job')->nullable()->after('service');
            $table->string('controller_api')->nullable()->after('job');
            $table->string('controller_web')->nullable()->after('controller_api');
            $table->string('description_index')->nullable()->after('controller_web');
            $table->string('description_show')->nullable()->after('description_index');
            $table->string('description_create')->nullable()->after('description_show');
            $table->string('description_edit')->nullable()->after('description_create');
            $table->boolean('show_drag')->default(true)->after('description_edit');
            $table->boolean('show_checkbox')->default(true)->after('show_drag');
            $table->boolean('show_actions')->default(true)->after('show_checkbox');
            $table->string('default_sort_field')->default('id')->after('show_actions');
            $table->string('default_sort_direction', 4)->default('asc')->after('default_sort_field');
            $table->integer('per_page')->default(25)->after('default_sort_direction');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn([
                'scope', 'icon', 'model', 'request', 'service', 'job',
                'controller_api', 'controller_web',
                'description_index', 'description_show', 'description_create', 'description_edit',
                'show_drag', 'show_checkbox', 'show_actions',
                'default_sort_field', 'default_sort_direction', 'per_page',
            ]);

            $table->foreignId('parent_id')->nullable()->constrained('modules')->nullOnDelete()->after('type');
        });
    }
};
