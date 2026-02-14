<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            $table->index('slug');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('contacts', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'contacts_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('documents', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'documents_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('addresses', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'addresses_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('files', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'files_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('notes', function (Blueprint $table) {
            $table->index(['module_id', 'register_id'], 'notes_module_register_index');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('people', function (Blueprint $table) {
            $table->index('tenant_id');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('users', function (Blueprint $table) {
            $table->index('person_id');
            $table->index('order');
            $table->index('status');
        });

        Schema::connection('landlord')->table('subscriptions', function (Blueprint $table) {
            $table->index('tenant_id');
            $table->index('plan_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('addresses', function (Blueprint $table) {
            $table->dropIndex('addresses_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('files', function (Blueprint $table) {
            $table->dropIndex('files_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('notes', function (Blueprint $table) {
            $table->dropIndex('notes_module_register_index');
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('people', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('users', function (Blueprint $table) {
            $table->dropIndex(['person_id']);
            $table->dropIndex(['order']);
            $table->dropIndex(['status']);
        });

        Schema::connection('landlord')->table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['plan_id']);
            $table->dropIndex(['status']);
        });
    }
};
