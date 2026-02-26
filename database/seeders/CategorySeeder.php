<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends AbstractSeeder
{
    /**
     * The S3 disk instance
     */
    protected $disk;

    /**
     * Source directory for category images
     */
    protected string $sourceDirectory = 'images/categories';

    /**
     * Target directory in S3
     */
    protected string $targetDirectory = 'categories';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = $this->getSeedData('categories');

        $this->disk = Storage::disk('s3');

        DB::transaction(function () use ($categories) {
            foreach ($categories as $category) {
                $this->createCategory($category);
            }
        });
    }

    /**
     * Create a category and optionally its children
     */
    protected function createCategory(object $category, ?int $parentId = null): Category
    {
        $slug = $category->slug ?? Str::slug($category->name);

        // Check if category already exists
        $existingCategory = Category::where('slug', $slug)->first();
        if ($existingCategory) {
            // Still process children for existing category
            if (!empty($category->children)) {
                foreach ($category->children as $child) {
                    $this->createCategory($child, $existingCategory->id);
                }
            }
            return $existingCategory;
        }

        $imagePath = null;
        if (!empty($category->image)) {
            $imagePath = $this->uploadImageToS3($category->image);
        }

        $categoryModel = Category::create([
            'parent_id' => $parentId,
            'name' => $category->name,
            'slug' => $slug,
            'description' => $category->description ?? null,
            'image' => $imagePath,
            'meta_title' => $category->meta_title ?? null,
            'meta_description' => $category->meta_description ?? null,
            'is_active' => $category->is_active ?? true,
            'show_in_menu' => $category->show_in_menu ?? true,
            'sort_order' => $category->sort_order ?? 0,
        ]);

        // Handle nested children recursively
        if (!empty($category->children)) {
            foreach ($category->children as $child) {
                $this->createCategory($child, $categoryModel->id);
            }
        }

        return $categoryModel;
    }

    /**
     * Upload an image to S3 and return the stored path
     */
    protected function uploadImageToS3(string $filename): string
    {
        $sourcePath = public_path("{$this->sourceDirectory}/{$filename}");

        if (!file_exists($sourcePath)) {
            throw new \RuntimeException("Image file not found: {$sourcePath}");
        }

        $targetPath = "{$this->targetDirectory}/{$filename}";

        $this->disk->put(
            $targetPath,
            file_get_contents($sourcePath),
            'public'
        );

        return $targetPath;
    }
}
