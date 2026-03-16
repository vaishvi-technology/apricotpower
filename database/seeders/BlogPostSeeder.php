<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = json_decode(
            file_get_contents(database_path('seeders/data/blog_posts.json')),
            true
        );

        foreach ($posts as $data) {
            $categoryIds = $data['category_ids'] ?? [];
            $tagIds = $data['tag_ids'] ?? [];
            unset($data['category_ids'], $data['tag_ids'], $data['featured_image']);

            $post = BlogPost::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            $post->categories()->syncWithoutDetaching($categoryIds);
            $post->tags()->syncWithoutDetaching($tagIds);
        }
    }
}
