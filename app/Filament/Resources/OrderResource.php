<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Order Information')
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->default(fn () => Order::generateOrderNumber())
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\Select::make('customer_id')
                                    ->options(fn () => \App\Models\Customer::pluck('email', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        Order::STATUS_PENDING => 'Pending',
                                        Order::STATUS_PROCESSING => 'Processing',
                                        Order::STATUS_SHIPPED => 'Shipped',
                                        Order::STATUS_DELIVERED => 'Delivered',
                                        Order::STATUS_CANCELLED => 'Cancelled',
                                        Order::STATUS_REFUNDED => 'Refunded',
                                    ])
                                    ->default(Order::STATUS_PENDING)
                                    ->required(),
                                Forms\Components\Select::make('payment_status')
                                    ->options([
                                        Order::PAYMENT_STATUS_PENDING => 'Pending',
                                        Order::PAYMENT_STATUS_PAID => 'Paid',
                                        Order::PAYMENT_STATUS_FAILED => 'Failed',
                                        Order::PAYMENT_STATUS_REFUNDED => 'Refunded',
                                        Order::PAYMENT_STATUS_NET_TERMS => 'Net Terms',
                                    ])
                                    ->default(Order::PAYMENT_STATUS_PENDING)
                                    ->required(),
                            ])->columns(2),

                        Forms\Components\Section::make('Shipping Address')
                            ->schema([
                                Forms\Components\TextInput::make('shipping_first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_company')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_address_line_1')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_address_line_2')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('shipping_city')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('shipping_state')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('shipping_postal_code')
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('shipping_country')
                                    ->required()
                                    ->maxLength(2)
                                    ->default('US'),
                                Forms\Components\TextInput::make('shipping_phone')
                                    ->tel()
                                    ->maxLength(20),
                            ])->columns(2),

                        Forms\Components\Section::make('Billing Address')
                            ->schema([
                                Forms\Components\TextInput::make('billing_first_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_last_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_company')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_address_line_1')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_address_line_2')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('billing_city')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('billing_state')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('billing_postal_code')
                                    ->required()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('billing_country')
                                    ->required()
                                    ->maxLength(2)
                                    ->default('US'),
                                Forms\Components\TextInput::make('billing_phone')
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('billing_email')
                                    ->email()
                                    ->maxLength(255),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Order Totals')
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('discount_amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('shipping_amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('tax_amount')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                                Forms\Components\TextInput::make('total')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                            ]),

                        Forms\Components\Section::make('Shipping')
                            ->schema([
                                Forms\Components\TextInput::make('shipping_method')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('coupon_code')
                                    ->maxLength(50),
                            ]),

                        Forms\Components\Section::make('Payment')
                            ->schema([
                                Forms\Components\DatePicker::make('payment_due_date'),
                                Forms\Components\DateTimePicker::make('paid_at'),
                            ]),

                        Forms\Components\Section::make('Notes')
                            ->schema([
                                Forms\Components\Textarea::make('customer_notes')
                                    ->rows(2),
                                Forms\Components\Textarea::make('admin_notes')
                                    ->rows(2),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_PENDING => 'warning',
                        Order::STATUS_PROCESSING => 'info',
                        Order::STATUS_SHIPPED => 'primary',
                        Order::STATUS_DELIVERED => 'success',
                        Order::STATUS_CANCELLED => 'danger',
                        Order::STATUS_REFUNDED => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Order::PAYMENT_STATUS_PAID => 'success',
                        Order::PAYMENT_STATUS_PENDING => 'warning',
                        Order::PAYMENT_STATUS_FAILED => 'danger',
                        Order::PAYMENT_STATUS_REFUNDED => 'gray',
                        Order::PAYMENT_STATUS_NET_TERMS => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Order::STATUS_PENDING => 'Pending',
                        Order::STATUS_PROCESSING => 'Processing',
                        Order::STATUS_SHIPPED => 'Shipped',
                        Order::STATUS_DELIVERED => 'Delivered',
                        Order::STATUS_CANCELLED => 'Cancelled',
                        Order::STATUS_REFUNDED => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        Order::PAYMENT_STATUS_PENDING => 'Pending',
                        Order::PAYMENT_STATUS_PAID => 'Paid',
                        Order::PAYMENT_STATUS_FAILED => 'Failed',
                        Order::PAYMENT_STATUS_REFUNDED => 'Refunded',
                        Order::PAYMENT_STATUS_NET_TERMS => 'Net Terms',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->overdue())
                    ->label('Overdue'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\ShipmentsRelationManager::class,
            RelationManagers\StatusHistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['order_number', 'customer.email'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', Order::STATUS_PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
