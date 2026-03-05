<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use App\Models\CustomerGroup;
use App\Models\CustomerGroupPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Support\Pages\BaseEditRecord;
use Lunar\Models\Currency;

class ManageProductGroupPricing extends BaseEditRecord
{
    protected static string $resource = ProductResource::class;

    // MAP Price from Lunar's prices table
    public ?string $mapPrice = null;
    public ?string $comparePrice = null;

    // Per-group pricing data structure
    // Format: ['group_id' => ['base_price' => x, 'products_minimum' => x, 'tier_method' => 'quantity', 'expires_at' => null, 'tiers' => [...]]]
    public array $groupPricing = [];

    public function getTitle(): string|Htmlable
    {
        return 'Group Pricing';
    }

    public static function getNavigationLabel(): string
    {
        return 'Group Pricing';
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        // Only show for products with a single variant (simple products)
        return $parameters['record']->variants()->withTrashed()->count() == 1;
    }

    public function getBreadcrumb(): string
    {
        return 'Group Pricing';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-currency-dollar';
    }

    protected function getDefaultHeaderActions(): array
    {
        return [];
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->loadPricingData();
    }

    protected function loadPricingData(): void
    {
        $variant = $this->getVariant();

        if (!$variant) {
            return;
        }

        // Load MAP price from Lunar's prices table
        $basePrice = $variant->basePrices()
            ->whereNull('customer_group_id')
            ->where('min_quantity', 1)
            ->first();

        if ($basePrice) {
            $this->mapPrice = number_format($basePrice->price->decimal(rounding: false), 2);
            $this->comparePrice = $basePrice->compare_price
                ? number_format($basePrice->compare_price->decimal(rounding: false), 2)
                : null;
        }

        // Load group pricing from customer_group_prices table
        $customerGroups = CustomerGroup::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($customerGroups as $group) {
            $groupPrices = CustomerGroupPrice::where('product_id', $this->getRecord()->id)
                ->where('customer_group_id', $group->id)
                ->orderBy('is_base_price', 'desc')
                ->orderBy('min_quantity')
                ->orderBy('cutoff_amount')
                ->get();

            $basePrice = $groupPrices->firstWhere('is_base_price', true);
            $tiers = $groupPrices->where('is_base_price', false)->values();

            $this->groupPricing[$group->id] = [
                'group_name' => $group->name,
                'group_handle' => $group->handle,
                'products_minimum' => $group->products_minimum ?? 1,
                'base_price' => $basePrice?->price ? number_format($basePrice->price, 2) : '',
                'base_price_id' => $basePrice?->id,
                'expires_at' => $basePrice?->expires_at?->format('Y-m-d'),
                'tier_method' => ($basePrice?->is_by_quantity ?? true) ? 'quantity' : 'cart_total',
                'tiers' => $tiers->map(fn($t) => [
                    'id' => $t->id,
                    'cutoff' => $t->is_by_quantity ? (string)$t->min_quantity : number_format($t->cutoff_amount, 2),
                    'price' => number_format($t->price, 2),
                ])->toArray(),
            ];
        }
    }

    protected function getVariant()
    {
        return $this->getRecord()->variants()->withTrashed()->first();
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $product = $record;
        $variant = $this->getVariant();

        if (!$variant) {
            return $record;
        }

        foreach ($this->groupPricing as $groupId => $groupData) {
            $isQuantityBased = ($groupData['tier_method'] ?? 'quantity') === 'quantity';

            // Update products_minimum on customer group
            CustomerGroup::find($groupId)?->update([
                'products_minimum' => $groupData['products_minimum'] ?? 1,
            ]);

            // Handle base price
            $basePriceValue = !empty($groupData['base_price']) ? (float)str_replace(',', '', $groupData['base_price']) : null;

            if ($basePriceValue !== null && $basePriceValue > 0) {
                CustomerGroupPrice::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'customer_group_id' => $groupId,
                        'is_base_price' => true,
                    ],
                    [
                        'price' => $basePriceValue,
                        'min_quantity' => 1,
                        'is_by_quantity' => $isQuantityBased,
                        'cutoff_amount' => null,
                        'expires_at' => $groupData['expires_at'] ?? null,
                    ]
                );
            } else {
                // Delete base price if cleared
                CustomerGroupPrice::where([
                    'product_id' => $product->id,
                    'customer_group_id' => $groupId,
                    'is_base_price' => true,
                ])->delete();
            }

