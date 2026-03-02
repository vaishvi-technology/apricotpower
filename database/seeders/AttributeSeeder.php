<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Lunar\Models\Attribute;
use Lunar\Models\AttributeGroup;
use Lunar\Models\ProductType;

class AttributeSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     *
     */
    public function run(): void
    {
        $attributes = $this->getSeedData('attributes');

        $attributeGroup = AttributeGroup::first();
        $productType = ProductType::first();

        DB::transaction(function () use ($attributes, $attributeGroup, $productType) {
            $createdAttributeIds = [];

            foreach ($attributes as $attribute) {
                $created = Attribute::firstOrCreate(
                    [
                        'handle' => $attribute->handle,
                        'attribute_group_id' => $attributeGroup->id,
                    ],
                    [
                        'attribute_type' => $attribute->attribute_type,
                        'section' => 'main',
                        'type' => $attribute->type,
                        'required' => $attribute->handle === 'name',
                        'searchable' => true,
                        'filterable' => false,
                        'system' => false,
                        'position' => $attributeGroup->attributes()->count() + 1,
                        'name' => [
                            'en' => $attribute->name,
                        ],
                        'description' => [
                            'en' => $attribute->name,
                        ],
                        'configuration' => (array) $attribute->configuration,
                    ]
                );
                $createdAttributeIds[] = $created->id;
            }

            // Associate attributes with the ProductType
            if ($productType) {
                $productType->mappedAttributes()->syncWithoutDetaching($createdAttributeIds);
            }
        });
    }
}
