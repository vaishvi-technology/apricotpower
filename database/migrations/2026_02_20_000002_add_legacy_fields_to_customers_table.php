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
            $table->string('phone', 50)->nullable()->after('tax_identifier');
            $table->string('alt_phone', 50)->nullable()->after('phone');

            // B2B / Wholesale fields (from legacy accounts)
            $table->boolean('is_tax_exempt')->default(false)->after('alt_phone');
            $table->string('tax_exempt_certificate')->nullable()->after('is_tax_exempt');
            $table->boolean('net_terms_approved')->default(false)->after('tax_exempt_certificate');
            $table->decimal('credit_limit', 10, 2)->nullable()->after('net_terms_approved');
            $table->decimal('current_balance', 10, 2)->default(0)->after('credit_limit');

            // Account status
            $table->boolean('is_active')->default(true)->after('current_balance');
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
            $table->text('notes')->nullable()->after('sales_rep_id');
            $table->text('admin_notes')->nullable()->after('notes');
            $table->text('extra_emails')->nullable()->after('admin_notes');

            // Tracking
            $table->timestamp('last_login_at')->nullable()->after('extra_emails');
            $table->timestamp('last_order_at')->nullable()->after('last_login_at');
            $table->timestamp('agreed_terms_at')->nullable()->after('last_order_at');

            // Soft deletes
            $table->softDeletes();

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
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_retailer']);
            $table->dropIndex(['is_vip']);
            $table->dropIndex(['last_name', 'first_name']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone', 'alt_phone',
                'is_tax_exempt', 'tax_exempt_certificate',
                'net_terms_approved', 'credit_limit', 'current_balance',
                'is_active', 'account_locked', 'subscribe_to_list',
                'is_vip', 'vip_since', 'vip_expire',
                'referred_by',
                'is_retailer', 'is_online_retailer', 'store_count',
                'sales_rep_id',
                'notes', 'admin_notes', 'extra_emails',
                'last_login_at', 'last_order_at', 'agreed_terms_at',
            ]);
        });
    }
};
