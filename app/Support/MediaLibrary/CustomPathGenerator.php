<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /**
     * Get the base path based on the model type.
     */
    protected function getBasePath(Media $media): string
    {
        $modelType = $this->getModelFolder($media->model_type);

        return "{$modelType}/{$media->id}";
    }

    /**
     * Map model class/morph alias to folder name.
     */
    protected function getModelFolder(string $modelType): string
    {
        // Handle both morph aliases (e.g., 'product') and full class names
        $mapping = [
            // Morph aliases (used by Lunar)
            'product' => 'products',
            'collection' => 'collections',
            'brand' => 'brands',
            'category' => 'categories',
            // Full class names (fallback)
            'Lunar\Models\Product' => 'products',
            'App\Models\Product' => 'products',
            'Lunar\Models\Collection' => 'collections',
            'App\Models\Collection' => 'collections',
            'Lunar\Models\Brand' => 'brands',
            'App\Models\Brand' => 'brands',
            'App\Models\Category' => 'categories',
        ];

        return $mapping[$modelType] ?? 'media';
    }
}
