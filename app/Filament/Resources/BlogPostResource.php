<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\SocialLink;
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
            Forms\Components\Grid::make(3)
                ->schema([
                    // ── Left Column (2/3) ──
                    Forms\Components\Group::make([
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
                                    ->unique(BlogPost::class, 'slug', ignoreRecord: true),

                                Forms\Components\Textarea::make('excerpt')
                                    ->maxLength(500)
                                    ->rows(3),

                                Forms\Components\RichEditor::make('content')
                                    ->required(),
                            ]),

                        Forms\Components\Section::make('Featured Image')
                            ->schema([
                                Forms\Components\FileUpload::make('featured_image')
                                    ->image()
                                    ->directory('blog/images')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                            ])
                            ->compact(),

                        Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(500)
                                    ->rows(2),
                            ])
                            ->collapsed()
                            ->compact(),
                    ])->columnSpan(2),

                    // ── Right Sidebar (1/3) ──
                    Forms\Components\Group::make([
                        Forms\Components\Section::make('Publishing')
                            ->schema([
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(false),

                                Forms\Components\Select::make('author_id')
                                    ->label('Author')
                                    ->options(
                                        Staff::query()
                                            ->orderBy('first_name')
                                            ->get()
                                            ->mapWithKeys(fn (Staff $staff) => [$staff->id => $staff->full_name.' ('.$staff->email.')'])
                                    )
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\DatePicker::make('published_at')
                                    ->label('Publish Date'),

                                Forms\Components\Toggle::make('is_pinned')
                                    ->label('Featured / Pin at Top')
                                    ->helperText('Highlighted strip at top of /blogs.')
                                    ->default(false),

                                Forms\Components\Toggle::make('is_nav_featured')
                                    ->label('Show in Nav Dropdown')
                                    ->helperText('BLOG nav hover dropdown (max 5).')
                                    ->default(false),
                            ])
                            ->compact(),

                        Forms\Components\Section::make('Categories')
                            ->schema([
                                Forms\Components\Select::make('categories')
                                    ->hiddenLabel()
                                    ->relationship('categories', 'name')
                                    ->options(
                                        BlogCategory::active()
                                            ->orderBy('sort_order')
                                            ->get()
                                            ->mapWithKeys(fn (BlogCategory $cat) => [$cat->id => $cat->full_name])
                                    )
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->compact(),

                        Forms\Components\Section::make('Tags')
                            ->schema([
                                Forms\Components\Select::make('tags')
                                    ->hiddenLabel()
                                    ->relationship('tags', 'name')
                                    ->options(BlogTag::active()->orderBy('sort_order')->pluck('name', 'id'))
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),
                            ])
                            ->compact(),

                        Forms\Components\Section::make('Social Sharing')
                            ->schema([
                                Forms\Components\Select::make('socialLinks')
                                    ->hiddenLabel()
                                    ->relationship('socialLinks', 'name')
                                    ->options(
                                        SocialLink::active()
                                            ->orderBy('sort_order')
                                            ->pluck('name', 'id')
                                    )
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Manage platforms under Blog > Social Links.'),
                            ])
                            ->compact()
                            ->collapsed(),
                    ])->columnSpan(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->words(12),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categories')
                    ->badge()
                    ->separator(',')
                    ->wrap(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Category')
                    ->relationship('categories', 'name'),

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
