<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Lunar\Facades\CartSession;

/**
 * Handles promo code redemption from external URLs.
 * Mirrors .NET /redeem-promo.asp?promo=CODE
 */
class PromoRedeemController extends Controller
{
    public function redeem(Request $request)
    {
        $promoCode = $request->query('promo', '');

        if (empty($promoCode)) {
            return redirect('/');
        }

        // Look up the promo to check for a landing URL
        $promo = Promo::where(\Illuminate\Support\Facades\DB::raw('UPPER(coupon_code)'), strtoupper(trim($promoCode)))->first();

        $cart = CartSession::current();

        if ($cart) {
            $promoService = app(PromoService::class);
            $promoService->applyByCouponCode($cart, $promoCode);
        }

        // Store promo code in session so cart page can pick it up
        session()->flash('pending_promo_code', strtoupper(trim($promoCode)));

        // Redirect to landing URL if set, otherwise default to cart page
        if ($promo && !empty($promo->landing_url)) {
            return redirect($promo->landing_url);
        }

        return redirect()->route('cart.view');
    }
}
