<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;

class CartPage extends Component
{
    public function getCartProperty()
    {
        return CartSession::current();
    }

    public function render(): View
    {
        return view('livewire.cart-page')
            ->layout('layouts.storefront');
    }
}
