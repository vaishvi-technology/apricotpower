<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend Lunar's existing `customers` table with legacy fields.
     *
     * Lunar's base customers table already has:
     *   id, title, first_name, last_name, company_name, email, password,
     *   email_verified_at, remember_token, tax_identifier, account_ref,
     *   attribute_data, meta, created_at, updated_at
     *
     * We add all missing fields from the legacy `accounts` table.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Account status
            $table->boolean('account_locked')->default(false)->after('notes');
            $table->boolean('subscribe_to_list')->default(false)->after('account_locked');

            // Referral
            $table->string('referred_by', 75)->nullable()->after('subscribe_to_list');

            // For retailer / wholesale
            $table->boolean('is_online_wholesaler')->default(false)->after('referred_by');
            $table->date('store_date')->nullable()->after('is_online_wholesaler');
            $table->integer('store_count')->default(1)->after('store_date');

            // Sales rep tracking
            $table->foreignId('sales_rep_id')->nullable()->constrained('staff')->nullOnDelete()->after('store_count');

            // Billing
            $table->string('accounts_payable_email')->nullable()->after('sales_rep_id');

            // Notes
            $table->text('admin_notes')->nullable()->after('notes');

            // Tracking
            $table->timestamp('last_order_at')->nullable()->after('last_login_at');
            $table->timestamp('agreed_terms_at')->nullable()->after('last_order_at');

            // Indexes
            $table->index('account_locked');
            $table->index('is_online_wholesaler');
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop indexes added by this migration
            $table->dropIndex(['account_locked']);
            $table->dropIndex(['is_online_wholesaler']);
            $table->dropIndex(['last_name', 'first_name']);

            // Only drop columns added by THIS migration
            // (not those from 2026_02_18_100020_add_custom_fields_to_lunar_customers_table.php)
            $table->dropColumn([
                'account_locked', 'subscribe_to_list',
                'referred_by',
                'is_online_wholesaler', 'store_date', 'store_count',
                'sales_rep_id',
                'accounts_payable_email',
                'admin_notes',
                'last_order_at', 'agreed_terms_at',
            ]);
        });
    }
};
