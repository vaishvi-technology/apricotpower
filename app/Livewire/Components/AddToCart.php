<?php

namespace App\Livewire\Components;

use App\Events\Klaviyo\AddedToCart as KlaviyoAddedToCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Base\Purchasable;
use Lunar\Facades\CartSession;

class AddToCart extends Component
{
    /**
     * The purchasable model we want to add to the cart.
     */
    public ?Purchasable $purchasable = null;

    /**
     * The quantity to add to cart.
     */
    public int $quantity = 1;

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:1|max:10000',
        ];
    }

    public function incrementQuantity(): void
    {
        $this->quantity++;
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        $this->validate();

        if ($this->purchasable->stock < $this->quantity) {
            $this->addError('quantity', 'The quantity exceeds the available stock.');

            return;
        }

        CartSession::manager()->add($this->purchasable, $this->quantity);
        $this->dispatch('add-to-cart');

        $customer = Auth::guard('customer')->user();
        if ($customer?->email) {
            $price = $this->purchasable->basePrices->first()?->price?->value;
            KlaviyoAddedToCart::dispatch(
                $customer->email,
                $this->purchasable->product?->translateAttribute('name') ?? $this->purchasable->getDescription(),
                $this->quantity,
                $price ? $price / 100 : null,
                $this->purchasable->id,
            );
        }
    }

    public function buyNow(): void
    {
        $this->validate();

        if ($this->purchasable->stock < $this->quantity) {
            $this->addError('quantity', 'The quantity exceeds the available stock.');

            return;
        }

        CartSession::manager()->add($this->purchasable, $this->quantity);
        $this->dispatch('add-to-cart');
        $this->redirect(route('checkout.view'));
    }

    public function render(): View
    {
        return view('livewire.components.add-to-cart');
    }
}