            // Handle tiers
            $existingTierIds = [];
            foreach ($groupData['tiers'] ?? [] as $tier) {
                $cutoffValue = !empty($tier['cutoff']) ? (float)str_replace(',', '', $tier['cutoff']) : null;
                $priceValue = !empty($tier['price']) ? (float)str_replace(',', '', $tier['price']) : null;

                if ($cutoffValue !== null && $cutoffValue > 0 && $priceValue !== null && $priceValue > 0) {
                    $tierData = [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'customer_group_id' => $groupId,
                        'is_base_price' => false,
                        'min_quantity' => $isQuantityBased ? (int)$cutoffValue : 1,
                        'cutoff_amount' => !$isQuantityBased ? $cutoffValue : null,
                        'price' => $priceValue,
                        'is_by_quantity' => $isQuantityBased,
                    ];

                    if (!empty($tier['id'])) {
                        $tierRecord = CustomerGroupPrice::find($tier['id']);
                        if ($tierRecord) {
                            $tierRecord->update($tierData);
                            $existingTierIds[] = $tierRecord->id;
                        }
                    } else {
                        $tierRecord = CustomerGroupPrice::create($tierData);
                        $existingTierIds[] = $tierRecord->id;
                    }
                }
            }

            // Delete removed tiers
            CustomerGroupPrice::where('product_id', $product->id)
                ->where('customer_group_id', $groupId)
                ->where('is_base_price', false)
                ->whereNotIn('id', $existingTierIds)
                ->delete();
        }

        Notification::make()
            ->title('Group pricing saved successfully')
            ->success()
            ->send();

        return $record;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    public function getDefaultForm(Form $form): Form
    {
        $schema = [];

        // MAP Price Section (read-only)
        $schema[] = Forms\Components\Section::make('MAP Price (Base Retail)')
            ->description('The base retail price from Lunar\'s pricing system. This is the default price shown to all customers.')
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Placeholder::make('map_price_display')
                        ->label('MAP Price')
                        ->content(fn() => $this->mapPrice ? '$' . $this->mapPrice : 'Not set'),
                    Forms\Components\Placeholder::make('compare_price_display')
                        ->label('Compare Price')
                        ->content(fn() => $this->comparePrice ? '$' . $this->comparePrice : 'Not set'),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('edit_map_price')
                            ->label('Edit in Lunar Pricing')
                            ->icon('heroicon-o-pencil')
                            ->url(fn() => ProductResource::getUrl('pricing', ['record' => $this->getRecord()]))
                            ->openUrlInNewTab(),
                    ]),
                ]),
            ]);

        // Per-group pricing sections
        foreach ($this->groupPricing as $groupId => $groupData) {
            $schema[] = $this->buildGroupPricingSection($groupId, $groupData);
        }

        return $form->schema($schema)->statePath('');
    }

    protected function buildGroupPricingSection(int $groupId, array $groupData): Forms\Components\Section
    {
        $isConsumer = strtolower($groupData['group_handle']) === 'consumer';

        return Forms\Components\Section::make($groupData['group_name'] . ' Pricing')
            ->description("Configure pricing for {$groupData['group_name']} customers. Leave Group Price blank to use MAP price.")
            ->collapsible()
            ->collapsed(!$isConsumer) // Expand Consumer by default
            ->schema([
                Forms\Components\Grid::make(4)->schema([
                    Forms\Components\TextInput::make("groupPricing.{$groupId}.products_minimum")
                        ->label('Products Minimum')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->helperText('Min products in cart to qualify'),

                    Forms\Components\TextInput::make("groupPricing.{$groupId}.base_price")
                        ->label('Group Base Price')
                        ->numeric()
                        ->step(0.01)
                        ->prefix('$')
                        ->placeholder('Use MAP')
                        ->helperText('Override MAP price'),

                    Forms\Components\DatePicker::make("groupPricing.{$groupId}.expires_at")
                        ->label('Sale Expires')
                        ->visible($isConsumer)
                        ->helperText('For promotional pricing'),

                    Forms\Components\Radio::make("groupPricing.{$groupId}.tier_method")
                        ->label('Tier Method')
                        ->options([
                            'quantity' => 'By Quantity',
                            'cart_total' => 'By Cart Total',
                        ])
                        ->default('quantity')
                        ->inline()
                        ->live(),
                ]),

                Forms\Components\Repeater::make("groupPricing.{$groupId}.tiers")
                    ->label('Pricing Tiers')
                    ->schema([
                        Forms\Components\Hidden::make('id'),
                        Forms\Components\TextInput::make('cutoff')
                            ->label(fn(Forms\Get $get) =>
                                ($this->groupPricing[$groupId]['tier_method'] ?? 'quantity') === 'cart_total'
                                    ? 'Min Cart Total ($)'
                                    : 'Min Quantity')
                            ->numeric()
                            ->required()
                            ->placeholder('e.g., 6'),
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('$')
                            ->required()
                            ->placeholder('e.g., 19.99'),
                    ])
                    ->columns(2)
                    ->addActionLabel('Add Tier')
                    ->reorderable(false)
                    ->defaultItems(0)
                    ->itemLabel(fn(array $state): ?string =>
                        !empty($state['cutoff']) && !empty($state['price'])
                            ? "Buy {$state['cutoff']}+ @ \${$state['price']}"
                            : null
                    ),
            ]);
    }

    public function getRelationManagers(): array
    {
        return [];
    }
}
