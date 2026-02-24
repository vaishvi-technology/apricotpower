<?php

namespace App\Livewire\Components;

use App\Models\Category;
use Illuminate\View\View;
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
        return Category::orderBy('name')->get();
    }

    /**
     * Get the cart item count.
     */
    public function getCartCountProperty()
    {
        $cart = CartSession::current();

        if (!$cart) {
            return 0;
        }

        return $cart->lines->sum('quantity');
    }

    public function render(): View
    {
        return view('livewire.components.navigation');
    }
}
