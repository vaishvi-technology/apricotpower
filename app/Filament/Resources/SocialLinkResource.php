<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages;
use App\Models\SocialLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Social Links';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Social Platform')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('e.g. Facebook, Twitter, Pinterest')
                        ->helperText('Display name for this social platform.'),

                    Forms\Components\FileUpload::make('icon')
                        ->label('Logo / Icon')
                        ->directory('social-icons')
                        ->image()
                        ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/jpeg', 'image/webp'])
                        ->helperText('Upload an SVG or PNG icon (recommended: square, transparent background).'),

                    Forms\Components\TextInput::make('color')
                        ->label('Brand Color')
                        ->type('color')
                        ->placeholder('#1877F2')
                        ->helperText('The brand color used for hover effects and styling.'),
                ])
                ->columns(3),

            Forms\Components\Section::make('Share URL Configuration')
                ->schema([
                    Forms\Components\TextInput::make('share_url_pattern')
                        ->label('Share URL Pattern')
                        ->maxLength(500)
                        ->placeholder('https://www.facebook.com/sharer/sharer.php?u={url}')
                        ->helperText('Use placeholders: {url} = post URL, {title} = post title, {excerpt} = post excerpt. Leave empty for non-sharing links.')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Settings')
                ->schema([
                    Forms\Components\Toggle::make('open_in_new_tab')
                        ->label('Open in New Tab')
                        ->default(true),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers appear first.'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->label('Icon')
                    ->circular()
                    ->size(32),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ColorColumn::make('color')
                    ->label('Brand Color'),

                Tables\Columns\TextColumn::make('share_url_pattern')
                    ->label('Share Pattern')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\TextColumn::make('blog_posts_count')
                    ->label('Posts Using')
                    ->counts('blogPosts')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialLinks::route('/'),
            'create' => Pages\CreateSocialLink::route('/create'),
            'edit' => Pages\EditSocialLink::route('/{record}/edit'),
        ];
    }
}
