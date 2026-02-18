<?php

namespace App\Lunar;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\ResourceExtension;

class TagResourceExtension extends ResourceExtension
{
    /**
     * Rename label to "Item Tag".
     */
    public static function getLabel(): string
    {
        return 'Item Tag';
    }

    public static function getPluralLabel(): string
    {
        return 'Item Tags';
    }

    /**
     * Group Tags under "Catalog" collapsible section with Items.
     */
    public static function getNavigationGroup(): string
    {
        return 'Catalog';
    }

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        return $form->schema([
            // Basic Info Section
            Forms\Components\Section::make('Tag Details')
                ->description('Basic tag information.')
                ->schema([
                    Forms\Components\TextInput::make('value')
                        ->label('Name')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\RichEditor::make('description')
                        ->label('Description')
                        ->columnSpanFull(),
                ]),

            // Flags Section
            Forms\Components\Section::make('Visibility')
                ->description('Control tag visibility.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_stealth')
                            ->label('Stealth Mode')
                            ->helperText('Hide tag from public view'),

                        Forms\Components\Toggle::make('is_hidden')
                            ->label('Hidden')
                            ->helperText('Completely hide this tag'),
                    ]),
                ]),

            // Badge Section
            Forms\Components\Section::make('Badge')
                ->description('Tag badge configuration.')
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('badge_image')
                        ->label('Badge Image')
                        ->maxLength(100)
                        ->helperText('Path to badge PNG image'),

                    Forms\Components\RichEditor::make('badge_description')
                        ->label('Badge Description'),
                ]),

            // SEO Meta Fields Section
            Forms\Components\Section::make('SEO Meta Fields')
                ->description('Search engine optimization settings.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta Title')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->rows(3),

                    Forms\Components\Textarea::make('meta_keywords')
                        ->label('Meta Keywords')
                        ->rows(2),
                ]),

            // Open Graph Section
            Forms\Components\Section::make('Open Graph')
                ->description('Social media sharing settings.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('og_title')
                            ->label('OG Title')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('og_type')
                            ->label('OG Type')
                            ->maxLength(50),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('og_image')
                            ->label('OG Image URL')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('og_url')
                            ->label('OG URL')
                            ->maxLength(255),
                    ]),
                ]),
        ]);
    }

    public function extendTable(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('value')
                ->label('Name')
                ->searchable()
                ->sortable(),

            Tables\Columns\IconColumn::make('is_stealth')
                ->label('Stealth')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\IconColumn::make('is_hidden')
                ->label('Hidden')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
    }
}
