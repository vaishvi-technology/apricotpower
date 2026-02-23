<?php

namespace App\Lunar;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\ResourceExtension;

class ProductResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        // Filter out the brand_id field from existing components
        $filtered = collect($existing)->map(function ($component) {
            if ($component instanceof Forms\Components\Section) {
                $schema = $component->getChildComponents();
                $filteredSchema = collect($schema)->filter(function ($child) {
                    if ($child instanceof Forms\Components\Select && $child->getName() === 'brand_id') {
                        return false;
                    }
                    return true;
                })->values()->all();

                // Add category select at the beginning of the section
                $categorySelect = Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(fn () => \App\Models\Category::pluck('name', 'id'))
                    ->searchable()
                    ->preload();

                return $component->schema([$categorySelect, ...$filteredSchema]);
            }
            return $component;
        })->all();

        return $form->schema([
            ...$filtered,

            Forms\Components\Section::make('SEO Meta Fields')
                ->description('Search engine optimization settings for this product.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(255),
                    ]),

                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->rows(3)
                        ->maxLength(500),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('meta_og_title')
                            ->label('Meta OG Title')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('meta_og_keywords')
                            ->label('Meta OG Keywords')
                            ->maxLength(255),
                    ]),

                    Forms\Components\TextInput::make('meta_og_image')
                        ->label('Meta OG Image')
                        ->url()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('meta_og_url')
                        ->label('Meta OG URL')
                        ->url()
                        ->maxLength(255),
                ]),
        ]);
    }

    public function extendTable(Table $table): Table
    {
        // Filter out brand column
        $columns = collect($table->getColumns())->filter(function ($column) {
            return $column->getName() !== 'brand.name';
        })->values()->all();

        // Filter out brand filter
        $filters = collect($table->getFilters())->filter(function ($filter) {
            return $filter->getName() !== 'brand';
        })->values()->all();

        return $table
            ->columns([
                ...$columns,
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->label('Meta Title')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                ...$filters,
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn () => \App\Models\Category::pluck('name', 'id')),
            ]);
    }
}
