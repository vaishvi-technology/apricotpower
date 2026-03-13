<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use App\Models\Supplier;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseEditRecord;

class ManageProductSupplier extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    public ?int $supplier_id = null;
    public ?string $inventory_notes = null;

    public function getTitle(): string|Htmlable
    {
        return 'Supplier';
    }

    public static function getNavigationLabel(): string
    {
        return 'Supplier';
    }

    public function getBreadcrumb(): string
    {
        return 'Supplier';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office';
    }

    protected function getDefaultHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $product = $this->getRecord();
        $this->supplier_id = $product->supplier_id;
        $this->inventory_notes = $product->inventory_notes;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'supplier_id' => $this->supplier_id,
            'inventory_notes' => $this->inventory_notes,
        ]);

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
            Section::make('Primary Supplier')
                ->description('Assign a primary supplier for this product.')
                ->schema([
                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(fn () => Supplier::active()->pluck('company_name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live(),

                    Placeholder::make('supplier_details')
                        ->label('Supplier Contact')
                        ->content(fn () => $this->getSupplierDetailsHtml())
                        ->visible(fn () => (bool) $this->supplier_id),

                    Textarea::make('inventory_notes')
                        ->label('Inventory Notes')
                        ->rows(3)
                        ->helperText('Internal notes about inventory management for this product.')
                        ->columnSpanFull(),
                ])->columns(2),
        ])->statePath('');
    }

    protected function getSupplierDetailsHtml(): HtmlString
    {
        if (!$this->supplier_id) {
            return new HtmlString('<span class="text-gray-500">No supplier selected</span>');
        }

        $supplier = Supplier::find($this->supplier_id);
        if (!$supplier) {
            return new HtmlString('<span class="text-gray-500">Supplier not found</span>');
        }

        $html = '<div class="text-sm space-y-1">';

        if ($supplier->contact_name) {
            $html .= '<div><strong>Contact:</strong> ' . e($supplier->contact_name) . '</div>';
        }
        if ($supplier->phone) {
            $html .= '<div><strong>Phone:</strong> ' . e($supplier->phone) . '</div>';
        }
        if ($supplier->email) {
            $html .= '<div><strong>Email:</strong> <a href="mailto:' . e($supplier->email) . '" class="text-primary-600 hover:underline">' . e($supplier->email) . '</a></div>';
        }
        if ($supplier->supplier_terms) {
            $html .= '<div><strong>Terms:</strong> ' . e($supplier->supplier_terms) . '</div>';
        }
        if ($supplier->lead_time) {
            $html .= '<div><strong>Lead Time:</strong> ' . $supplier->lead_time . ' days</div>';
        }

        $html .= '</div>';

        return new HtmlString($html);
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
