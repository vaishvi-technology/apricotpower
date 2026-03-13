<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Lunar\Models\Contracts\ProductVariant as ProductVariantContract;

class ManageProductShipping extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    public bool $free_shipping = false;
    public $weight_lbs = 0;
    public $weight_oz = 0;

    public function getTitle(): string|Htmlable
    {
        return __('lunarpanel::product.pages.shipping.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('lunarpanel::product.pages.shipping.label');
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return $parameters['record']->variants()->withTrashed()->count() == 1;
    }

    public function getBreadcrumb(): string
    {
        return __('lunarpanel::product.pages.shipping.label');
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::product-shipping');
    }

    protected function getDefaultHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $variant = $this->getVariant();

        $this->free_shipping = (bool) $variant->free_shipping;
        $this->weight_lbs = $variant->weight_lbs ?? 0;
        $this->weight_oz = $variant->weight_oz ?? 0;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $variant = $this->getVariant();

        $variant->update([
            'free_shipping' => $this->free_shipping,
            'weight_lbs' => $this->weight_lbs,
            'weight_oz' => $this->weight_oz,
        ]);

        return $record;
    }

    protected function getVariant(): ProductVariantContract
    {
        return $this->getRecord()->variants()->withTrashed()->first();
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Toggle::make('free_shipping')
                    ->label('Free Shipping')
                    ->columnSpan(2),

                Grid::make(2)->schema([
                    TextInput::make('weight_lbs')
                        ->label('Weight (lbs)')
                        ->numeric()
                        ->default(0)
                        ->suffix('lbs'),

                    TextInput::make('weight_oz')
                        ->label('Weight (oz)')
                        ->numeric()
                        ->default(0)
                        ->suffix('oz'),
                ]),
            ])->columns(2),
        ])->statePath('');
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
