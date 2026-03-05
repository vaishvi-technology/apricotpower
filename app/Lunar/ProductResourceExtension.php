<?php

namespace App\Lunar;

use App\Lunar\Filament\Resources\ProductResource\Pages\ManageProductGroupPricing;
use App\Lunar\Filament\Resources\ProductResource\Pages\ManageProductInventoryLots;
use App\Lunar\Filament\Resources\ProductResource\Pages\ManageProductNutritionFacts;
use App\Lunar\Filament\Resources\ProductResource\Pages\ManageProductShipping;
use App\Lunar\Filament\Resources\ProductResource\Pages\ManageProductSupplier;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\ResourceExtension;

class ProductResourceExtension extends ResourceExtension
{
    /**
     * Extend the pages array with custom nutrition facts page.
     */
    public function extendPages(array $pages): array
    {
        return array_merge($pages, [
            'shipping' => ManageProductShipping::route('/{record}/shipping'),
            'nutrition-facts' => ManageProductNutritionFacts::route('/{record}/nutrition-facts'),
            'inventory-lots' => ManageProductInventoryLots::route('/{record}/inventory-lots'),
            'supplier' => ManageProductSupplier::route('/{record}/supplier'),
            'group-pricing' => ManageProductGroupPricing::route('/{record}/group-pricing'),
        ]);
    }

    /**
     * Extend the subnavigation with nutrition facts link.
     */
    public function extendSubNavigation(array $pages): array
    {
        // Filter out the default shipping page and add our custom one
        $filtered = collect($pages)->filter(function ($page) {
            return $page !== \Lunar\Admin\Filament\Resources\ProductResource\Pages\ManageProductShipping::class;
        })->values()->all();

        return array_merge($filtered, [
            ManageProductShipping::class,
            ManageProductNutritionFacts::class,
            ManageProductInventoryLots::class,
            ManageProductSupplier::class,
            ManageProductGroupPricing::class,
        ]);
    }

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
                    if ($child instanceof Forms\Components\Select && $child->getName() === 'product_type_id') {
                        return false;
                    }
                    return true;
                })->values()->all();

                // Add categories multi-select at the beginning of the section
                $categorySelect = Forms\Components\Select::make('categories')
                    ->label('Categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload();

                return $component->schema([$categorySelect, ...$filteredSchema]);
            }
            return $component;
        })->all();

        return $form->schema([
            ...$filtered,

            Forms\Components\Section::make('Product Details')
                ->description('Basic product information displayed on the storefront.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Product Name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(4)
                        ->helperText('Full product description displayed on the product page.')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('quantity_size')
                        ->label('Quantity/Size')
                        ->placeholder('e.g., 8 oz, 16 oz, 100 tablets')
                        ->maxLength(255)
                        ->helperText('Product size or quantity displayed on the product page.'),
                ]),

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

            Forms\Components\Section::make('Product Badges')
                ->description('Assign certification badges to display on the product page.')
                ->collapsible()
                ->schema([
                    Forms\Components\CheckboxList::make('badge_keys')
                        ->label('Select Badges')
                        ->options(collect(config('badges'))->mapWithKeys(fn ($badge, $key) => [$key => $badge['name']]))
                        ->columns(2)
                        ->helperText('Select certification badges to display for this product.'),
                ]),

            Forms\Components\Section::make('Content Tabs')
                ->description('Content displayed in product page tabs.')
                ->collapsible()
                ->schema([
                    Forms\Components\RichEditor::make('intro_content')
                        ->label('Item Description')
                        ->helperText('Content displayed in the Intro tab on the product page.')
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('learn_more')
                        ->label('Learn More')
                        ->helperText('Content displayed in the Learn More tab on the product page.')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Supplier Information')
                ->description('Primary supplier and inventory notes for this product.')
                ->collapsible()
                ->schema([
                    Forms\Components\Select::make('supplier_id')
                        ->label('Primary Supplier')
                        ->relationship('supplier', 'company_name')
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('company_name')
                                ->required()
                                ->maxLength(100),
                            Forms\Components\TextInput::make('contact_name')
                                ->maxLength(100),
                            Forms\Components\TextInput::make('phone')
                                ->tel(),
                            Forms\Components\TextInput::make('email')
                                ->email(),
                            Forms\Components\TextInput::make('supplier_terms')
                                ->label('Supplier Terms')
                                ->placeholder('e.g., Net 30'),
                            Forms\Components\TextInput::make('lead_time')
                                ->label('Lead Time (Days)')
                                ->numeric(),
                        ])
                        ->createOptionUsing(function (array $data) {
                            return Supplier::create($data)->id;
                        }),

                    Forms\Components\Textarea::make('inventory_notes')
                        ->label('Inventory Notes')
                        ->rows(3)
                        ->helperText('Internal notes about inventory management for this product.')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Miscellaneous')
                ->description('Cart message and disclaimer settings.')
                ->collapsible()
                ->schema([
                    Forms\Components\Textarea::make('disclaimer')
                        ->label('Item Disclaimer / Cart Message')
                        ->rows(4)
                        ->helperText('Message displayed in shopping cart for this product. Leave empty for no message.')
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('disclaimer_agreement')
                        ->label('Require Customer Agreement')
                        ->helperText('Customer must check a box to acknowledge before checkout.')
                        ->default(false),
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
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categories')
                    ->badge()
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->label('Meta Title')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('supplier.company_name')
                    ->label('Supplier')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                ...$filters,
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Category')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }
}
