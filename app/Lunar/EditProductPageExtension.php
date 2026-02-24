<?php

namespace App\Lunar;

use App\Models\ProductBadge;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * Mutate form data before filling the form.
     */
    public function mutateFormDataBeforeFill(Model $record, array $data): array
    {
        // Load existing badge keys for the product
        $data['badge_keys'] = $record->productBadges->pluck('badge_key')->toArray();

        return $data;
    }

    public function beforeSave(array $data): array
    {
        return $data;
    }

    public function afterUpdate(Model $record, array $data): Model
    {
        // Save badge keys
        if (isset($data['badge_keys'])) {
            // Delete existing badges
            $record->productBadges()->delete();

            // Create new badge records
            foreach ($data['badge_keys'] as $badgeKey) {
                ProductBadge::create([
                    'product_id' => $record->id,
                    'badge_key' => $badgeKey,
                ]);
            }
        }

        return $record;
    }
}
