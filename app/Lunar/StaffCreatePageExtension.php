<?php

namespace App\Lunar;

use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\Extending\CreatePageExtension;

class StaffCreatePageExtension extends CreatePageExtension
{
    private array $customFields = [
        'is_locked', 'is_hidden', 'is_tester', 'track_activity', 'include_for_upsell',
        'percent_ws', 'percent_cs',
        'perm_edit_staff', 'perm_edit_order_fulfillment', 'perm_edit_order_accounts',
        'perm_edit_inventory', 'perm_edit_income_expenses', 'perm_view_order_totals',
        'perm_edit_marketing', 'perm_edit_email_list', 'perm_edit_other_admin',
        'perm_edit_rep_settings', 'perm_edit_account_locked', 'perm_edit_credits',
    ];

    public function afterCreation(Model $record, array $data): Model
    {
        $custom = array_intersect_key($data, array_flip($this->customFields));

        if (! empty($custom)) {
            $record->forceFill($custom)->save();
        }

        return $record;
    }
}
