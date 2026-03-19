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
                Forms\Components\Section::make(fn ($record) => $record
                        ? 'Promo Details — Total Uses: ' . $record->used_count . ($record->limit_total > 0 ? " / {$record->limit_total}" : '')
                        : 'Promo Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Promo Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->label('Friendly Name')
                            ->maxLength(255)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'This is the title of the Promo that will be visible to customers. If left blank, it will use the above name.')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('coupon_code')
                            ->label('Coupon Code')
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->rows(2),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('is_hidden')
                                    ->label('Hidden?')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Since promos that have been used cannot be deleted, this option hides the promo from the admin page. Used to keep old inactive promos out of the way. Does not disable the promo if this is on while the promo is still Active (below).')
                                    ->default(false),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Active?')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'If set to Active and a Start Date is set, the promo will NOT be valid until the Start Date. If left Inactive, promo will be disabled regardless of Start Date / End Date values.')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_auto')
                                    ->label('Auto Apply?')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'If the box is checked, this promo will attempt to apply to every cart, without the customer having to enter a coupon or clicking a link. The user will NOT be notified or prevented from checking out if the promo does not apply, however!'),
                            ])->columnSpanFull(),
                        Forms\Components\TextInput::make('landing_url')
                            ->label('Promo Landing URL')
                            ->url()
                            ->placeholder('https://...')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'After using the Promo Link, the user will be redirected to this page as opposed to the default landing page. Leave blank to redirect to the default cart page.')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('redeem_link')
                            ->label('Promo Links')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Copy this link to give to customers. If there is a coupon for this promo, two links will be provided. Please note that both links function exactly the same, but the coupon link is easier for customers to type in manually, if used for print ads.')
                            ->content(fn ($record) => $record?->coupon_code
                                ? url('/redeem-promo?promo=' . urlencode($record->coupon_code))
                                : 'Save the promo with a coupon code to generate a link.')
                            ->visibleOn('edit')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('valid_start')
                            ->label('Start Date'),
                        Forms\Components\DateTimePicker::make('valid_end')
                            ->label('End Date'),
                        Forms\Components\TextInput::make('limit_per_customer')
                            ->label('Limit Per Customer')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Leave 0 for unlimited uses.'),
                        Forms\Components\TextInput::make('limit_total')
                            ->label('Total Limit')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Leave 0 for unlimited uses. This number is the total number of carts that are allowed to use this promo.'),
                        Forms\Components\Select::make('account_groups')
                            ->label('Account Groups')
                            ->multiple()
                            ->options(fn () => CustomerGroup::pluck('name', 'id')->toArray())
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'If any account groups are checked, only those groups will be eligible to use the promo. Must choose at least one.')
                            ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                if (is_string($state) && $state !== '') {
                                    $component->state(array_map('intval', array_filter(explode(',', $state))));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('countries')
                            ->label('Countries')
                            ->multiple()
                            ->options(fn () => Country::orderBy('name')->pluck('name', 'iso2')->toArray())
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'If any countries are checked, only orders in those countries will be eligible to use the promo. Must choose at least one.')
                            ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                if (is_string($state) && $state !== '') {
                                    $component->state(array_filter(array_map('trim', explode(',', $state))));
                                }
                            })
                            ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                            ->searchable()
                            ->required(),
                        Forms\Components\Toggle::make('disable_volume_discounts')
                            ->label('Disable Volume Discounts?')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'This option will stop volume discounts from applying to the cart.'),
                        Forms\Components\Repeater::make('autocart_items_repeater')
                            ->label('Autocart Items')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'These items will be added to the users cart if they do not already have them. The cart cannot have less than the quantity shown. Estimated total based on default Retail pricing NOTE: Autocart Items WILL NOT be added with the \'Auto Apply\' option.')
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
                    ])->columns(2),

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
