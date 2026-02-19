<?php

namespace App\Models;

use Lunar\Admin\Models\Staff as LunarStaff;

class Staff extends LunarStaff
{
    protected $fillable = [
        // Lunar base fields
        'first_name',
        'last_name',
        'admin',
        'email',
        'password',

        // Account status (legacy UserLocked, UserHidden, Tester, UserTrackActivity, IncludeForUpsell)
        'is_locked',
        'is_hidden',
        'is_tester',
        'track_activity',
        'include_for_upsell',

        // Commission rates (legacy PercentWS, PercentCS)
        'percent_ws',
        'percent_cs',

        // Permission flags (legacy Edit* columns)
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
    ];

    protected $casts = [
        'admin'                        => 'boolean',
        'email_verified_at'            => 'datetime',
        'password'                     => 'hashed',
        'is_locked'                    => 'boolean',
        'is_hidden'                    => 'boolean',
        'is_tester'                    => 'boolean',
        'track_activity'               => 'boolean',
        'include_for_upsell'           => 'boolean',
        'perm_edit_staff'              => 'boolean',
        'perm_edit_order_fulfillment'  => 'boolean',
        'perm_edit_order_accounts'     => 'boolean',
        'perm_edit_inventory'          => 'boolean',
        'perm_edit_income_expenses'    => 'boolean',
        'perm_view_order_totals'       => 'boolean',
        'perm_edit_marketing'          => 'boolean',
        'perm_edit_email_list'         => 'boolean',
        'perm_edit_other_admin'        => 'boolean',
        'perm_edit_rep_settings'       => 'boolean',
        'perm_edit_account_locked'     => 'boolean',
        'perm_edit_credits'            => 'boolean',
    ];
}
