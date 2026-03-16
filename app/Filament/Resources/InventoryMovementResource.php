<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryMovementResource\Pages;
use App\Models\InventoryMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryMovementResource extends Resource
{
    protected static ?string $model = InventoryMovement::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Inventory History';

    protected static ?string $modelLabel = 'Inventory Movement';

    protected static ?string $pluralModelLabel = 'Inventory History';

    public static function form(Form $form): Form
    {
        // Read-only resource - no form needed for create/edit
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Movement Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Date/Time')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('type')
                            ->label('Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                InventoryMovement::TYPE_RECEIVED => 'success',
                                InventoryMovement::TYPE_SOLD => 'primary',
                                InventoryMovement::TYPE_ADJUSTED => 'warning',
                                InventoryMovement::TYPE_RETURNED => 'info',
                                InventoryMovement::TYPE_DAMAGED => 'danger',
                                InventoryMovement::TYPE_TRANSFERRED => 'gray',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                        Infolists\Components\TextEntry::make('quantity')
                            ->label('Quantity Change')
                            ->formatStateUsing(fn (int $state): string =>
                                $state > 0 ? "+{$state}" : (string) $state
                            )
                            ->color(fn (int $state): string =>
                                $state > 0 ? 'success' : 'danger'
                            ),
                    ])->columns(3),

                Infolists\Components\Section::make('Stock Levels')
                    ->schema([
                        Infolists\Components\TextEntry::make('quantity_before')
                            ->label('Quantity Before'),

                        Infolists\Components\TextEntry::make('quantity_after')
                            ->label('Quantity After'),
                    ])->columns(2),

                Infolists\Components\Section::make('Product Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('product_name')
                            ->label('Product')
                            ->state(fn ($record) => $record->product?->name ?? $record->product?->translateAttribute('name') ?? 'Unknown'),

                        Infolists\Components\TextEntry::make('variant.sku')
                            ->label('Variant SKU')
                            ->placeholder('Default'),

                        Infolists\Components\TextEntry::make('inventoryLot.lot_number')
                            ->label('Lot Number')
                            ->placeholder('N/A'),
                    ])->columns(3),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('reason')
                            ->label('Reason')
                            ->placeholder('No reason provided'),

                        Infolists\Components\TextEntry::make('performed_by')
                            ->label('Performed By')
                            ->state(fn ($record) => $record->user?->name ?? $record->staff?->fullName ?? 'System'),

                        Infolists\Components\TextEntry::make('reference_type')
                            ->label('Reference Type')
                            ->placeholder('N/A')
                            ->visible(fn ($record) => $record->reference_type),

                        Infolists\Components\TextEntry::make('reference_id')
                            ->label('Reference ID')
                            ->placeholder('N/A')
                            ->visible(fn ($record) => $record->reference_id),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date/Time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->getStateUsing(fn ($record) => $record->product?->name ?? $record->product?->translateAttribute('name') ?? 'Unknown')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('product', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhereRaw("JSON_EXTRACT(attribute_data, '$.name.value') LIKE ?", ["%{$search}%"]);
                        });
                    })
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->product?->name ?? $record->product?->translateAttribute('name')),

                Tables\Columns\TextColumn::make('inventoryLot.lot_number')
                    ->label('Lot #')
                    ->placeholder('N/A')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        InventoryMovement::TYPE_RECEIVED => 'success',
                        InventoryMovement::TYPE_SOLD => 'primary',
                        InventoryMovement::TYPE_ADJUSTED => 'warning',
                        InventoryMovement::TYPE_RETURNED => 'info',
                        InventoryMovement::TYPE_DAMAGED => 'danger',
                        InventoryMovement::TYPE_TRANSFERRED => 'gray',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Change')
                    ->formatStateUsing(fn (int $state): string =>
                        $state > 0 ? "+{$state}" : (string) $state
                    )
                    ->color(fn (int $state): string =>
                        $state > 0 ? 'success' : 'danger'
                    )
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('quantity_before')
                    ->label('Before')
                    ->numeric()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity_after')
                    ->label('After')
                    ->numeric()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->reason)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('performed_by')
                    ->label('Performed By')
                    ->state(fn ($record) => $record->user?->name ?? $record->staff?->fullName ?? 'System')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Movement Type')
                    ->options([
                        InventoryMovement::TYPE_RECEIVED => 'Received',
                        InventoryMovement::TYPE_SOLD => 'Sold',
                        InventoryMovement::TYPE_ADJUSTED => 'Adjusted',
                        InventoryMovement::TYPE_RETURNED => 'Returned',
                        InventoryMovement::TYPE_DAMAGED => 'Damaged',
                        InventoryMovement::TYPE_TRANSFERRED => 'Transferred',
                    ]),

                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->options(fn () => \App\Models\Product::all()->mapWithKeys(fn ($product) => [
                        $product->id => $product->translateAttribute('name') ?? 'Product #' . $product->id
                    ]))
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->columns(2),

                Tables\Filters\TernaryFilter::make('is_addition')
                    ->label('Direction')
                    ->placeholder('All')
                    ->trueLabel('Additions (+)')
                    ->falseLabel('Removals (-)')
                    ->queries(
                        true: fn (Builder $query) => $query->where('quantity', '>', 0),
                        false: fn (Builder $query) => $query->where('quantity', '<', 0),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for audit log
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryMovements::route('/'),
            'view' => Pages\ViewInventoryMovement::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Read-only resource
    }

    public static function canEdit($record): bool
    {
        return false; // Read-only resource
    }

    public static function canDelete($record): bool
    {
        return false; // Read-only resource
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['product.name', 'inventoryLot.lot_number', 'reason'];
    }
}
