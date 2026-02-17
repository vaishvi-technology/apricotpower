<?php

namespace App\Lunar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Lunar\Admin\Support\Extending\EditPageExtension;

class EditProductPageExtension extends EditPageExtension
{
    public function heading($title, Model $record): string
    {
        return 'Editing: ' . $record->translateAttribute('name');
    }

    public function subheading($title, Model $record): ?string
    {
        $status = $record->status;
        $sku = $record->variants?->first()?->sku ?? 'N/A';

        return "Status: {$status} | SKU: {$sku}";
    }

    public function beforeSave(array $data): array
    {
        Log::info('EditProductPageExtension: beforeSave', [
            'keys' => array_keys($data),
        ]);

        return $data;
    }

    public function afterUpdate(Model $record, array $data): Model
    {
        Log::info('EditProductPageExtension: afterUpdate', [
            'product_id' => $record->id,
            'product_name' => $record->translateAttribute('name'),
        ]);

        return $record;
    }
}
