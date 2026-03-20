<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;

class CartPage extends Component
{
    public function getCartProperty()
    {
        $cart = CartSession::current();

        if ($cart) {
            $cart->calculate();
        }

        return $cart;
    }

    public function render(): View
    {
        return view('livewire.cart-page')
            ->layout('layouts.storefront');
    }
}
