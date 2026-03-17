<?php

namespace App\Livewire;

use App\Models\Promo;
use App\Services\PromoService;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;

class CartPage extends Component
{
    public string $promoCode = '';
    public string $promoMessage = '';
    public string $promoMessageType = ''; // 'success' or 'error'
    public bool $denyAutoPromo = false;

    public function mount(): void
    {
        $cart = CartSession::current();
        if (!$cart) {
            return;
        }

        $promoService = app(PromoService::class);

        // Re-verify existing promo on page load (mirrors .NET Promo_Verify)
        if ($cart->promo_id) {
            $stillValid = $promoService->verify($cart);
            if (!$stillValid) {
                $promo = Promo::find($cart->promo_id);
                // If it was an auto-promo, silently remove (no error message)
                if (!$promo || !$promo->is_auto) {
                    $this->promoMessage = 'Your promo code is no longer valid and has been removed.';
                    $this->promoMessageType = 'error';
                }
            }
        }

        // Auto-apply promos if no promo active and not denied
        // Mirrors .NET: if no ActivePromoID and DenyAutoPromo != True, call Promo_AutoApply
        if (!$cart->promo_id && !$this->denyAutoPromo) {
            $promoService->autoApply($cart);
        }
    }

    public function getCartProperty()
    {
        $cart = CartSession::current();

        return $cart?->calculate();
    }

    public function applyPromoCode(): void
    {
        $cart = CartSession::current();

        if (!$cart) {
            $this->promoMessage = 'Your cart is empty.';
            $this->promoMessageType = 'error';
            return;
        }

        // Check if customer is logged in (mirrors .NET: disabled input for non-signed-in users)
        if (!$this->isCustomerLoggedIn()) {
            $this->promoMessage = 'Please log in or create an account to use a promo code.';
            $this->promoMessageType = 'error';
            return;
        }

        $promoService = app(PromoService::class);
        $result = $promoService->applyByCouponCode($cart, $this->promoCode);

        $this->promoMessage = $result['message'];
        $this->promoMessageType = $result['success'] ? 'success' : 'error';

        if ($result['success']) {
            $this->promoCode = '';
            $this->denyAutoPromo = false;
            $this->dispatch('cart-updated');
        }
    }

    public function removePromoCode(): void
    {
        $cart = CartSession::current();

        if (!$cart) {
            return;
        }

        // Check if current promo is auto-apply, set deny flag (mirrors .NET DenyAutoPromo cookie)
        if ($cart->promo_id) {
            $promo = Promo::find($cart->promo_id);
            if ($promo && $promo->is_auto) {
                $this->denyAutoPromo = true;
            }
        }

        $promoService = app(PromoService::class);
        $promoService->unapply($cart);

        $this->promoMessage = 'Promo code removed.';
        $this->promoMessageType = 'success';
        $this->promoCode = '';
        $this->dispatch('cart-updated');
    }

    public function getAppliedPromoProperty(): ?Promo
    {
        $cart = CartSession::current();

        if (!$cart || !$cart->promo_id) {
            return null;
        }

        return Promo::find($cart->promo_id);
    }

    public function getPromoDiscountProperty(): float
    {
        $cart = CartSession::current();

        return (float) ($cart?->promo_discount ?? 0);
    }

    public function getPromoFreeShippingProperty(): bool
    {
        $cart = CartSession::current();

        return (bool) ($cart?->promo_free_shipping ?? false);
    }

    /**
     * Check if a customer is currently logged in.
     */
    protected function isCustomerLoggedIn(): bool
    {
        return auth()->guard('customer')->check();
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
            $this->reverifyPromo();
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
            $this->reverifyPromo();
            $this->dispatch('cart-updated');
        }
    }

    public function updateQuantity(int $lineId, int $quantity): void
    {
        if ($quantity < 1) $quantity = 1;

        CartSession::updateLines(collect([
            ['id' => $lineId, 'quantity' => $quantity],
        ]));
        $this->reverifyPromo();
        $this->dispatch('cart-updated');
    }

    public function removeLine(int $lineId): void
    {
        CartSession::remove($lineId);
        $this->reverifyPromo();
        $this->dispatch('cart-updated');
    }

    /**
     * Re-verify promo after cart changes.
     */
    protected function reverifyPromo(): void
    {
        $cart = CartSession::current();

        if ($cart && $cart->promo_id) {
            $promoService = app(PromoService::class);
            $stillValid = $promoService->verify($cart);

            if (!$stillValid) {
                $this->promoMessage = 'Your promo code is no longer valid for the updated cart and has been removed.';
                $this->promoMessageType = 'error';
            }
        }
    }

    public function render(): View
    {
        return view('livewire.cart-page')
            ->layout('layouts.storefront');
    }
}
