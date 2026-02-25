<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\ProductVariant;

class StorePage extends Component
{
    use WithPagination;

    public string $sortBy = 'default';
    public string $status = 'published';
    public array $selectedCategories = [];
    public array $selectedTags = [];
    public string $searchQuery = '';
    public int $perPage = 12;

    protected $queryString = [
        'sortBy' => ['except' => 'default'],
        'status' => ['except' => 'published'],
        'selectedCategories' => ['except' => [], 'as' => 'categories'],
        'selectedTags' => ['except' => [], 'as' => 'tags'],
        'searchQuery' => ['except' => '', 'as' => 'q'],
    ];

    /**
     * Get all categories for filtering.
     */
    public function getCategoriesProperty(): Collection
    {
        return Category::withCount('products')
            ->where('is_active', true)
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get all tags for filtering.
     */
    public function getTagsProperty(): Collection
    {
        return Tag::withCount('products')
            ->having('products_count', '>', 0)
            ->orderBy('value')
            ->get();
    }

    /**
     * Get filtered and sorted products.
     */
    public function getProductsProperty(): LengthAwarePaginator
    {
        $query = Product::with([
            'defaultUrl',
            'thumbnail',
            'variants.basePrices.currency',
            'category',
            'tags',
        ]);

        // Filter by status
        if ($this->status === 'published') {
            $query->where('status', 'published');
        } elseif ($this->status === 'draft') {
            $query->where('status', 'draft');
        }

        // Filter by search query
        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('attribute_data->name->value', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('attribute_data->description->value', 'like', '%' . $this->searchQuery . '%');
            });
        }

        // Filter by selected categories
        if (!empty($this->selectedCategories)) {
            $query->whereIn('category_id', $this->selectedCategories);
        }

        // Filter by selected tags
        if (!empty($this->selectedTags)) {
            $query->whereHas('tags', function ($q) {
                $q->whereIn('tags.id', $this->selectedTags);
            });
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'price_high':
                $query->orderByDesc(
                    \Lunar\Models\Price::select('price')
                        ->whereColumn('priceable_id', 'lunar_product_variants.id')
                        ->where('priceable_type', ProductVariant::class)
                        ->limit(1)
                );
                break;

            case 'price_low':
                $query->orderBy(
                    \Lunar\Models\Price::select('price')
                        ->whereColumn('priceable_id', 'lunar_product_variants.id')
                        ->where('priceable_type', ProductVariant::class)
                        ->limit(1)
                );
                break;

            case 'name_asc':
                $query->orderBy('attribute_data->name->value');
                break;

            case 'name_desc':
                $query->orderByDesc('attribute_data->name->value');
                break;

            case 'newest':
                $query->orderByDesc('created_at');
                break;

            case 'best_selling':
                $query->orderByDesc('created_at');
                break;

            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Get active filter count.
     */
    public function getActiveFilterCountProperty(): int
    {
        $count = 0;
        if (!empty($this->selectedCategories)) $count += count($this->selectedCategories);
        if (!empty($this->selectedTags)) $count += count($this->selectedTags);
        if (!empty($this->searchQuery)) $count++;
        return $count;
    }

    /**
     * Toggle category filter.
     */
    public function toggleCategory(int $categoryId): void
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            $this->selectedCategories = array_values(array_diff($this->selectedCategories, [$categoryId]));
        } else {
            $this->selectedCategories[] = $categoryId;
        }
        $this->resetPage();
    }

    /**
     * Toggle tag filter.
     */
    public function toggleTag(int $tagId): void
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_values(array_diff($this->selectedTags, [$tagId]));
        } else {
            $this->selectedTags[] = $tagId;
        }
        $this->resetPage();
    }

    /**
     * Set status filter.
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    /**
     * Clear all filters.
     */
    public function clearFilters(): void
    {
        $this->selectedCategories = [];
        $this->selectedTags = [];
        $this->searchQuery = '';
        $this->sortBy = 'default';
        $this->resetPage();
    }

    /**
     * Clear category filters.
     */
    public function clearCategoryFilters(): void
    {
        $this->selectedCategories = [];
        $this->resetPage();
    }

    public function clearTagFilters(): void
    {
        $this->selectedTags = [];
        $this->resetPage();
    }

    /**
     * Add product to cart.
     */
    public function addToCart(int $variantId, int $quantity = 1): void
    {
        $cart = CartSession::current();

        if (!$cart) {
            $cart = Cart::create([
                'currency_id' => Currency::getDefault()->id,
                'channel_id' => Channel::getDefault()->id,
            ]);
            CartSession::use($cart);
        }

        $cart->add(ProductVariant::find($variantId), $quantity);

        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Product added to cart!', type: 'success');
    }

    /**
     * Add to cart and redirect to cart page.
     */
    public function buyNow(int $variantId, int $quantity = 1): void
    {
        $this->addToCart($variantId, $quantity);
        $this->redirect(route('cart.view'));
    }

    public function render(): View
    {
        return view('livewire.store-page')
            ->layout('layouts.storefront');
    }
}
