<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the admin/staff columns that were mistakenly added to `users`.
 * These columns now live in `lunar_staff` (managed via Filament admin panel).
 * The `users` table is for storefront customer accounts only.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_admin',
                'is_locked',
                'is_hidden',
                'last_login_at',
                'perm_edit_staff',
                'perm_edit_order_fulfillment',
                'perm_edit_order_accounts',
                'perm_edit_inventory',
                'perm_edit_income_expenses',
                'perm_view_order_totals',
                'perm_edit_marketing',
                'perm_edit_email_list',
                'perm_edit_other_admin',
                'perm_edit_rep_settings',
                'perm_edit_account_locked',
                'perm_edit_credits',
                'is_tester',
                'track_activity',
                'include_for_upsell',
                'percent_ws',
                'percent_cs',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('company_name');
            $table->boolean('is_locked')->default(false)->after('is_admin');
            $table->boolean('is_hidden')->default(false)->after('is_locked');
            $table->timestamp('last_login_at')->nullable()->after('is_hidden');
            $table->boolean('perm_edit_staff')->default(false)->after('last_login_at');
            $table->boolean('perm_edit_order_fulfillment')->default(false)->after('perm_edit_staff');
            $table->boolean('perm_edit_order_accounts')->default(false)->after('perm_edit_order_fulfillment');
            $table->boolean('perm_edit_inventory')->default(false)->after('perm_edit_order_accounts');
            $table->boolean('perm_edit_income_expenses')->default(false)->after('perm_edit_inventory');
            $table->boolean('perm_view_order_totals')->default(false)->after('perm_edit_income_expenses');
            $table->boolean('perm_edit_marketing')->default(false)->after('perm_view_order_totals');
            $table->boolean('perm_edit_email_list')->default(false)->after('perm_edit_marketing');
            $table->boolean('perm_edit_other_admin')->default(false)->after('perm_edit_email_list');
            $table->boolean('perm_edit_rep_settings')->default(false)->after('perm_edit_other_admin');
            $table->boolean('perm_edit_account_locked')->default(false)->after('perm_edit_rep_settings');
            $table->boolean('perm_edit_credits')->default(false)->after('perm_edit_account_locked');
            $table->boolean('is_tester')->default(false)->after('perm_edit_credits');
            $table->boolean('track_activity')->default(false)->after('is_tester');
            $table->boolean('include_for_upsell')->default(false)->after('track_activity');
            $table->unsignedTinyInteger('percent_ws')->default(0)->after('include_for_upsell');
            $table->unsignedTinyInteger('percent_cs')->default(0)->after('percent_ws');
        });
    }
};
