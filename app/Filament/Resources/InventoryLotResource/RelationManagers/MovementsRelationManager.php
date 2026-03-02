<?php

namespace App\Filament\Resources\InventoryLotResource\RelationManagers;

use App\Models\InventoryMovement;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'Movement History';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date/Time')
                    ->dateTime()
                    ->sortable(),

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
                    ->numeric(),

                Tables\Columns\TextColumn::make('quantity_after')
                    ->label('After')
                    ->numeric(),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Reason')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->reason),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('System'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        InventoryMovement::TYPE_RECEIVED => 'Received',
                        InventoryMovement::TYPE_SOLD => 'Sold',
                        InventoryMovement::TYPE_ADJUSTED => 'Adjusted',
                        InventoryMovement::TYPE_RETURNED => 'Returned',
                        InventoryMovement::TYPE_DAMAGED => 'Damaged',
                        InventoryMovement::TYPE_TRANSFERRED => 'Transferred',
                    ]),
            ])
            ->headerActions([
                // No create action - movements are created through adjustments
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for audit log
            ]);
    }
}
