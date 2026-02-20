<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Details')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => Str::upper(Str::random(8))),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Discount')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->options([
                                Coupon::TYPE_PERCENTAGE => 'Percentage',
                                Coupon::TYPE_FIXED_AMOUNT => 'Fixed Amount',
                                Coupon::TYPE_FREE_SHIPPING => 'Free Shipping',
                            ])
                            ->required()
                            ->default(Coupon::TYPE_PERCENTAGE)
                            ->live(),
                        Forms\Components\TextInput::make('discount_value')
                            ->numeric()
                            ->required()
                            ->suffix(fn (Forms\Get $get) => $get('discount_type') === Coupon::TYPE_PERCENTAGE ? '%' : '$')
                            ->visible(fn (Forms\Get $get) => $get('discount_type') !== Coupon::TYPE_FREE_SHIPPING),
                        Forms\Components\TextInput::make('minimum_order_amount')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('No minimum'),
                        Forms\Components\TextInput::make('maximum_discount_amount')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('No maximum')
                            ->visible(fn (Forms\Get $get) => $get('discount_type') === Coupon::TYPE_PERCENTAGE),
                    ])->columns(2),

                Forms\Components\Section::make('Usage Limits')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->placeholder('Unlimited'),
                        Forms\Components\TextInput::make('usage_limit_per_customer')
                            ->numeric()
                            ->placeholder('Unlimited'),
                        Forms\Components\TextInput::make('times_used')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),

                Forms\Components\Section::make('Product Restrictions')
                    ->schema([
                        Forms\Components\Toggle::make('applies_to_all_products')
                            ->label('Applies to All Products')
                            ->default(true)
                            ->live(),
                        Forms\Components\Toggle::make('exclude_sale_items')
                            ->label('Exclude Sale Items'),
                        Forms\Components\Select::make('products')
                            ->relationship('products', 'id')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => !$get('applies_to_all_products')),
                        Forms\Components\Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get) => !$get('applies_to_all_products')),
                    ])->columns(2),

                Forms\Components\Section::make('Validity')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Start Date'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expiry Date'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('discount_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Coupon::TYPE_PERCENTAGE => 'success',
                        Coupon::TYPE_FIXED_AMOUNT => 'info',
                        Coupon::TYPE_FREE_SHIPPING => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        Coupon::TYPE_PERCENTAGE => 'Percentage',
                        Coupon::TYPE_FIXED_AMOUNT => 'Fixed',
                        Coupon::TYPE_FREE_SHIPPING => 'Free Shipping',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('discount_value')
                    ->formatStateUsing(fn ($state, Coupon $record): string =>
                        $record->discount_type === Coupon::TYPE_PERCENTAGE
                            ? "{$state}%"
                            : ($record->discount_type === Coupon::TYPE_FREE_SHIPPING ? '—' : "\${$state}")
                    ),
                Tables\Columns\TextColumn::make('times_used')
                    ->label('Used')
                    ->formatStateUsing(fn ($state, Coupon $record): string =>
                        $record->usage_limit ? "{$state}/{$record->usage_limit}" : $state
                    ),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_valid')
                    ->boolean()
                    ->label('Valid'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\SelectFilter::make('discount_type')
                    ->options([
                        Coupon::TYPE_PERCENTAGE => 'Percentage',
                        Coupon::TYPE_FIXED_AMOUNT => 'Fixed Amount',
                        Coupon::TYPE_FREE_SHIPPING => 'Free Shipping',
                    ]),
                Tables\Filters\Filter::make('expired')
                    ->query(fn ($query) => $query->where('expires_at', '<', now()))
                    ->label('Expired'),
                Tables\Filters\Filter::make('valid')
                    ->query(fn ($query) => $query->valid())
                    ->label('Currently Valid'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'name'];
    }
}
