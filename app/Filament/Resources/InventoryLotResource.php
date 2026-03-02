<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryLotResource\Pages;
use App\Filament\Resources\InventoryLotResource\RelationManagers;
use App\Models\InventoryLot;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryLotResource extends Resource
{
    protected static ?string $model = InventoryLot::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Inventory Lots';

    protected static ?string $recordTitleAttribute = 'lot_number';

    protected static ?string $modelLabel = 'Inventory Lot';

    protected static ?string $pluralModelLabel = 'Inventory Lots';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Product')
                            ->options(fn () => Product::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('product_variant_id', null)),

                        Forms\Components\Select::make('product_variant_id')
                            ->label('Variant (Optional)')
                            ->options(fn (Forms\Get $get) =>
                                ProductVariant::where('product_id', $get('product_id'))
                                    ->pluck('sku', 'id')
                            )
                            ->searchable()
                            ->placeholder('Default (No Variant)')
                            ->visible(fn (Forms\Get $get) => $get('product_id')),
                    ])->columns(2),

                Forms\Components\Section::make('Lot Details')
                    ->schema([
                        Forms\Components\TextInput::make('lot_number')
                            ->label('Lot Number')
                            ->maxLength(255)
                            ->placeholder('Auto-generated if empty')
                            ->helperText('Leave empty to auto-generate'),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(0),

                        Forms\Components\TextInput::make('cost_per_unit')
                            ->label('Cost Per Unit')
                            ->numeric()
                            ->prefix('$')
                            ->step(0.01)
                            ->minValue(0),

                        Forms\Components\TextInput::make('location')
                            ->label('Storage Location')
                            ->maxLength(255)
                            ->placeholder('e.g., Warehouse A, Shelf 3'),
                    ])->columns(2),

                Forms\Components\Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('received_at')
                            ->label('Received Date')
                            ->default(now()),

                        Forms\Components\DatePicker::make('expires_at')
                            ->label('Expiration Date')
                            ->helperText('Leave empty if no expiration'),
                    ])->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->product?->name),

                Tables\Columns\TextColumn::make('variant.sku')
                    ->label('SKU')
                    ->placeholder('Default')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('lot_number')
                    ->label('Lot #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    })
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cost_per_unit')
                    ->label('Cost/Unit')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('received_at')
                    ->label('Received')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->color(fn ($record): string => match (true) {
                        $record->is_expired => 'danger',
                        $record->is_expiring_soon => 'warning',
                        default => 'success',
                    })
                    ->placeholder('No expiration'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->state(fn ($record): string => match (true) {
                        $record->is_expired => 'expired',
                        $record->is_expiring_soon => 'expiring',
                        $record->quantity <= 0 => 'out_of_stock',
                        default => 'in_stock',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'expired' => 'heroicon-o-x-circle',
                        'expiring' => 'heroicon-o-exclamation-triangle',
                        'out_of_stock' => 'heroicon-o-minus-circle',
                        'in_stock' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'expired' => 'danger',
                        'expiring' => 'warning',
                        'out_of_stock' => 'gray',
                        'in_stock' => 'success',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        'expired' => 'Expired',
                        'expiring' => 'Expiring Soon',
                        'out_of_stock' => 'Out of Stock',
                        'in_stock' => 'In Stock',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('in_stock')
                    ->label('In Stock')
                    ->queries(
                        true: fn (Builder $query) => $query->inStock(),
                        false: fn (Builder $query) => $query->where('quantity', '<=', 0),
                    ),

                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon (30 days)')
                    ->query(fn (Builder $query) => $query->expiringSoon())
                    ->toggle(),

                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn (Builder $query) => $query->expired())
                    ->toggle(),

                Tables\Filters\Filter::make('has_location')
                    ->label('Has Location')
                    ->query(fn (Builder $query) => $query->whereNotNull('location'))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('adjust')
                        ->label('Adjust Qty')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('type')
                                ->label('Adjustment Type')
                                ->options([
                                    'add' => 'Add (+)',
                                    'subtract' => 'Subtract (-)',
                                ])
                                ->required()
                                ->default('add'),

                            Forms\Components\TextInput::make('quantity')
                                ->label('Quantity')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->default(1),

                            Forms\Components\Textarea::make('reason')
                                ->label('Reason')
                                ->required()
                                ->rows(2)
                                ->placeholder('Why are you adjusting this inventory?'),
                        ])
                        ->action(function (InventoryLot $record, array $data): void {
                            app(InventoryService::class)->adjustInventory($record, $data);
                        })
                        ->successNotificationTitle('Inventory adjusted successfully'),

                    Tables\Actions\DeleteAction::make(),
                ]),
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
            RelationManagers\MovementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryLots::route('/'),
            'create' => Pages\CreateInventoryLot::route('/create'),
            'edit' => Pages\EditInventoryLot::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::expiringSoon()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['lot_number', 'product.name', 'location'];
    }
}
