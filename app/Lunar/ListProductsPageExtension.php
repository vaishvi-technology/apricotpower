<?php

namespace App\Lunar;

use Filament\Actions;
use Filament\Forms\Components\Grid;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Extending\ListPageExtension;
use Lunar\Facades\DB;
use Lunar\Models\Attribute;
use Lunar\Models\Currency;
use Lunar\Models\ProductType;
use Lunar\Models\TaxClass;

class ListProductsPageExtension extends ListPageExtension
{
    /**
     * Override header actions to customize the create form without product_type.
     */
    public function headerActions(array $actions): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->form($this->getCreateFormInputs())
                ->using(fn (array $data, string $model) => $this->createRecord($data, $model))
                ->successRedirectUrl(fn (Model $record): string => ProductResource::getUrl('edit', [
                    'record' => $record,
                ])),
        ];
    }

    /**
     * Get the create form inputs without product_type_id.
     */
    protected function getCreateFormInputs(): array
    {
        return [
            Grid::make(2)->schema([
                ProductResource::getBaseNameFormComponent(),
                ProductResource::getSkuFormComponent(),
            ]),
            Grid::make(2)->schema([
                ProductResource::getBasePriceFormComponent(),
            ]),
        ];
    }

    /**
     * Create a product record with default product type.
     */
    protected function createRecord(array $data, string $model): Model
    {
        $currency = Currency::getDefault() ?? Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'exchange_rate' => 1,
            'decimal_places' => 2,
            'default' => true,
            'enabled' => true,
        ]);
        $productType = ProductType::first() ?? ProductType::create(['name' => 'Default']);
        $taxClass = TaxClass::getDefault() ?? TaxClass::create(['name' => 'Default', 'default' => true]);

        $nameAttribute = Attribute::whereAttributeType($model::morphName())
            ->whereHandle('name')
            ->first()
            ->type;

        DB::beginTransaction();

        $product = $model::create([
            'status' => 'draft',
            'product_type_id' => $productType->id,
            'attribute_data' => [
                'name' => new $nameAttribute($data['name']),
            ],
        ]);

        $variant = $product->variants()->create([
            'tax_class_id' => $taxClass->id,
            'sku' => $data['sku'],
        ]);

        $variant->prices()->create([
            'min_quantity' => 1,
            'currency_id' => $currency->id,
            'price' => (int) bcmul($data['base_price'], $currency->factor),
        ]);

        DB::commit();

        return $product;
    }
}
