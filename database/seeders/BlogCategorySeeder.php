<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Apricot Seeds', 'accent_color' => '#e67e22', 'sort_order' => 1],
            ['name' => 'B17', 'accent_color' => '#2ecc71', 'sort_order' => 2],
            ['name' => 'B17 Boosters', 'accent_color' => '#27ae60', 'sort_order' => 3],
            ['name' => 'Health & Wellness', 'accent_color' => '#3498db', 'sort_order' => 4],
            ['name' => 'Healthy Snacks', 'accent_color' => '#1abc9c', 'sort_order' => 5],
            ['name' => 'Interviews', 'accent_color' => '#9b59b6', 'sort_order' => 6],
            ['name' => 'Newsletters', 'accent_color' => '#e74c3c', 'sort_order' => 7],
            ['name' => 'Pet Wellness', 'accent_color' => '#f39c12', 'sort_order' => 8],
            [
                'name' => 'Recipes',
                'accent_color' => '#d35400',
                'sort_order' => 9,
                'children' => [
                    ['name' => 'Baked', 'accent_color' => '#e67e22', 'sort_order' => 1],
                    ['name' => 'Blended', 'accent_color' => '#f1c40f', 'sort_order' => 2],
                    ['name' => 'Creative', 'accent_color' => '#e74c3c', 'sort_order' => 3],
                    ['name' => 'Simple', 'accent_color' => '#2ecc71', 'sort_order' => 4],
                ],
            ],
            ['name' => 'Skin Care', 'accent_color' => '#e91e63', 'sort_order' => 10],
        ];

        foreach ($categories as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $parent = BlogCategory::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug' => Str::slug($data['name']),
                    'is_active' => true,
                ])
            );

            foreach ($children as $childData) {
                BlogCategory::updateOrCreate(
                    ['slug' => Str::slug($childData['name'])],
                    array_merge($childData, [
                        'parent_id' => $parent->id,
                        'slug' => Str::slug($childData['name']),
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
