<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use App\Models\InventoryLot;
use App\Services\InventoryService;
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
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseEditRecord;

class ManageProductInventoryLots extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    // Inventory summary properties
    public int $totalStock = 0;
    public int $availableStock = 0;
    public int $expiringStock = 0;
    public int $expiredStock = 0;

    // Quick adjustment form
    public ?string $adjustmentType = 'add';
    public ?int $adjustmentQuantity = 1;
    public ?int $adjustmentLotId = null;
    public ?string $adjustmentReason = null;

    // Receive inventory form
    public ?string $newLotNumber = null;
    public ?int $newQuantity = null;
    public ?float $newCostPerUnit = null;
    public ?string $newLocation = null;
    public $newReceivedAt = null;
    public $newExpiresAt = null;
    public ?string $newNotes = null;

    public function getTitle(): string|Htmlable
    {
        $product = $this->getRecord();
        $productName = $product->name ?? $product->translateAttribute('name') ?? 'Product';

        return "Inventory Lots - {$productName}";
    }

    public static function getNavigationLabel(): string
    {
        return 'Inventory Lots';
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return true;
    }

    public function getBreadcrumb(): string
    {
        return 'Inventory Lots';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cube';
    }

    protected function getDefaultHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->calculateInventorySummary();
        $this->newReceivedAt = now()->format('Y-m-d');
    }

    protected function calculateInventorySummary(): void
    {
        $summary = app(InventoryService::class)->getInventorySummary($this->getRecord());

        $this->totalStock = $summary['total_stock'];
        $this->availableStock = $summary['available_stock'];
        $this->expiringStock = $summary['expiring_stock'];
        $this->expiredStock = $summary['expired_stock'];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $record;
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            // Inventory Summary Section
            Section::make('Inventory Summary')
                ->description('Current stock levels for this product.')
                ->schema([
                    Grid::make(4)->schema([
                        Placeholder::make('total_stock_display')
                            ->label('Total Stock')
                            ->content(fn () => $this->totalStock),

                        Placeholder::make('available_stock_display')
                            ->label('Available (Non-Expired)')
                            ->content(fn () => $this->availableStock),

                        Placeholder::make('expiring_stock_display')
                            ->label('Expiring Soon')
                            ->content(fn () => $this->expiringStock)
                            ->helperText('Within 30 days'),

                        Placeholder::make('expired_stock_display')
                            ->label('Expired')
                            ->content(fn () => $this->expiredStock),
                    ]),
                ]),

            // Current Lots Section
            Section::make('Current Inventory Lots')
                ->description('All lots for this product.')
                ->schema([
                    Placeholder::make('lots_list')
                        ->label('')
                        ->content(fn () => $this->getLotsHtml()),
                ]),

            // Quick Adjustment Section
            Section::make('Quick Adjustment')
                ->description('Add or subtract inventory from an existing lot.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(4)->schema([
                        Select::make('adjustmentType')
                            ->label('Action')
                            ->options([
                                'add' => 'Add (+)',
                                'subtract' => 'Subtract (-)',
                            ])
                            ->default('add')
                            ->required(),

                        TextInput::make('adjustmentQuantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Select::make('adjustmentLotId')
                            ->label('From Lot')
                            ->options(fn () => $this->getLotOptions())
                            ->placeholder('Select a lot')
                            ->required()
                            ->helperText('Select the lot to adjust'),

                        TextInput::make('adjustmentReason')
                            ->label('Reason')
                            ->placeholder('Why are you adjusting?')
                            ->required(),
                    ]),
                    Actions::make([
                        Action::make('applyAdjustment')
                            ->label('Apply Adjustment')
                            ->icon('heroicon-o-check')
                            ->color('warning')
                            ->action(fn () => $this->applyAdjustment()),
                    ]),
                ]),

            // Receive New Inventory Section
            Section::make('Receive New Inventory')
                ->description('Create a new lot with fresh inventory.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('newLotNumber')
                            ->label('Lot Number')
                            ->placeholder('Auto-generated if empty')
                            ->helperText('Leave empty to auto-generate'),

                        TextInput::make('newQuantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        TextInput::make('newCostPerUnit')
                            ->label('Cost Per Unit')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01),

                        TextInput::make('newLocation')
                            ->label('Location')
                            ->placeholder('e.g., Warehouse A, Shelf 3'),

                        DatePicker::make('newReceivedAt')
                            ->label('Received Date')
                            ->default(now()),

                        DatePicker::make('newExpiresAt')
                            ->label('Expiration Date'),
                    ]),

                    Textarea::make('newNotes')
                        ->label('Notes')
                        ->rows(2)
                        ->columnSpanFull(),

                    Actions::make([
                        Action::make('receiveInventory')
                            ->label('Receive Inventory')
                            ->icon('heroicon-o-plus')
                            ->color('success')
                            ->action(fn () => $this->receiveInventory()),
                    ]),
                ]),
        ])->statePath('');
    }

    protected function getLotsHtml(): \Illuminate\Support\HtmlString
    {
        $lots = InventoryLot::where('product_id', $this->getRecord()->id)
            ->orderBy('expires_at', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($lots->isEmpty()) {
            return new \Illuminate\Support\HtmlString(
                '<div class="text-gray-500 text-sm italic">No inventory lots found. Use "Receive New Inventory" below to add inventory.</div>'
            );
        }

        $html = '<div class="overflow-x-auto"><table class="w-full text-sm">';
        $html .= '<thead class="bg-gray-50 dark:bg-gray-800"><tr>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Lot #</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Quantity</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Location</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Received</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Expires</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Status</th>';
        $html .= '<th class="px-3 py-2 text-left font-medium">Actions</th>';
        $html .= '</tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

        foreach ($lots as $lot) {
            $qtyColor = $lot->quantity <= 0 ? 'text-red-600' : ($lot->quantity <= 10 ? 'text-yellow-600' : 'text-green-600');
            $statusBadge = $this->getStatusBadge($lot);
            $editUrl = "/admin/inventory-lots/{$lot->id}/edit";

            $lotNumber = $lot->lot_number ?? 'Lot #' . $lot->id;
            $html .= '<tr class="hover:bg-gray-50 dark:hover:bg-gray-800">';
            $html .= "<td class=\"px-3 py-2 font-mono text-sm\">{$lotNumber}</td>";
            $html .= "<td class=\"px-3 py-2 font-bold {$qtyColor}\">{$lot->quantity}</td>";
            $html .= "<td class=\"px-3 py-2\">" . ($lot->location ?: '-') . "</td>";
            $html .= "<td class=\"px-3 py-2\">" . ($lot->received_at ? $lot->received_at->format('m/d/Y') : '-') . "</td>";
            $html .= "<td class=\"px-3 py-2\">" . ($lot->expires_at ? $lot->expires_at->format('m/d/Y') : 'No expiration') . "</td>";
            $html .= "<td class=\"px-3 py-2\">{$statusBadge}</td>";
            $html .= "<td class=\"px-3 py-2\"><a href=\"{$editUrl}\" class=\"text-primary-600 hover:underline text-sm\">Edit</a></td>";
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return new \Illuminate\Support\HtmlString($html);
    }

    protected function getStatusBadge(InventoryLot $lot): string
    {
        if ($lot->is_expired) {
            return '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Expired</span>';
        }
        if ($lot->is_expiring_soon) {
            return '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Expiring Soon</span>';
        }
        if ($lot->quantity <= 0) {
            return '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">Out of Stock</span>';
        }
        return '<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">In Stock</span>';
    }

    protected function getLotOptions(): array
    {
        return InventoryLot::where('product_id', $this->getRecord()->id)
            ->where('quantity', '>', 0)
            ->orderBy('expires_at', 'asc')
            ->get()
            ->mapWithKeys(fn ($lot) => [
                $lot->id => ($lot->lot_number ?? 'Lot #' . $lot->id) . ' (Qty: ' . $lot->quantity . ')'
                    . ($lot->expires_at ? ' - Expires: ' . $lot->expires_at->format('m/d/Y') : '')
            ])
            ->toArray();
    }

    public function applyAdjustment(): void
    {
        if (!$this->adjustmentLotId || !$this->adjustmentQuantity || !$this->adjustmentReason) {
            Notification::make()
                ->title('Please fill in all required fields')
                ->danger()
                ->send();
            return;
        }

        $lot = InventoryLot::find($this->adjustmentLotId);

        if (!$lot) {
            Notification::make()
                ->title('Lot not found')
                ->danger()
                ->send();
            return;
        }

        app(InventoryService::class)->adjustInventory($lot, [
            'type' => $this->adjustmentType,
            'quantity' => $this->adjustmentQuantity,
            'reason' => $this->adjustmentReason,
        ]);

        // Reset form
        $this->adjustmentQuantity = 1;
        $this->adjustmentLotId = null;
        $this->adjustmentReason = null;

        // Refresh summary
        $this->calculateInventorySummary();

        Notification::make()
            ->title('Inventory adjusted successfully')
            ->success()
            ->send();
    }

    public function receiveInventory(): void
    {
        if (!$this->newQuantity) {
            Notification::make()
                ->title('Please enter a quantity')
                ->danger()
                ->send();
            return;
        }

        app(InventoryService::class)->receiveInventory($this->getRecord(), [
            'lot_number' => $this->newLotNumber,
            'quantity' => $this->newQuantity,
            'cost_per_unit' => $this->newCostPerUnit,
            'location' => $this->newLocation,
            'received_at' => $this->newReceivedAt,
            'expires_at' => $this->newExpiresAt,
            'notes' => $this->newNotes,
        ]);

        // Reset form
        $this->newLotNumber = null;
        $this->newQuantity = null;
        $this->newCostPerUnit = null;
        $this->newLocation = null;
        $this->newReceivedAt = now()->format('Y-m-d');
        $this->newExpiresAt = null;
        $this->newNotes = null;

        // Refresh summary
        $this->calculateInventorySummary();

        Notification::make()
            ->title('Inventory received successfully')
            ->success()
            ->send();
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
