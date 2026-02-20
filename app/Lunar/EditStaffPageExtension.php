<?php

namespace App\Lunar;

use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\Extending\EditPageExtension;

class EditStaffPageExtension extends EditPageExtension
{
    public function afterUpdate(Model $record, array $data): Model
    {
        $record->forceFill([
            'is_active' => $data['is_active'] ?? $record->is_active,
        ])->save();

        return $record;
    }
}
