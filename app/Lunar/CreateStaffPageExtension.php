<?php

namespace App\Lunar;

use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Support\Extending\CreatePageExtension;

class CreateStaffPageExtension extends CreatePageExtension
{
    public function afterCreation(Model $record, array $data): Model
    {
        $record->forceFill([
            'is_active' => $data['is_active'] ?? true,
        ])->save();

        return $record;
    }
}
