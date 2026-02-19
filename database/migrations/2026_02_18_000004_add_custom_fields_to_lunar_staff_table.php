<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds legacy admin permission columns to the `lunar_staff` table.
 *
 * Legacy `users` table column mapping:
 *   users.UserLocked              → lunar_staff.is_locked
 *   users.UserHidden              → lunar_staff.is_hidden
 *   users.Tester                  → lunar_staff.is_tester
 *   users.UserTrackActivity       → lunar_staff.track_activity
 *   users.IncludeForUpsell        → lunar_staff.include_for_upsell
 *   users.PercentWS               → lunar_staff.percent_ws
 *   users.PercentCS               → lunar_staff.percent_cs
 *   users.EditStaff               → lunar_staff.perm_edit_staff
 *   users.EditOrderFullfillment   → lunar_staff.perm_edit_order_fulfillment
 *   users.EditOrderAccounts       → lunar_staff.perm_edit_order_accounts
 *   users.EditInventoryItems      → lunar_staff.perm_edit_inventory
 *   users.EditIncomeExpenses      → lunar_staff.perm_edit_income_expenses
 *   users.ViewOrderTotals         → lunar_staff.perm_view_order_totals
 *   users.EditMarketingPromos     → lunar_staff.perm_edit_marketing
 *   users.EditEmailList           → lunar_staff.perm_edit_email_list
 *   users.EditOtherAdmin          → lunar_staff.perm_edit_other_admin
 *   users.EditRepSettings         → lunar_staff.perm_edit_rep_settings
 *   users.EditAccountLocked       → lunar_staff.perm_edit_account_locked
 *   users.EditCredits             → lunar_staff.perm_edit_credits
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lunar_staff', function (Blueprint $table) {
            // Account state flags
            $table->boolean('is_locked')->default(false)->after('admin');
            $table->boolean('is_hidden')->default(false)->after('is_locked');
            $table->boolean('is_tester')->default(false)->after('is_hidden');
            $table->boolean('track_activity')->default(false)->after('is_tester');
            $table->boolean('include_for_upsell')->default(false)->after('track_activity');

            // Commission rates
            $table->unsignedTinyInteger('percent_ws')->default(0)->after('include_for_upsell');
            $table->unsignedTinyInteger('percent_cs')->default(0)->after('percent_ws');

            // Permission flags
            $table->boolean('perm_edit_staff')->default(false)->after('percent_cs');
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
        });
    }

    public function down(): void
    {
        Schema::table('lunar_staff', function (Blueprint $table) {
            $table->dropColumn([
                'is_locked', 'is_hidden', 'is_tester', 'track_activity', 'include_for_upsell',
                'percent_ws', 'percent_cs',
                'perm_edit_staff', 'perm_edit_order_fulfillment', 'perm_edit_order_accounts',
                'perm_edit_inventory', 'perm_edit_income_expenses', 'perm_view_order_totals',
                'perm_edit_marketing', 'perm_edit_email_list', 'perm_edit_other_admin',
                'perm_edit_rep_settings', 'perm_edit_account_locked', 'perm_edit_credits',
            ]);
        });
    }
};
