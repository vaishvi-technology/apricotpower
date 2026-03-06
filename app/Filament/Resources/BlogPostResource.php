<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Lunar\Admin\Models\Staff;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Posts';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Content')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(BlogPost::class, 'slug', ignoreRecord: true)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('excerpt')
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('content')
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Media')
                ->schema([
                    Forms\Components\FileUpload::make('featured_image')
                        ->image()
                        ->directory('blog/images')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('16:9')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Taxonomy')
                ->schema([
                    Forms\Components\Select::make('blog_category_id')
                        ->label('Category')
                        ->options(BlogCategory::active()->orderBy('sort_order')->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('tags')
                        ->label('Tags')
                        ->relationship('tags', 'name')
                        ->options(BlogTag::active()->orderBy('sort_order')->pluck('name', 'id'))
                        ->multiple()
                        ->searchable()
                        ->preload(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Author & Publishing')
                ->schema([
                    Forms\Components\Select::make('author_id')
                        ->label('Author')
                        ->options(
                            Staff::query()
                                ->orderBy('first_name')
                                ->get()
                                ->mapWithKeys(fn (Staff $staff) => [$staff->id => $staff->full_name . ' (' . $staff->email . ')'])
                        )
                        ->searchable()
                        ->preload(),

                    Forms\Components\DatePicker::make('published_at')
                        ->label('Publish Date'),

                    Forms\Components\Toggle::make('is_published')
                        ->label('Published')
                        ->default(false),

                    Forms\Components\Toggle::make('is_featured')
                        ->label('Featured')
                        ->default(false),

                    Forms\Components\Toggle::make('is_nav_featured')
                        ->label('Show in Nav Dropdown')
                        ->helperText('Appears in the BLOG nav hover dropdown (max 5).')
                        ->default(false),

                    Forms\Components\Toggle::make('is_pinned')
                        ->label('Pin at Top of Blog List')
                        ->helperText('Pinned posts appear in the featured strip at the top of /blogs.')
                        ->default(false),
                ])
                ->columns(2),

            Forms\Components\Section::make('SEO')
                ->schema([
                    Forms\Components\TextInput::make('meta_title')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('meta_description')
                        ->maxLength(500)
                        ->rows(3),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('blog_category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),

                Tables\Filters\TernaryFilter::make('is_nav_featured')
                    ->label('In Nav'),

                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Pinned'),
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
