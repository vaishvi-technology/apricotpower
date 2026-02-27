<?php

namespace Database\Seeders;

use App\Models\Tag;

class TagSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = $this->getSeedData('tags');

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['value' => $tag->value]);
        }
    }
}
