<?php

namespace App\Livewire;

use App\Traits\FetchesUrls;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Price;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductPage extends Component
{
    use FetchesUrls;

    /**
     * The selected option values.
     */
    public array $selectedOptionValues = [];

    /**
     * The currently selected image ID for the gallery.
     */
    public ?int $selectedImageId = null;

    public function mount($slug): void
    {
        $this->url = $this->fetchUrl(
            $slug,
            (new Product)->getMorphClass(),
            [
                'element.media',
                'element.variants.basePrices.currency',
                'element.variants.basePrices.priceable',
                'element.variants.values.option',
                'element.collections.defaultUrl',
                'element.tags',
                'element.productBadges',
                'element.associations.target.thumbnail',
                'element.associations.target.defaultUrl',
                'element.associations.target.variants.basePrices.currency',
            ]
        );

        if (! $this->url) {
            abort(404);
        }

        $this->selectedOptionValues = $this->productOptions->mapWithKeys(function ($data) {
            return [$data['option']->id => $data['values']->first()->id];
        })->toArray();

        // Initialize selectedImageId to the primary/first image
        $this->selectedImageId = $this->image?->id;
    }

    /**
     * Select an image by its media ID.
     */
    public function selectImage(int $mediaId): void
    {
        $this->selectedImageId = $mediaId;
    }

    /**
     * Computed property to return the currently selected image.
     */
    public function getSelectedImageProperty(): ?Media
    {
        if ($this->selectedImageId) {
            $selected = $this->images->first(fn ($media) => $media->id === $this->selectedImageId);
            if ($selected) {
                return $selected;
            }
        }
        return $this->image; // Fall back to default image logic
    }

    /**
     * Computed property to get variant.
     */
    public function getVariantProperty(): ProductVariant
    {
        return $this->product->variants->first(function ($variant) {
            return ! $variant->values->pluck('id')
                ->diff(
                    collect($this->selectedOptionValues)->values()
                )->count();
        });
    }

    /**
     * Computed property to return all available option values.
     */
    public function getProductOptionValuesProperty(): Collection
    {
        return $this->product->variants->pluck('values')->flatten();
    }

    /**
     * Computed propert to get available product options with values.
     */
    public function getProductOptionsProperty(): Collection
    {
        return $this->productOptionValues->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
    }

    /**
     * Computed property to return product.
     */
    public function getProductProperty(): Product
    {
        return $this->url->element;
    }

    /**
     * Return all images for the product.
     */
    public function getImagesProperty(): Collection
    {
        return $this->product->media->sortBy('order_column');
    }

    /**
     * Computed property to return current image.
     */
    public function getImageProperty(): ?Media
    {
        if (count($this->variant->images)) {
            return $this->variant->images->first();
        }

        if ($primary = $this->images->first(fn ($media) => $media->getCustomProperty('primary'))) {
            return $primary;
        }

        return $this->images->first();
    }

    /**
     * Computed property to get the base price for the current variant.
     */
    public function getBasePriceProperty(): ?Price
    {
        return $this->variant->basePrices->first();
    }

    /**
     * Computed property to get related (cross-sell) products.
     */
    public function getRelatedProductsProperty(): Collection
    {
        return $this->product->associations
            ->where('type', 'cross-sell')
            ->map(fn ($assoc) => $assoc->target)
            ->filter();
    }

    /**
     * Computed property to get alternate products.
     */
    public function getAlternateProductsProperty(): Collection
    {
        return $this->product->associations
            ->where('type', 'alternate')
            ->map(fn ($assoc) => $assoc->target)
            ->filter();
    }

    public function render(): View
    {
        return view('livewire.product-page');
    }
}
