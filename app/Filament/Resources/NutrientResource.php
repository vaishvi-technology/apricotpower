<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NutrientResource\Pages;
use App\Models\Nutrient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NutrientResource extends Resource
{
    protected static ?string $model = Nutrient::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Nutrients';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nutrient Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Name'),
                        Forms\Components\TextInput::make('display_title')
                            ->required()
                            ->maxLength(255)
                            ->label('Display Title')
                            ->helperText('Title shown on nutrition label'),
                        Forms\Components\Select::make('display_class')
                            ->options([
                                '' => 'Normal',
                                'bold' => 'Bold',
                                'indent' => 'Indented',
                                'indent-2' => 'Double Indented',
                            ])
                            ->label('Display Style'),
                        Forms\Components\TextInput::make('rank')
                            ->numeric()
                            ->default(0)
                            ->label('Sort Order')
                            ->helperText('Lower numbers appear first'),
                    ])->columns(2),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_funky')
                            ->label('Specialty Nutrient')
                            ->helperText('Mark as specialty/funky nutrient (like B17 Amygdalin)'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_class')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bold' => 'primary',
                        'indent' => 'gray',
                        'indent-2' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('rank')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_funky')
                    ->label('Specialty')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('rank')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_funky')
                    ->label('Specialty'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('rank');
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
            'index' => Pages\ListNutrients::route('/'),
            'create' => Pages\CreateNutrient::route('/create'),
            'edit' => Pages\EditNutrient::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'display_title'];
    }
}
