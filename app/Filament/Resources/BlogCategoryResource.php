<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Categories';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Category Details')
                ->schema([
                    Forms\Components\Select::make('parent_id')
                        ->label('Parent Category')
                        ->options(fn (?BlogCategory $record) => BlogCategory::query()
                            ->whereNull('parent_id')
                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            ->orderBy('sort_order')
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->placeholder('None (top-level category)')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(BlogCategory::class, 'slug', ignoreRecord: true),

                    Forms\Components\Textarea::make('description')
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\ColorPicker::make('accent_color')
                        ->label('Accent Color')
                        ->helperText('Used as the badge background color on blog cards.'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (BlogCategory $record) {
                        $prefix = $record->parent_id ? '— ' : '';
                        return $prefix . $record->name;
                    }),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),

                Tables\Columns\ColorColumn::make('accent_color')
                    ->label('Color'),

                Tables\Columns\TextColumn::make('published_posts_count')
                    ->label('Posts')
                    ->counts('publishedPosts')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent')
                    ->options(
                        BlogCategory::whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                    )
                    ->placeholder('All'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
