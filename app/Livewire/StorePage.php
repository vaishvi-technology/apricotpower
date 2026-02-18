<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\Collection as LunarCollection;
class StorePage extends Component
{
    public string $sortBy = '';

    /**
     * Get products grouped by collection (category).
     */
    public function getCollectionsWithProductsProperty(): Collection
    {
        return LunarCollection::with([
            'products' => function ($query) {
                $query->with([
                    'defaultUrl',
                    'thumbnail',
                    'variants.basePrices.currency',
                ]);

                // Apply sorting
                if ($this->sortBy === 'highest_price') {
                    $query->orderByDesc(
                        \Lunar\Models\Price::select('price')
                            ->whereColumn('priceable_id', 'lunar_product_variants.id')
                            ->where('priceable_type', \Lunar\Models\ProductVariant::class)
                            ->limit(1)
                    );
                } elseif ($this->sortBy === 'lowest_price') {
                    $query->orderBy(
                        \Lunar\Models\Price::select('price')
                            ->whereColumn('priceable_id', 'lunar_product_variants.id')
                            ->where('priceable_type', \Lunar\Models\ProductVariant::class)
                            ->limit(1)
                    );
                }
            },
            'defaultUrl',
        ])
            ->whereHas('products')
            ->get();
    }

    /**
     * Add product to cart.
     */
    public function addToCart(int $variantId, int $quantity = 1): void
    {
        $cart = CartSession::current();

        if (!$cart) {
            $cart = Cart::create([
                'currency_id' => \Lunar\Models\Currency::getDefault()->id,
                'channel_id' => \Lunar\Models\Channel::getDefault()->id,
            ]);
            CartSession::use($cart);
        }

        $cart->add(
            \Lunar\Models\ProductVariant::find($variantId),
            $quantity
        );

        $this->dispatch('cart-updated');
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
