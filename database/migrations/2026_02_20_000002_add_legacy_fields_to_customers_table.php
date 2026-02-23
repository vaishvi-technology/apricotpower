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
            // Contact
            $table->string('alt_phone', 50)->nullable()->after('phone');


            // Account status
            $table->boolean('account_locked')->default(false)->after('is_active');
            $table->boolean('subscribe_to_list')->default(false)->after('account_locked');

            // VIP / loyalty
            $table->boolean('is_vip')->default(false)->after('subscribe_to_list');
            $table->date('vip_since')->nullable()->after('is_vip');
            $table->date('vip_expire')->nullable()->after('vip_since');

            // Referral
            $table->string('referred_by', 75)->nullable()->after('vip_expire');

            // For retailer / wholesale
            $table->boolean('is_retailer')->default(false)->after('referred_by');
            $table->boolean('is_online_retailer')->default(false)->after('is_retailer');
            $table->integer('store_count')->default(1)->after('is_online_retailer');

            // Sales rep tracking
            $table->unsignedInteger('sales_rep_id')->default(0)->after('store_count');

            // Notes
            $table->text('admin_notes')->nullable()->after('notes');
            $table->text('extra_emails')->nullable()->after('admin_notes');

            // Tracking
            $table->timestamp('last_order_at')->nullable()->after('last_login_at');
            $table->timestamp('agreed_terms_at')->nullable()->after('last_order_at');

            // Indexes
            $table->index('is_active');
            $table->index('is_retailer');
            $table->index('is_vip');
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
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_retailer']);
            $table->dropIndex(['is_vip']);
            $table->dropIndex(['last_name', 'first_name']);

            // Only drop columns added by THIS migration
            // (not those from 2026_02_18_100020_add_custom_fields_to_lunar_customers_table.php)
            $table->dropColumn([
                'alt_phone',
                'account_locked', 'subscribe_to_list',
                'is_vip', 'vip_since', 'vip_expire',
                'referred_by',
                'is_retailer', 'is_online_retailer', 'store_count',
                'sales_rep_id',
                'admin_notes', 'extra_emails',
                'last_order_at', 'agreed_terms_at',
            ]);
        });
    }
};
