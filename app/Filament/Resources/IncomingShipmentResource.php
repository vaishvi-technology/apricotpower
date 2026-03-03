<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomingShipmentResource\Pages;
use App\Models\IncomingShipment;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IncomingShipmentResource extends Resource
{
    protected static ?string $model = IncomingShipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Incoming Shipments';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Shipment Details')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->label('Product')
                        ->options(fn () => Product::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if ($state) {
                                $product = Product::find($state);
                                if ($product && $product->supplier_id) {
                                    $set('supplier_id', $product->supplier_id);
                                }
                            }
                        }),

                    Forms\Components\Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(fn () => Supplier::active()->pluck('company_name', 'id'))
                        ->searchable()
                        ->preload()
                        ->helperText('Defaults to product\'s primary supplier.'),

                    Forms\Components\TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->minValue(1),

                    Forms\Components\DatePicker::make('expected_date')
                        ->label('Expected Delivery Date'),

                    Forms\Components\Select::make('status')
                        ->options(IncomingShipment::getStatusOptions())
                        ->default(IncomingShipment::STATUS_PENDING)
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Tracking')
                ->schema([
                    Forms\Components\TextInput::make('tracking_url')
                        ->label('Tracking URL')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('supplier.company_name')
                    ->label('Supplier')
                    ->searchable()
                    ->placeholder('No supplier'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_date')
                    ->label('Expected')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record?->is_overdue ? 'danger' : null),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        IncomingShipment::STATUS_PENDING => 'warning',
                        IncomingShipment::STATUS_IN_TRANSIT => 'info',
                        IncomingShipment::STATUS_RECEIVED => 'success',
                        IncomingShipment::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tracking_url')
                    ->label('Tracking')
                    ->limit(20)
                    ->url(fn ($record) => $record?->tracking_url)
                    ->openUrlInNewTab()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('expected_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(IncomingShipment::getStatusOptions())
                    ->default(IncomingShipment::STATUS_PENDING),
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'company_name'),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(fn ($query) => $query->overdue())
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('receive')
                    ->label('Mark Received')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, [
                        IncomingShipment::STATUS_PENDING,
                        IncomingShipment::STATUS_IN_TRANSIT,
                    ]))
                    ->requiresConfirmation()
                    ->modalHeading('Receive Shipment')
                    ->modalDescription('This will create an inventory lot from this shipment and mark it as received.')
                    ->action(function ($record) {
                        try {
                            app(InventoryService::class)->receiveFromShipment($record);
                            Notification::make()
                                ->title('Shipment received')
                                ->body('Inventory lot created successfully.')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error receiving shipment')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', [
            IncomingShipment::STATUS_PENDING,
            IncomingShipment::STATUS_IN_TRANSIT,
        ])->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomingShipments::route('/'),
            'create' => Pages\CreateIncomingShipment::route('/create'),
            'edit' => Pages\EditIncomingShipment::route('/{record}/edit'),
        ];
    }
}
