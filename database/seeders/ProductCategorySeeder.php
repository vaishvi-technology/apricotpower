<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Attribute;
use Lunar\Models\Brand;
use Lunar\Models\Currency;
use Lunar\Models\Language;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\Price;
use Lunar\Models\TaxClass;
use Lunar\Models\Url;

class ProductCategorySeeder extends AbstractSeeder
{
    /**
     * Source directory for product images
     */
    protected string $sourceDirectory = 'images/products';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = $this->getSeedData('products-with-categories');

        $attributes = Attribute::get();
        $productType = ProductType::first();
        $taxClass = TaxClass::whereDefault(true)->first();
        $currency = Currency::whereDefault(true)->first();
        $tags = Tag::all();

        DB::transaction(function () use ($products, $attributes, $productType, $taxClass, $currency, $tags) {
            foreach ($products as $product) {
                $this->createProduct($product, $attributes, $productType, $taxClass, $currency, $tags);
            }
        });
    }

    /**
     * Create a product with image, category, and random tags
     */
    protected function createProduct(
        object $product,
        $attributes,
        ProductType $productType,
        TaxClass $taxClass,
        Currency $currency,
        $tags
    ): ?Product {
        // Check if product with this SKU already exists
        $existingVariant = ProductVariant::where('sku', $product->sku)->first();
        if ($existingVariant) {
            return $existingVariant->product;
        }

        // Build attribute data - always set name and description as TranslatedText
        $attributeData = [];
        foreach ($product->attributes as $attributeHandle => $value) {
            // Always create TranslatedText for name and description
            $attributeData[$attributeHandle] = new TranslatedText([
                'en' => new Text($value),
            ]);
        }

        // Find or create brand
        $brand = Brand::firstOrCreate([
            'name' => $product->brand,
        ]);

        // Find category by slug
        $category = null;
        if (!empty($product->category_slug)) {
            $category = Category::where('slug', $product->category_slug)->first();
        }

        // Create the product
        $productModel = Product::create([
            'attribute_data' => $attributeData,
            'product_type_id' => $productType->id,
            'status' => 'published',
            'brand_id' => $brand->id,
            'category_id' => $category?->id,
        ]);

        // Create URL for the product
        $language = Language::whereDefault(true)->first();
        $productName = $product->attributes->name ?? 'product-' . $productModel->id;
        $slug = Str::slug($productName);

        // Make slug unique
        $originalSlug = $slug;
        $counter = 1;
        while (Url::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Url::create([
            'slug' => $slug,
            'default' => true,
            'language_id' => $language->id,
            'element_type' => $productModel->getMorphClass(),
            'element_id' => $productModel->id,
        ]);

        // Create default variant
        $variant = ProductVariant::create([
            'product_id' => $productModel->id,
            'purchasable' => 'always',
            'shippable' => true,
            'backorder' => 0,
            'sku' => $product->sku,
            'tax_class_id' => $taxClass->id,
            'stock' => 500,
        ]);

        // Create price
        Price::create([
            'customer_group_id' => null,
            'currency_id' => $currency->id,
            'priceable_type' => (new ProductVariant)->getMorphClass(),
            'priceable_id' => $variant->id,
            'price' => $product->price,
            'min_quantity' => 1,
        ]);

        // Handle image using Spatie Media Library
        if (!empty($product->image)) {
            $imagePath = public_path("{$this->sourceDirectory}/{$product->image}");

            if (file_exists($imagePath)) {
                $media = $productModel->addMedia($imagePath)
                    ->preservingOriginal()
                    ->toMediaCollection('images');

                $media->setCustomProperty('primary', true);
                $media->save();
            }
        }

        // Assign random tags (1-3 tags per product)
        if ($tags->isNotEmpty()) {
            $randomTags = $tags->random(min(rand(1, 3), $tags->count()));
            $productModel->tags()->sync($randomTags->pluck('id'));
        }

        return $productModel;
    }
}
