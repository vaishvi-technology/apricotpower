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
                Forms\Components\Section::make('Rule Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Rule Name')
                            ->required()
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Rule Conditions')
                    ->description('At least one condition must be enabled. All enabled conditions must be met.')
                    ->schema([
                        // Weight Condition
                        Forms\Components\Fieldset::make('Weight')
                            ->schema([
                                Forms\Components\Toggle::make('cond_is_weight')
                                    ->label('Enable')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('cond_weight_greater_than')
                                    ->label('Cart weight is')
                                    ->options([
                                        '0' => 'Less Than',
                                        '1' => 'Greater Than',
                                    ])
                                    ->default('0')
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        $component->state($state ? '1' : '0');
                                    })
                                    ->dehydrateStateUsing(fn ($state) => $state === '1')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_weight')),
                                Forms\Components\TextInput::make('cond_weight_lbs')
                                    ->label('lbs.')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $record) {
                                        $totalOz = $record?->cond_weight_amount ?? 0;
                                        $component->state(intdiv((int) $totalOz, 16));
                                    })
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_weight')),
                                Forms\Components\TextInput::make('cond_weight_oz')
                                    ->label('oz.')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $record) {
                                        $totalOz = $record?->cond_weight_amount ?? 0;
                                        $component->state(((int) $totalOz) % 16);
                                    })
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_weight')),
                                Forms\Components\Hidden::make('cond_weight_amount')
                                    ->dehydrateStateUsing(function (Forms\Get $get) {
                                        $lbs = (int) ($get('cond_weight_lbs') ?? 0);
                                        $oz = (int) ($get('cond_weight_oz') ?? 0);
                                        return ($lbs * 16) + $oz;
                                    }),
                            ])->columns(3),

                        // Subtotal Condition
                        Forms\Components\Fieldset::make('Subtotal')
                            ->schema([
                                Forms\Components\Toggle::make('cond_is_subtotal')
                                    ->label('Enable')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Leave the Less Than field blank or at 0 if there is no maximum cart total.')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('cond_subtotal_min')
                                    ->label('Cart subtotal is equal to or greater than')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->default(0)
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_subtotal')),
                                Forms\Components\TextInput::make('cond_subtotal_max')
                                    ->label('but equal to or less than')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('$')
                                    ->default(0)
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Leave the Less Than field blank or at 0 if there is no maximum cart total.')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_subtotal')),
                            ])->columns(2),

                        // Items Condition
                        Forms\Components\Fieldset::make('Items')
                            ->schema([
                                Forms\Components\Toggle::make('cond_is_items')
                                    ->label('Enable')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('cond_item_quantity')
                                    ->label('Cart contains')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(1)
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_items')),
                                Forms\Components\Select::make('cond_item_all')
                                    ->label('of')
                                    ->options([
                                        '0' => 'Any',
                                        '1' => 'All',
                                    ])
                                    ->default('0')
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        $component->state($state ? '1' : '0');
                                    })
                                    ->dehydrateStateUsing(fn ($state) => $state === '1')
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_items')),
                                Forms\Components\Select::make('cond_item_list')
                                    ->label('of the following items:')
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
                                    ->visible(fn (Forms\Get $get) => $get('cond_is_items'))
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ]),

                Forms\Components\Section::make('Rule Actions')
                    ->description('NOTE: Using the \'Discount\' action along with \'Free Items\' or \'BOGO\' may cause issues.')
                    ->schema([
                        // Discount Action
                        Forms\Components\Fieldset::make('Discount')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_discount')
                                    ->label('Enable')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The option for \'Use Above Items\' is a function that will essentially set the Discount Action to \'Specific Items\' and check all the same items that are checked in the Items condition above.')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('act_discount_is_percent')
                                    ->label('Discount')
                                    ->options([
                                        '0' => '$',
                                        '1' => '%',
                                    ])
                                    ->default('0')
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        $component->state($state ? '1' : '0');
                                    })
                                    ->dehydrateStateUsing(fn ($state) => $state === '1')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount')),
                                Forms\Components\TextInput::make('act_discount_amount')
                                    ->label('Amount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount')),
                                Forms\Components\Select::make('act_discount_is_for_items')
                                    ->label('off of')
                                    ->options([
                                        'full_cart' => 'Full Cart',
                                        'above_items' => 'Use Above Items',
                                        'specific' => 'Specific Items',
                                    ])
                                    ->default('full_cart')
                                    ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                        if ($state === true || $state === 1 || $state === '1') {
                                            $component->state('specific');
                                        } elseif ($state === 'above_items') {
                                            $component->state('above_items');
                                        } else {
                                            $component->state('full_cart');
                                        }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => match ($state) {
                                        'specific' => true,
                                        'above_items' => true,
                                        default => false,
                                    })
                                    ->live()
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount')),
                                Forms\Components\Select::make('act_discount_item_list')
                                    ->label('Specific Items')
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
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount') && $get('act_discount_is_for_items') === 'specific')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('act_discount_limit')
                                    ->label('Limit to')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->suffix('eligible items (leave at 0 for unlimited).')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_discount'))
                                    ->columnSpanFull(),
                            ])->columns(3),

                        // Free Shipping Action
                        Forms\Components\Fieldset::make('Free Shipping')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_free_shipping')
                                    ->label('Cart will qualify for free shipping'),
                            ]),

                        // Free Items Action
                        Forms\Components\Fieldset::make('Free Items')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_free_items')
                                    ->label('Enable')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(5)
                                    ->schema([
                                        Forms\Components\Placeholder::make('free_items_label_add')
                                            ->hiddenLabel()
                                            ->content('Add')
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('act_item_is_all')
                                            ->hiddenLabel()
                                            ->options([
                                                '0' => 'Any',
                                                '1' => 'All',
                                            ])
                                            ->default('0')
                                            ->afterStateHydrated(function (Forms\Components\Select $component, $state) {
                                                $component->state($state ? '1' : '0');
                                            })
                                            ->dehydrateStateUsing(fn ($state) => $state === '1')
                                            ->columnSpan(1),
                                        Forms\Components\Placeholder::make('free_items_label_end')
                                            ->hiddenLabel()
                                            ->content('of the following items to the cart:')
                                            ->columnSpan(3),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('act_is_free_items'))
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('act_item_list')
                                    ->hiddenLabel()
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
                                    ->visible(fn (Forms\Get $get) => $get('act_is_free_items'))
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(5)
                                    ->schema([
                                        Forms\Components\Placeholder::make('free_items_label_limit')
                                            ->hiddenLabel()
                                            ->content('Limit to')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('act_item_limit')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->columnSpan(1),
                                        Forms\Components\Placeholder::make('free_items_label_limit_end')
                                            ->hiddenLabel()
                                            ->content('items (Leave at 0 for unlimited; repeats each time they meet the minimum rule conditions).')
                                            ->columnSpan(3),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('act_is_free_items'))
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // BOGO Action
                        Forms\Components\Fieldset::make('BOGO')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_bogo')
                                    ->label('Enable')
                                    ->live()
                                    ->columnSpanFull(),
                                // Every [buy] item(s) that get added, add another [get] of the same item at [discount] % off.
                                Forms\Components\Grid::make(7)
                                    ->schema([
                                        Forms\Components\Placeholder::make('bogo_label_every')
                                            ->hiddenLabel()
                                            ->content('Every')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('act_bogo_buy_count')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(1)
                                            ->columnSpan(1),
                                        Forms\Components\Placeholder::make('bogo_label_items')
                                            ->hiddenLabel()
                                            ->content('item(s) that get added, add another')
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('act_bogo_get_count')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(1)
                                            ->columnSpan(1),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo'))
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(5)
                                    ->schema([
                                        Forms\Components\Placeholder::make('bogo_label_same')
                                            ->hiddenLabel()
                                            ->content('of the same item at')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('act_bogo_discount')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(100)
                                            ->columnSpan(1),
                                        Forms\Components\Placeholder::make('bogo_label_off')
                                            ->hiddenLabel()
                                            ->content('% off.')
                                            ->columnSpan(3),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo'))
                                    ->columnSpanFull(),
                                // Applies to the following items:
                                Forms\Components\Placeholder::make('bogo_label_applies')
                                    ->hiddenLabel()
                                    ->content('Applies to the following items:')
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo'))
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('act_bogo_item_list')
                                    ->hiddenLabel()
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
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo'))
                                    ->columnSpanFull(),
                                // Limit to [limit] items (Per item; leave at 0 for unlimited).
                                Forms\Components\Grid::make(5)
                                    ->schema([
                                        Forms\Components\Placeholder::make('bogo_label_limit')
                                            ->hiddenLabel()
                                            ->content('Limit to')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('act_bogo_limit')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->columnSpan(1),
                                        Forms\Components\Placeholder::make('bogo_label_limit_end')
                                            ->hiddenLabel()
                                            ->content('items (Per item; leave at 0 for unlimited).')
                                            ->columnSpan(3),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('act_is_bogo'))
                                    ->columnSpanFull(),
                            ]),

                        // LoyaltyLion Points Action
                        Forms\Components\Fieldset::make('LoyaltyLion Points')
                            ->schema([
                                Forms\Components\Toggle::make('act_is_ll_points')
                                    ->label('Enable')
                                    ->live()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(5)
                                    ->schema([
                                        Forms\Components\Placeholder::make('ll_label_start')
                                            ->hiddenLabel()
                                            ->content('Award LL Points:')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('act_ll_points_amount')
                                            ->hiddenLabel()
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->columnSpan(1),
                                        Forms\Components\Placeholder::make('ll_label_end')
                                            ->hiddenLabel()
                                            ->content('(consumer accounts only)')
                                            ->columnSpan(3),
                                    ])
                                    ->visible(fn (Forms\Get $get) => $get('act_is_ll_points'))
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Rule Name')
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
                Tables\Actions\CreateAction::make()
                    ->label('Add New Rule'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete Rule'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
