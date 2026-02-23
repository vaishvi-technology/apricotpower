<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ShipmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shipments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('carrier')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('service')
                    ->maxLength(100),
                Forms\Components\TextInput::make('tracking_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tracking_url')
                    ->url()
                    ->maxLength(500),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'label_created' => 'Label Created',
                        'in_transit' => 'In Transit',
                        'out_for_delivery' => 'Out for Delivery',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                        'returned' => 'Returned',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\TextInput::make('weight')
                    ->numeric()
                    ->suffix('lbs'),
                Forms\Components\TextInput::make('shipping_cost')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\DateTimePicker::make('shipped_at'),
                Forms\Components\DateTimePicker::make('delivered_at'),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tracking_number')
            ->columns([
                Tables\Columns\TextColumn::make('carrier'),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->copyable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'label_created' => 'info',
                        'in_transit' => 'primary',
                        'out_for_delivery' => 'primary',
                        'delivered' => 'success',
                        'failed' => 'danger',
                        'returned' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->dateTime()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->dateTime()
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'label_created' => 'Label Created',
                        'in_transit' => 'In Transit',
                        'out_for_delivery' => 'Out for Delivery',
                        'delivered' => 'Delivered',
                        'failed' => 'Failed',
                        'returned' => 'Returned',
                    ]),
            ])
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
