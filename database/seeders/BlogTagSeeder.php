<?php

namespace Database\Seeders;

use App\Models\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Apricot Seeds', 'sort_order' => 1],
            ['name' => 'B17', 'sort_order' => 2],
            ['name' => 'B17 Boosters', 'sort_order' => 3],
            ['name' => 'Health & Wellness', 'sort_order' => 4],
            ['name' => 'Healthy Snacks', 'sort_order' => 5],
            ['name' => 'Interviews', 'sort_order' => 6],
            ['name' => 'Newsletters', 'sort_order' => 7],
            ['name' => 'Pet Wellness', 'sort_order' => 8],
            ['name' => 'Recipes', 'sort_order' => 9],
            ['name' => 'Baked', 'sort_order' => 10],
            ['name' => 'Blended', 'sort_order' => 11],
            ['name' => 'Creative', 'sort_order' => 12],
            ['name' => 'Simple', 'sort_order' => 13],
            ['name' => 'Skin Care', 'sort_order' => 14],
        ];

        foreach ($tags as $data) {
            BlogTag::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug' => Str::slug($data['name']),
                    'is_active' => true,
                ])
            );
        }
    }
}
