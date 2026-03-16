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

        return $cart?->calculate();
    }

    public function incrementQuantity(int $lineId): void
    {
        $cart = $this->cart;
        if (!$cart) return;

        $line = $cart->lines->first(fn ($l) => $l->id === $lineId);
        if ($line) {
            CartSession::updateLines(collect([
                ['id' => $lineId, 'quantity' => $line->quantity + 1],
            ]));
            $this->dispatch('cart-updated');
        }
    }

    public function decrementQuantity(int $lineId): void
    {
        $cart = $this->cart;
        if (!$cart) return;

        $line = $cart->lines->first(fn ($l) => $l->id === $lineId);
        if ($line && $line->quantity > 1) {
            CartSession::updateLines(collect([
                ['id' => $lineId, 'quantity' => $line->quantity - 1],
            ]));
            $this->dispatch('cart-updated');
        }
    }

    public function updateQuantity(int $lineId, int $quantity): void
    {
        if ($quantity < 1) $quantity = 1;

        CartSession::updateLines(collect([
            ['id' => $lineId, 'quantity' => $quantity],
        ]));
        $this->dispatch('cart-updated');
    }

    public function removeLine(int $lineId): void
    {
        CartSession::remove($lineId);
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.cart-page')
            ->layout('layouts.storefront');
    }
}
