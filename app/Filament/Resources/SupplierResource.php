<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Company Information')
                ->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->required()
                        ->maxLength(100),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Inactive suppliers will not appear in dropdown lists.'),
                ])->columns(2),

            Forms\Components\Section::make('Contact Information')
                ->schema([
                    Forms\Components\TextInput::make('contact_name')
                        ->label('Contact Name')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(100),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(100),
                ])->columns(3),

            Forms\Components\Section::make('Business Terms')
                ->schema([
                    Forms\Components\TextInput::make('supplier_terms')
                        ->label('Supplier Terms')
                        ->placeholder('e.g., Net 30, COD, Prepaid')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('lead_time')
                        ->label('Lead Time (Days)')
                        ->numeric()
                        ->minValue(0)
                        ->helperText('Default lead time for orders from this supplier.'),
                ])->columns(2),

            Forms\Components\Section::make('Notes')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Internal Notes')
                        ->rows(3)
                        ->helperText('Internal notes about this supplier.')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('supplier_terms')
                    ->label('Terms')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lead_time')
                    ->label('Lead Time')
                    ->suffix(' days')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->default(true),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
