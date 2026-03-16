<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        $productType = ProductType::first();
        $taxClass = TaxClass::whereDefault(true)->first();
        $currency = Currency::whereDefault(true)->first();
        $tags = Tag::all();

        DB::transaction(function () use ($products, $productType, $taxClass, $currency, $tags) {
            foreach ($products as $product) {
                $this->createProduct($product, $productType, $taxClass, $currency, $tags);
            }
        });
    }

    /**
     * Create a product with image, category, and random tags
     */
    protected function createProduct(
        object $product,
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

        // Find or create brand
        $brand = Brand::firstOrCreate([
            'name' => $product->brand,
        ]);

        // Create the product with direct column fields
        $productModel = Product::create([
            'name' => $product->attributes->name ?? null,
            'description' => $product->attributes->description ?? null,
            'attribute_data' => [], // Required by Lunar, but we use direct columns
            'product_type_id' => $productType->id,
            'status' => 'published',
            'brand_id' => $brand->id,
        ]);

        // Sync categories (many-to-many)
        if (!empty($product->category_slugs)) {
            $categorySlugs = is_array($product->category_slugs) ? $product->category_slugs : [$product->category_slugs];
            $categoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id');
            $productModel->categories()->sync($categoryIds);
        } elseif (!empty($product->category_slug)) {
            // Backwards compatibility with single category_slug
            $category = Category::where('slug', $product->category_slug)->first();
            if ($category) {
                $productModel->categories()->sync([$category->id]);
            }
        }

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
