<?php

namespace App\Lunar;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Lunar\Admin\Support\Extending\ResourceExtension;

class ProductResourceExtension extends ResourceExtension
{
    /**
     * Rename "Product" to "Item" in sidebar and UI.
     */
    public static function getLabel(): string
    {
        return 'Item';
    }

    public static function getPluralLabel(): string
    {
        return 'Items';
    }

    /**
     * Group Items under "Catalog" collapsible section.
     */
    public static function getNavigationGroup(): string
    {
        return 'Catalog';
    }

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public function extendForm(Form $form): Form
    {
        $existing = $form->getComponents(withHidden: true);

        return $form->schema([
            ...$existing,

            // Basic Item Info Section
            Forms\Components\Section::make('Item Details')
                ->description('Basic item information.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('descriptor')
                            ->label('Descriptor')
                            ->maxLength(200),
                    ]),

                    Forms\Components\Textarea::make('short_description')
                        ->label('Short Description')
                        ->rows(3),

                    Forms\Components\RichEditor::make('description')
                        ->label('Full Description')
                        ->columnSpanFull(),
                ]),

            // Pricing Section
            Forms\Components\Section::make('Pricing')
                ->description('Item pricing information.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('cost')
                            ->label('Cost')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('handling')
                            ->label('Handling')
                            ->numeric()
                            ->prefix('$'),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('discounted_price')
                            ->label('Discounted Price')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('discount')
                            ->label('Discount Amount')
                            ->numeric(),

                        Forms\Components\Toggle::make('is_discount_percentage')
                            ->label('Discount is Percentage'),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('tally_price')
                            ->label('Tally Price')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\Toggle::make('is_taxable')
                            ->label('Taxable'),
                    ]),
                ]),

            // Inventory Section
            Forms\Components\Section::make('Inventory')
                ->description('Inventory and stock management.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('quantity_available')
                            ->label('Quantity Available')
                            ->numeric(),

                        Forms\Components\TextInput::make('reorder_alert')
                            ->label('Reorder Alert Level')
                            ->numeric(),

                        Forms\Components\Toggle::make('track_inventory')
                            ->label('Track Inventory')
                            ->default(true),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('daily_sales_avg')
                            ->label('Daily Sales Average')
                            ->numeric(),

                        Forms\Components\DatePicker::make('inventory_arrival_date')
                            ->label('Inventory Arrival Date'),

                        Forms\Components\TextInput::make('lead_time')
                            ->label('Lead Time (days)')
                            ->numeric(),
                    ]),

                    Forms\Components\Textarea::make('inventory_notes')
                        ->label('Inventory Notes')
                        ->rows(2)
                        ->maxLength(500),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('purchase_limit')
                            ->label('Purchase Limit')
                            ->numeric(),

                        Forms\Components\Toggle::make('always_show_stock')
                            ->label('Always show item, even if out-of-stock'),
                    ]),
                ]),

            // Shipping Section
            Forms\Components\Section::make('Shipping')
                ->description('Shipping weight and restrictions.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('shipping_weight_lb')
                            ->label('Weight (lb)')
                            ->numeric(),

                        Forms\Components\TextInput::make('shipping_weight_oz')
                            ->label('Weight (oz)')
                            ->numeric(),

                        Forms\Components\Toggle::make('is_free_shipping')
                            ->label('Free Shipping'),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('shipping_restriction_id')
                            ->label('Shipping Restriction ID')
                            ->numeric(),

                        Forms\Components\Textarea::make('shipping_restrictions')
                            ->label('Shipping Restrictions')
                            ->rows(2),
                    ]),
                ]),

            // Identifiers Section
            Forms\Components\Section::make('Identifiers & SKUs')
                ->description('Product identifiers and codes.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('upc')
                            ->label('UPC')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('amazon_sku')
                            ->label('Amazon SKU')
                            ->maxLength(20),
                    ]),
                ]),

            // Flags Section
            Forms\Components\Section::make('Item Flags')
                ->description('Toggle item properties.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),

                        Forms\Components\Toggle::make('is_hidden')
                            ->label('Hidden'),

                        Forms\Components\Toggle::make('is_combo')
                            ->label('Is Combo'),

                        Forms\Components\Toggle::make('is_new')
                            ->label('Is New'),
                    ]),

                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\Toggle::make('has_options')
                            ->label('Has Options'),

                        Forms\Components\Toggle::make('is_checkout_featured')
                            ->label('Checkout Featured'),

                        Forms\Components\Toggle::make('sb_send_as_combo')
                            ->label('SB Send as Combo'),

                        Forms\Components\Toggle::make('shop_skip_processing')
                            ->label('Skip Shop Processing'),
                    ]),
                ]),

            // Images Section
            Forms\Components\Section::make('Legacy Images')
                ->description('Legacy image paths (prefer using media library).')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('image_small')
                            ->label('Small Image Path')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('image_large')
                            ->label('Large Image Path')
                            ->maxLength(50),
                    ]),
                ]),

            // Related Items Section
            Forms\Components\Section::make('Related Items')
                ->description('Link to related items.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('related_item_1_id')
                            ->label('Related Item 1 ID')
                            ->numeric(),

                        Forms\Components\TextInput::make('related_item_2_id')
                            ->label('Related Item 2 ID')
                            ->numeric(),

                        Forms\Components\TextInput::make('related_item_3_id')
                            ->label('Related Item 3 ID')
                            ->numeric(),
                    ]),
                ]),

            // Category and Organization Section
            Forms\Components\Section::make('Organization')
                ->description('Category and ranking.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('category_id')
                            ->label('Category ID')
                            ->numeric(),

                        Forms\Components\TextInput::make('rank')
                            ->label('Rank')
                            ->numeric(),

                        Forms\Components\TextInput::make('keywords')
                            ->label('Keywords')
                            ->maxLength(255),
                    ]),

                    Forms\Components\TextInput::make('size_quantity')
                        ->label('Size/Quantity')
                        ->maxLength(150),
                ]),

            // SEO Meta Fields Section
            Forms\Components\Section::make('SEO Meta Fields')
                ->description('Search engine optimization settings for this product.')
                ->collapsible()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(180),
                    ]),

                    Forms\Components\Textarea::make('meta_keywords')
                        ->label('Meta Keywords')
                        ->rows(2),

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

            // Supplier Section
            Forms\Components\Section::make('Supplier Information')
                ->description('Supplier contact details.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('supplier_company')
                            ->label('Company')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('supplier_contact_name')
                            ->label('Contact Name')
                            ->maxLength(100),
                    ]),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('supplier_phone')
                            ->label('Phone')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('supplier_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('supplier_terms')
                            ->label('Terms')
                            ->maxLength(100),
                    ]),
                ]),

            // External Integrations Section
            Forms\Components\Section::make('External Integrations')
                ->description('Integration IDs for external services.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('infusionsoft_id')
                            ->label('Infusionsoft ID')
                            ->numeric(),

                        Forms\Components\TextInput::make('quickbooks_id')
                            ->label('QuickBooks ID')
                            ->numeric(),

                        Forms\Components\TextInput::make('shop_item_id')
                            ->label('Shop Item ID')
                            ->numeric(),
                    ]),

                    Forms\Components\TextInput::make('shop_variant_id')
                        ->label('Shop Variant ID')
                        ->numeric(),
                ]),

            // Feefo Reviews Section
            Forms\Components\Section::make('Feefo Reviews')
                ->description('Feefo review data.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('feefo_rating')
                            ->label('Rating')
                            ->numeric()
                            ->step(0.1),

                        Forms\Components\TextInput::make('feefo_review_count')
                            ->label('Review Count')
                            ->numeric(),
                    ]),
                ]),

            // Disclaimer Section
            Forms\Components\Section::make('Disclaimer')
                ->description('Product disclaimer information.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\RichEditor::make('disclaimer')
                        ->label('Disclaimer Text'),

                    Forms\Components\Toggle::make('requires_disclaimer_agreement')
                        ->label('Requires Agreement'),
                ]),

            // Resources Section
            Forms\Components\Section::make('Resources')
                ->description('Additional product resources.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Forms\Components\RichEditor::make('resources')
                        ->label('Resources'),
                ]),
        ]);
    }

    public function extendTable(Table $table): Table
    {
        return $table->columns([
            ...$table->getColumns(),
            Tables\Columns\TextColumn::make('title')
                ->label('Title')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('sku')
                ->label('SKU')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('price')
                ->label('Price')
                ->money('USD')
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\IconColumn::make('is_featured')
                ->label('Featured')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\IconColumn::make('is_hidden')
                ->label('Hidden')
                ->boolean()
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
    }
}
