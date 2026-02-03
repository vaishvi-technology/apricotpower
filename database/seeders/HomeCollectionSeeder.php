<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Product;

class HomeCollectionSeeder extends Seeder
{
    public function run(): void
    {
        $collectionGroup = CollectionGroup::first();

        if (! $collectionGroup) {
            $this->command->warn('No CollectionGroup found. Please run the base seeders first.');
            return;
        }

        $definitions = [
            ['name' => 'Banner', 'description' => 'Products displayed in the home page banner carousel'],
            ['name' => 'Featured', 'description' => 'Featured products shown in the Our Products section'],
            ['name' => 'Hot Buys', 'description' => 'Hot buy deals shown on the home page'],
        ];

        DB::transaction(function () use ($definitions, $collectionGroup) {
            foreach ($definitions as $def) {
                $collection = Collection::create([
                    'collection_group_id' => $collectionGroup->id,
                    'attribute_data' => [
                        'name' => new TranslatedText([
                            'en' => new Text($def['name']),
                        ]),
                        'description' => new TranslatedText([
                            'en' => new Text($def['description']),
                        ]),
                    ],
                ]);

                $this->command->info("Created collection: {$def['name']} (slug will be auto-generated)");
            }

            // Attach existing products to all three collections so the page isn't empty
            $products = Product::take(6)->get();

            if ($products->isEmpty()) {
                $this->command->warn('No products found to attach to collections.');
                return;
            }

            $collections = Collection::whereHas('defaultUrl', function ($query) {
                $query->whereIn('slug', ['banner', 'featured', 'hot-buys']);
            })->get();

            foreach ($collections as $collection) {
                $collection->products()->sync(
                    $products->mapWithKeys(fn ($product, $index) => [
                        $product->id => ['position' => $index + 1],
                    ])->toArray()
                );

                $this->command->info("Attached {$products->count()} products to '{$collection->translateAttribute('name')}'");
            }
        });
    }
}
