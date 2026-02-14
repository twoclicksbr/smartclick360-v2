<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('tenant')->table('contacts', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'contacts_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('tenant')->table('documents', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'documents_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('tenant')->table('addresses', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'addresses_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('tenant')->table('files', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'files_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('tenant')->table('notes', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'notes_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('tenant')->table('people', function (Blueprint $table) {
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('tenant')->table('users', function (Blueprint $table) {
            $table->index('order');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('tenant')->table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('tenant')->table('addresses', function (Blueprint $table) {
            $table->dropIndex('addresses_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('tenant')->table('files', function (Blueprint $table) {
            $table->dropIndex('files_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('tenant')->table('notes', function (Blueprint $table) {
            $table->dropIndex('notes_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('tenant')->table('people', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('tenant')->table('users', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });
    }
};
