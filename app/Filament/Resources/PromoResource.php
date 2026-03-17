<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Filament\Resources\PromoResource\RelationManagers;
use App\Models\CustomerGroup;
use App\Models\Promo;
use App\Models\ProductVariant;
use Lunar\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promo Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Internal name for this promo.'),
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255)
                            ->helperText('Customer-facing title.'),
                        Forms\Components\TextInput::make('coupon_code')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Leave blank for auto-apply promos.'),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Activation')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When enabled, this promo can be used by customers. Disable to temporarily deactivate without deleting.')
                            ->default(true),
                        Forms\Components\Toggle::make('is_auto')
                            ->label('Auto-Apply')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Automatically apply this promo when cart conditions are met — no coupon code needed. The system checks all auto-apply promos on every cart update.')
                            ->helperText('Automatically apply when conditions are met (no code needed).'),
                        Forms\Components\Toggle::make('is_hidden')
                            ->label('Hidden/Archived')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Hidden promos are excluded from public listings but can still be redeemed if the customer has the code.')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('valid_start')
                            ->label('Valid From'),
                        Forms\Components\DateTimePicker::make('valid_end')
                            ->label('Valid Until'),
                    ])->columns(3),

                Forms\Components\Section::make('Usage Limits')
                    ->schema([
                        Forms\Components\TextInput::make('limit_per_customer')
                            ->numeric()
                            ->default(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Leave `0` for unlimited uses.')
                            ->helperText('0 = unlimited'),
                        Forms\Components\TextInput::make('limit_total')
                            ->numeric()
                            ->default(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Leave `0` for unlimited uses. This number is the total number of carts that are allowed to use this promo.')
                            ->helperText('0 = unlimited'),
                    ])->columns(2),

                Forms\Components\Section::make('Restrictions')
                    ->schema([
                        Forms\Components\Select::make('account_groups')
                            ->label('Account Groups')
                            ->multiple()
                            ->options(fn () => CustomerGroup::pluck('name', 'id')->toArray())
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Restrict this promo to specific customer groups (e.g. Consumer, Wholesale). Leave empty to allow all account types.')
                            ->helperText('Select account groups. Leave empty for all.')
                            ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                if (is_string($state) && $state !== '') {
                                    $component->state(array_map('intval', array_filter(explode(',', $state))));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                            ->searchable(),
                        Forms\Components\Select::make('countries')
                            ->label('Countries')
                            ->multiple()
                            ->options(fn () => Country::orderBy('name')->pluck('name', 'iso2')->toArray())
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Restrict this promo to orders shipping to specific countries. Leave empty to allow all countries.')
                            ->helperText('Select countries. Leave empty for all.')
                            ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                if (is_string($state) && $state !== '') {
                                    $component->state(array_filter(array_map('trim', explode(',', $state))));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                            ->searchable(),
                        Forms\Components\Toggle::make('disable_volume_discounts')
                            ->label('Disable Volume Discounts')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When enabled, any volume/tier pricing discounts will be ignored while this promo is active on the cart. Useful to prevent stacking promo discounts with volume discounts.')
                            ->helperText('Prevent volume discounts when this promo is active.'),
                    ])->columns(2),

                Forms\Components\Section::make('Auto-Cart Items')
                    ->description('Items automatically added to the cart when this promo is applied.')
                    ->schema([
                        Forms\Components\Repeater::make('autocart_items_repeater')
                            ->label('')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Items listed here are automatically added to the cart when this promo is applied. If the item is already in the cart with fewer quantity, it will be increased to the specified amount.')
                            ->schema([
                                Forms\Components\Select::make('variant_id')
                                    ->label('Product')
                                    ->options(fn () => ProductVariant::with('product')
                                        ->get()
                                        ->mapWithKeys(fn ($variant) => [
                                            $variant->id => ($variant->product?->translateAttribute('name') ?? 'Product') .
                                                ($variant->sku ? " ({$variant->sku})" : " (Variant #{$variant->id})"),
                                        ])
                                        ->sort()
                                        ->toArray())
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add Auto-Cart Item')
                            ->reorderable(false)
                            ->afterStateHydrated(function (Forms\Components\Repeater $component, $record) {
                                if (!$record || empty($record->autocart_items)) {
                                    $component->state([]);
                                    return;
                                }
                                $items = [];
                                $parts = array_filter(explode('|', $record->autocart_items));
                                foreach ($parts as $part) {
                                    $pair = explode('=', trim($part));
                                    if (count($pair) === 2 && (int) $pair[1] > 0) {
                                        $items[] = [
                                            'variant_id' => (int) $pair[0],
                                            'quantity' => (int) $pair[1],
                                        ];
                                    }
                                }
                                $component->state($items);
                            })
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('autocart_items')
                            ->dehydrateStateUsing(function (Forms\Get $get) {
                                $items = $get('autocart_items_repeater');
                                if (empty($items) || !is_array($items)) {
                                    return null;
                                }
                                $parts = [];
                                foreach ($items as $item) {
                                    $variantId = $item['variant_id'] ?? null;
                                    $qty = $item['quantity'] ?? 0;
                                    if ($variantId && $qty > 0) {
                                        $parts[] = "{$variantId}={$qty}";
                                    }
                                }
                                return !empty($parts) ? '|' . implode('|', $parts) . '|' : null;
                            }),
                    ])->collapsed(),

                Forms\Components\Section::make('Landing Page')
                    ->schema([
                        Forms\Components\TextInput::make('landing_url')
                            ->url()
                            ->placeholder('https://...')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'After clicking the promo link, the customer will be redirected to this URL instead of the default cart page. Leave empty to redirect to the cart page.')
                            ->columnSpanFull(),
                    ])->collapsed(),

                Forms\Components\Section::make('Promo Links')
                    ->schema([
                        Forms\Components\Placeholder::make('redeem_link')
                            ->label('Redeem URL')
                            ->content(fn ($record) => $record?->coupon_code
                                ? url('/redeem-promo?promo=' . urlencode($record->coupon_code))
                                : 'Save the promo with a coupon code to generate a link.'),
                        Forms\Components\Placeholder::make('usage_stats')
                            ->label('Total Uses')
                            ->content(fn ($record) => $record
                                ? ($record->used_count . ($record->limit_total > 0 ? " / {$record->limit_total}" : ' (unlimited)'))
                                : '0'),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('coupon_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->placeholder('Auto-Apply'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_auto')
                    ->label('Auto')
                    ->boolean(),
                Tables\Columns\TextColumn::make('active_code')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ACTIVE' => 'success',
                        'ACTIVE_UNTIL' => 'info',
                        'PENDING' => 'warning',
                        'EXPIRED' => 'danger',
                        'INACTIVE' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('rules_count')
                    ->counts('rules')
                    ->label('Rules'),
                Tables\Columns\TextColumn::make('usages_count')
                    ->counts('usages')
                    ->label('Used'),
                Tables\Columns\TextColumn::make('limit_total')
                    ->label('Limit')
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : 'Unlimited'),
                Tables\Columns\TextColumn::make('valid_start')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('No start')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('valid_end')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('No end'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_auto')
                    ->label('Auto-Apply'),
                Tables\Filters\TernaryFilter::make('is_hidden')
                    ->label('Hidden'),
                Tables\Filters\Filter::make('currently_valid')
                    ->query(fn ($query) => $query->valid())
                    ->label('Currently Valid'),
                Tables\Filters\Filter::make('expired')
                    ->query(fn ($query) => $query->where('valid_end', '<', now()))
                    ->label('Expired'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'coupon_code', 'title'];
    }
}
