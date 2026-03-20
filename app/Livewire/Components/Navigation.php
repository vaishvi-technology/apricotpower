<?php

namespace App\Livewire\Components;

use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Collection;

class Navigation extends Component
{
    /**
     * The search term for the search input.
     *
     * @var string
     */
    public $term = null;

    /**
     * {@inheritDoc}
     */
    protected $queryString = [
        'term',
    ];

    /**
     * Return the collections in a tree.
     */
    public function getCollectionsProperty()
    {
        return Collection::with(['defaultUrl'])->get()->toTree();
    }

    /**
     * Return all categories for navigation.
     */
    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Refresh the component when an item is added to cart.
     */
    #[On('add-to-cart')]
    #[On('cart-updated')]
    public function refreshCart(): void
    {
        // Re-renders the component, which recalculates cartCount
    }

    /**
     * Get the cart item count.
     */
    public function getCartCountProperty()
    {
        try {
            $cart = CartSession::current();

            if (!$cart || !$cart->lines) {
                return 0;
            }

            return $cart->lines->sum('quantity');
        } catch (\Exception $e) {
            // Handle case where Lunar defaults (Channel/Currency) don't exist
            return 0;
        }
    }

    /**
     * Return nav-featured blog posts for the nav dropdown.
     */
    public function getNavFeaturedBlogsProperty()
    {
        return BlogPost::published()->navFeatured()->with('categories')->orderByDesc('published_at')->limit(5)->get();
    }

    public function render(): View
    {
        return view('livewire.components.navigation');
    }
}
