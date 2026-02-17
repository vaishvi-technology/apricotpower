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

        return $form->schema([
            ...$existing,

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
        return $table->columns([
            ...$table->getColumns(),
            Tables\Columns\TextColumn::make('meta_title')
                ->label('Meta Title')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
    }
}
