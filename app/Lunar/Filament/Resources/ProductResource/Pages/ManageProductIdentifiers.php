<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseEditRecord;

class ManageProductIdentifiers extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    public $sku = '';
    public $amazon_sku = '';

    public function getTitle(): string|Htmlable
    {
        return __('lunarpanel::product.pages.identifiers.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('lunarpanel::product.pages.identifiers.label');
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return $parameters['record']->variants()->withTrashed()->count() == 1;
    }

    public function getBreadcrumb(): string
    {
        return __('lunarpanel::product.pages.identifiers.label');
    }

    public static function getNavigationIcon(): ?string
    {
        return FilamentIcon::resolve('lunar::product-identifiers');
    }

    protected function getDefaultHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $variant = $this->getRecord()->variants()->withTrashed()->first();

        if ($variant) {
            $this->sku = $variant->sku ?? '';
            $this->amazon_sku = $variant->amazon_sku ?? '';
        }
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $variant = $record->variants()->withTrashed()->first();

        if ($variant) {
            $variant->update([
                'sku' => $this->sku ?: null,
                'amazon_sku' => $this->amazon_sku ?: null,
            ]);
        }

        return $record;
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
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('amazon_sku')
                    ->label('Amazon SKU')
                    ->maxLength(255),
            ])->columns(1),
        ])->statePath('');
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
