<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend Lunar's existing `addresses` table with legacy fields.
     *
     * Lunar's base addresses table has:
     *   id, customer_id, country_id, title, first_name, last_name, company_name,
     *   line_one, line_two, line_three, city, state, postcode,
     *   delivery_instructions, contact_email, contact_phone,
     *   meta, shipping_default, billing_default, timestamps
     *
     * We add:
     *   label    - address label (Home, Office, Warehouse)
     *   type     - explicit shipping/billing type for legacy compatibility
     *   last_used_at - tracking when address was last used
     *
     * Legacy `accounts` inline addresses are normalized into this table:
     *   ShippingAddress1 -> line_one (type='shipping', shipping_default=true)
     *   BillingAddress1  -> line_one (type='billing', billing_default=true)
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (! Schema::hasColumn('addresses', 'label')) {
                $table->string('label')->nullable()->after('billing_default');
            }
            if (! Schema::hasColumn('addresses', 'type')) {
                $table->string('type')->default('shipping')->after('label');
            }
            if (! Schema::hasColumn('addresses', 'last_used_at')) {
                $table->timestamp('last_used_at')->nullable()->after('type');
            }

            if (! Schema::hasIndex('addresses', 'addresses_customer_id_type_index')) {
                $table->index(['customer_id', 'type']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'type']);
            $table->dropColumn(['label', 'type', 'last_used_at']);
        });
    }
};
