<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use App\Models\IncomingShipment;
use App\Models\Supplier;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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

    // New shipment form fields
    public ?int $newQuantity = null;
    public $newExpectedDate = null;
    public ?string $newTrackingUrl = null;
    public ?string $newNotes = null;

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

            Section::make('Incoming Shipments')
                ->description('Expected deliveries for this product.')
                ->schema([
                    Placeholder::make('shipments_list')
                        ->label('')
                        ->content(fn () => $this->getShipmentsHtml()),
                ]),

            Section::make('Create New Shipment')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('newQuantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1),
                        DatePicker::make('newExpectedDate')
                            ->label('Expected Date'),
                    ]),
                    TextInput::make('newTrackingUrl')
                        ->label('Tracking URL')
                        ->url(),
                    Textarea::make('newNotes')
                        ->label('Notes')
                        ->rows(2),
                    Actions::make([
                        Action::make('createShipment')
                            ->label('Create Shipment')
                            ->icon('heroicon-o-plus')
                            ->action(fn () => $this->createShipment()),
                    ]),
                ]),
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

    protected function getShipmentsHtml(): HtmlString
    {
        $product = $this->getRecord();
        $shipments = $product->incomingShipments()
            ->with('supplier')
            ->orderBy('expected_date', 'asc')
            ->get();

        if ($shipments->isEmpty()) {
            return new HtmlString('<div class="text-gray-500 text-sm py-4">No incoming shipments for this product.</div>');
        }

        $html = '<div class="overflow-x-auto">';
        $html .= '<table class="w-full text-sm">';
        $html .= '<thead class="bg-gray-50 dark:bg-gray-800">';
        $html .= '<tr>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Quantity</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Expected</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Supplier</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Status</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Tracking</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

        foreach ($shipments as $shipment) {
            $statusColor = match ($shipment->status) {
                IncomingShipment::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
                IncomingShipment::STATUS_IN_TRANSIT => 'bg-blue-100 text-blue-800',
                IncomingShipment::STATUS_RECEIVED => 'bg-green-100 text-green-800',
                IncomingShipment::STATUS_CANCELLED => 'bg-red-100 text-red-800',
                default => 'bg-gray-100 text-gray-800',
            };

            $expectedDate = $shipment->expected_date?->format('M j, Y') ?? '-';
            $isOverdue = $shipment->is_overdue;
            $dateClass = $isOverdue ? 'text-red-600 font-medium' : '';

            $supplierName = $shipment->supplier?->company_name ?? '-';
            $statusLabel = IncomingShipment::getStatusOptions()[$shipment->status] ?? $shipment->status;

            $tracking = '-';
            if ($shipment->tracking_url) {
                $tracking = '<a href="' . e($shipment->tracking_url) . '" target="_blank" class="text-primary-600 hover:underline">Track</a>';
            }

            $html .= '<tr>';
            $html .= '<td class="px-3 py-2">' . $shipment->quantity . '</td>';
            $html .= '<td class="px-3 py-2 ' . $dateClass . '">' . $expectedDate . ($isOverdue ? ' (Overdue)' : '') . '</td>';
            $html .= '<td class="px-3 py-2">' . e($supplierName) . '</td>';
            $html .= '<td class="px-3 py-2"><span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ' . $statusColor . '">' . $statusLabel . '</span></td>';
            $html .= '<td class="px-3 py-2">' . $tracking . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        return new HtmlString($html);
    }

    public function createShipment(): void
    {
        if (!$this->newQuantity || $this->newQuantity < 1) {
            Notification::make()
                ->title('Quantity is required')
                ->danger()
                ->send();
            return;
        }

        $product = $this->getRecord();

        IncomingShipment::create([
            'product_id' => $product->id,
            'supplier_id' => $this->supplier_id ?? $product->supplier_id,
            'quantity' => $this->newQuantity,
            'expected_date' => $this->newExpectedDate,
            'tracking_url' => $this->newTrackingUrl,
            'notes' => $this->newNotes,
            'status' => IncomingShipment::STATUS_PENDING,
        ]);

        // Reset form fields
        $this->newQuantity = null;
        $this->newExpectedDate = null;
        $this->newTrackingUrl = null;
        $this->newNotes = null;

        Notification::make()
            ->title('Shipment created')
            ->success()
            ->send();
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
