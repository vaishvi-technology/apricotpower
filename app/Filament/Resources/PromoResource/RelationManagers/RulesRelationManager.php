<?php

namespace App\Filament\Resources\PromoResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RulesRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    protected static ?string $title = 'Promo Rules';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rule Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Rules are checked in order from lowest to highest. The first rule whose conditions are met will be applied — remaining rules are skipped.')
                            ->helperText('Lower numbers are evaluated first.'),
                    ])->columns(2),

                Forms\Components\Section::make('Conditions')
                    ->description('Define when this rule applies. All enabled conditions must be met.')
                    ->schema([
                        // Items Condition
                        Forms\Components\Fieldset::make('Items in Cart')
                            ->schema([
                                Forms\Components\Toggle::make('cond_is_items')
                                    ->label('Enable Items Condition')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When enabled, the cart must contain specific items from the list below for this rule to apply.')
                                    ->live(),
                                Forms\Components\Select::make('cond_item_list')
                                    ->label('Items')
                                    ->multiple()
                                    ->options(fn () => Product::all()->mapWithKeys(fn ($product) => [
                                        $product->id => $product->translateAttribute('name') ?? ('Product #' . $product->id),
                                    ])->sort()->toArray())
                                    ->searchable()
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Select the products that the customer must have in their cart. Use the ALL/ANY toggle below to control whether all or just one of these items is required.')
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        if (is_string($state) && $state !== '') {
                                            $component->state(array_map('intval', array_filter(explode(',', $state))));
                                        }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_items')),
                                Forms\Components\Toggle::make('cond_item_all')
                                    ->label('Require ALL items (vs ANY)')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ON (ALL): Every item in the list must be in the cart. OFF (ANY): At least one item from the list must be in the cart.')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_items')),
                                Forms\Components\TextInput::make('cond_item_quantity')
                                    ->label('Minimum Quantity per Item')
                                    ->numeric()
                                    ->default(1)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The minimum quantity of each required item. In ALL mode, every item needs this many. In ANY mode, at least one item needs this many. Set to 0 to require just presence.')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_items')),
                            ])->columns(2),

                        // Subtotal Condition
                        Forms\Components\Fieldset::make('Cart Subtotal')
                            ->schema([
                                Forms\Components\Toggle::make('cond_is_subtotal')
                                    ->label('Enable Subtotal Condition')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When enabled, the cart subtotal (excluding promo items) must fall within the min/max range for this rule to apply.')
                                    ->live(),
                                Forms\Components\TextInput::make('cond_subtotal_min')
                                    ->label('Minimum Subtotal ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Cart subtotal must be at least this amount. Set to 0 to have no minimum requirement.')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_subtotal')),
                                Forms\Components\TextInput::make('cond_subtotal_max')
                                    ->label('Maximum Subtotal ($)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Cart subtotal must not exceed this amount. Set to 0 for no maximum limit.')
                                    ->helperText('0 = no maximum')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_subtotal')),
                            ])->columns(2),

                        // Weight Condition
                        Forms\Components\Fieldset::make('Cart Weight')
                            ->schema([
                                Forms\Components\Toggle::make('cond_is_weight')
                                    ->label('Enable Weight Condition')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When enabled, the total cart weight (in oz, excluding promo items) must meet the specified threshold.')
                                    ->live(),
                                Forms\Components\TextInput::make('cond_weight_amount')
                                    ->label('Weight (oz)')
                                    ->numeric()
                                    ->default(0)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The weight threshold in ounces. Use the toggle below to set whether the cart weight must be above or below this value.')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_weight')),
                                Forms\Components\Toggle::make('cond_weight_greater_than')
                                    ->label('Weight Must Be Greater Than (vs Less Than)')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ON: Cart weight must be greater than the specified amount. OFF: Cart weight must be less than or equal to the specified amount.')
                                    ->default(true)
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_weight')),
                            ])->columns(2),
                    ]),

                Forms\Components\Section::make('Actions')
                    ->description('Define what happens when this rule matches. At least one action is required.')
                    ->schema([
                        // Discount Action
                        Forms\Components\Fieldset::make('Discount')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_discount')
                                    ->label('Apply Discount')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Enable to apply a discount to the cart. Can be a fixed dollar amount or a percentage. NOTE: Using Discount with Free Items or BOGO may cause issues.')
                                    ->live(),
                                Forms\Components\TextInput::make('act_discount_amount')
                                    ->label('Discount Amount')
                                    ->numeric()
                                    ->default(0)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The discount value. If percentage mode is ON, this is a percent (e.g. 10 = 10% off). If OFF, this is a fixed dollar amount (e.g. 5 = $5.00 off).')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount')),
                                Forms\Components\Toggle::make('act_discount_is_percent')
                                    ->label('Is Percentage (%)')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ON: Discount amount is treated as a percentage (e.g. 10 = 10% off). OFF: Discount amount is a fixed dollar value (e.g. 5 = $5.00 off).')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount')),
                                Forms\Components\Toggle::make('act_discount_is_for_items')
                                    ->label('Apply to Specific Items Only')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ON: Discount applies only to specific items selected below. OFF: Discount applies to the entire cart subtotal.')
                                    ->live()
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount')),
                                Forms\Components\Select::make('act_discount_item_list')
                                    ->label('Items to Discount')
                                    ->multiple()
                                    ->options(fn () => Product::all()->mapWithKeys(fn ($product) => [
                                        $product->id => $product->translateAttribute('name') ?? ('Product #' . $product->id),
                                    ])->sort()->toArray())
                                    ->searchable()
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        if (is_string($state) && $state !== '') {
                                            $component->state(array_map('intval', array_filter(explode(',', $state))));
                                        }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount') && $get('act_discount_is_for_items')),
                                Forms\Components\TextInput::make('act_discount_limit')
                                    ->label('Max Items to Discount')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('0 = unlimited')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount') && $get('act_discount_is_for_items')),
                            ])->columns(2),

                        // Free Shipping Action
                        Forms\Components\Fieldset::make('Free Shipping')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_free_shipping')
                                    ->label('Grant Free Shipping')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When enabled, the customer gets free shipping on their order when this rule is applied.'),
                            ]),

                        // Free Items Action
                        Forms\Components\Fieldset::make('Free Items')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_free_items')
                                    ->label('Add Free Items')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When enabled, free items are automatically added to the cart. The quantity is calculated based on the rule conditions (e.g. for every X items bought, give Y free).')
                                    ->live(),
                                Forms\Components\Toggle::make('act_item_is_all')
                                    ->label('Add ALL Items in List (vs choose one)')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'ON (All): All items in the free items list will be added to the cart. OFF (One): Only the first item from the list will be added.')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_free_items')),
                                Forms\Components\Select::make('act_item_list')
                                    ->label('Free Items')
                                    ->multiple()
                                    ->options(fn () => Product::all()->mapWithKeys(fn ($product) => [
                                        $product->id => $product->translateAttribute('name') ?? ('Product #' . $product->id),
                                    ])->sort()->toArray())
                                    ->searchable()
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        if (is_string($state) && $state !== '') {
                                            $component->state(array_map('intval', array_filter(explode(',', $state))));
                                        }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                                    ->visible(fn (Forms\Get $get) => $get('act_is_free_items')),
                                Forms\Components\TextInput::make('act_item_limit')
                                    ->label('Max Free Items')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('0 = unlimited')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_free_items')),
                            ])->columns(2),

                        // BOGO Action
                        Forms\Components\Fieldset::make('Buy One Get One (BOGO)')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_bogo')
                                    ->label('Enable BOGO')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Buy One Get One: For every X items purchased from the eligible list, the customer gets Y additional items at a discounted price (or free).')
                                    ->live(),
                                Forms\Components\Select::make('act_bogo_item_list')
                                    ->label('Eligible Items')
                                    ->multiple()
                                    ->options(fn () => Product::all()->mapWithKeys(fn ($product) => [
                                        $product->id => $product->translateAttribute('name') ?? ('Product #' . $product->id),
                                    ])->sort()->toArray())
                                    ->searchable()
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        if (is_string($state) && $state !== '') {
                                            $component->state(array_map('intval', array_filter(explode(',', $state))));
                                        }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => is_array($state) && count($state) ? implode(',', $state) : null)
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo')),
                                Forms\Components\TextInput::make('act_bogo_buy_count')
                                    ->label('Buy Count')
                                    ->numeric()
                                    ->default(1)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Number of items the customer must buy to trigger the BOGO deal. E.g. "Buy 2" means every 2 items purchased qualifies.')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo')),
                                Forms\Components\TextInput::make('act_bogo_get_count')
                                    ->label('Get Count')
                                    ->numeric()
                                    ->default(1)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Number of free/discounted items the customer receives per qualifying set. E.g. "Get 1" means 1 item is added at the discounted price.')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo')),
                                Forms\Components\TextInput::make('act_bogo_discount')
                                    ->label('Discount on Free Items (%)')
                                    ->numeric()
                                    ->default(100)
                                    ->suffix('%')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Percentage discount on the "Get" items. 100% = completely free, 50% = half price, etc.')
                                    ->helperText('100 = completely free')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo')),
                                Forms\Components\TextInput::make('act_bogo_limit')
                                    ->label('Max BOGO Sets')
                                    ->numeric()
                                    ->default(0)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Maximum number of free/discounted items from the BOGO deal per order. Set to 0 for unlimited.')
                                    ->helperText('0 = unlimited')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo')),
                            ])->columns(2),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\IconColumn::make('cond_is_items')
                    ->label('Items Cond.')
                    ->boolean(),
                Tables\Columns\IconColumn::make('cond_is_subtotal')
                    ->label('Subtotal Cond.')
                    ->boolean(),
                Tables\Columns\IconColumn::make('act_is_discount')
                    ->label('Discount')
                    ->boolean(),
                Tables\Columns\TextColumn::make('act_discount_amount')
                    ->label('Discount Amt')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->act_is_discount) return '—';
                        return $record->act_discount_is_percent ? "{$state}%" : "\${$state}";
                    }),
                Tables\Columns\IconColumn::make('act_is_free_shipping')
                    ->label('Free Ship')
                    ->boolean(),
                Tables\Columns\IconColumn::make('act_is_bogo')
                    ->label('BOGO')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
