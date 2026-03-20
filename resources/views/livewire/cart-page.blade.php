<div>
    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="container">
            <div class="inner-banner-head">
                <h1><span class="normal-font">Shopping</span> <span class="bold-font">Cart</span></h1>
            </div>
        </div>
    </section>

    <style>
        .cart-section {
            padding: 50px 0;
        }

        .cart-item-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: box-shadow 0.2s;
        }

        .cart-item-card:hover {
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .cart-item-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .cart-item-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .cart-item-price {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
        }

        .cart-item-subtotal {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }

        .cart-qty-group {
            display: inline-flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .cart-qty-group .cart-qty-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: #f8f8f8;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .cart-qty-group .cart-qty-btn:hover {
            background: #e8e8e8;
        }

        .cart-qty-group .cart-qty-btn:disabled {
            background: #f0f0f0;
            color: #bbb;
            cursor: not-allowed;
        }

        .cart-qty-group .cart-qty-btn:first-child {
            border-right: 1px solid #ddd;
        }

        .cart-qty-group .cart-qty-btn:last-child {
            border-left: 1px solid #ddd;
        }

        .cart-qty-group .cart-qty-input {
            width: 45px;
            height: 36px;
            border: none;
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            color: #333;
            background: #fff;
            -moz-appearance: textfield;
        }

        .cart-qty-group .cart-qty-input::-webkit-outer-spin-button,
        .cart-qty-group .cart-qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .cart-qty-group .cart-qty-input:focus {
            outline: none;
        }

        .cart-remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 13px;
            padding: 4px 8px;
            transition: color 0.2s;
        }

        .cart-remove-btn:hover {
            color: #a71d2a;
            text-decoration: underline;
        }

        .cart-summary-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
        }

        .cart-summary-card h5 {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 15px;
            color: #555;
        }

        .cart-summary-row.total {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
            margin-top: 5px;
        }

        .cart-summary-row.discount {
            color: #28a745;
            font-weight: 600;
        }

        .btn-checkout {
            display: block;
            width: 100%;
            padding: 14px;
            background: #fbb700;
            border: none;
            border-radius: 30px;
            color: #333;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 20px;
        }

        .btn-checkout:hover {
            background: #e0a800;
            color: #333;
        }

        .btn-continue-shopping {
            display: block;
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 2px solid #ddd;
            border-radius: 30px;
            color: #555;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }

        .btn-continue-shopping:hover {
            border-color: #333;
            color: #333;
        }

        .cart-empty {
            text-align: center;
            padding: 60px 20px;
        }

        .cart-empty h4 {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .cart-empty p {
            color: #777;
            margin-bottom: 20px;
        }

        .cart-empty .btn-checkout {
            display: inline-block;
            width: auto;
            padding: 14px 40px;
        }

        /* Promo Code Styles */
        .promo-code-section {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        .promo-code-section label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }

        .promo-input-group {
            display: flex;
            gap: 8px;
        }

        .promo-input-group input {
            flex: 1;
            padding: 10px 14px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            text-transform: uppercase;
            transition: border-color 0.2s;
        }

        .promo-input-group input:focus {
            outline: none;
            border-color: #fbb700;
        }

        .promo-input-group button {
            padding: 10px 20px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .promo-input-group button:hover {
            background: #555;
        }

        .promo-input-group button:disabled {
            background: #999;
            cursor: not-allowed;
        }

        .promo-message {
            margin-top: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .promo-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .promo-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .promo-applied {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 8px;
        }

        .promo-applied-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .promo-applied-code {
            font-weight: 700;
            color: #155724;
            font-size: 14px;
            text-transform: uppercase;
        }

        .promo-applied-name {
            font-size: 12px;
            color: #28a745;
        }

        .promo-remove-btn {
            background: none;
            border: none;
            color: #721c24;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 8px;
            transition: color 0.2s;
        }

        .promo-remove-btn:hover {
            color: #a71d2a;
            text-decoration: underline;
        }

        @media (max-width: 991px) {
            .cart-summary-card {
                margin-top: 30px;
            }
        }

        .promo-item-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            margin-left: 8px;
        }

        .promo-item-badge.free {
            background: #d4edda;
            color: #155724;
        }

        .promo-item-badge.discounted {
            background: #fff3cd;
            color: #856404;
        }

        .promo-item-locked-qty {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            background: #f0f0f0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            color: #999;
        }

        .promo-login-note {
            font-size: 13px;
            color: #666;
            margin-top: 8px;
        }

        .promo-login-note a {
            color: #fbb700;
            font-weight: 600;
            text-decoration: none;
        }

        .promo-login-note a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .cart-item-card .d-flex.align-items-center {
                flex-direction: column;
                text-align: center;
            }

            .cart-item-image {
                margin-right: 0 !important;
                margin-bottom: 15px;
            }

            .cart-item-details .d-flex.align-items-center.gap-3 {
                justify-content: center;
            }

            .promo-input-group {
                flex-direction: column;
            }
        }
    </style>

    {{-- Cart Content --}}
    <section class="cart-section">
        <div class="container">
            @if($this->cart && $this->cart->lines->count() > 0)
                <div class="row">
                    <div class="col-lg-8">
                        @foreach($this->cart->lines as $line)
                            @php
                                $lineMeta = $line->meta ?? [];
                                $isPromoItem = !empty($lineMeta['is_promo_item']);
                                $promoLabel = $lineMeta['promo_label'] ?? '';
                                $promoPrice = $lineMeta['promo_price'] ?? null;
                                $isFreePromoItem = $isPromoItem && ($promoPrice === 0 || $promoPrice === '0' || $promoPrice === 0.0);
                            @endphp
                            <div class="cart-item-card" wire:key="cart-line-{{ $line->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="cart-item-image me-3">
                                        @if($line->purchasable->product->thumbnail)
                                            <img src="{{ $line->purchasable->product->thumbnail->getUrl('small') }}"
                                                 alt="{{ $line->purchasable->product->translateAttribute('name') }}">
                                        @endif
                                    </div>
                                    <div class="cart-item-details flex-grow-1">
                                        <div class="cart-item-name">
                                            {{ $line->purchasable->product->translateAttribute('name') }}
                                            @if($isPromoItem)
                                                <span class="promo-item-badge {{ $isFreePromoItem ? 'free' : 'discounted' }}">
                                                    {{ $promoLabel ?: ($isFreePromoItem ? 'FREE PROMO ITEM' : 'PROMO Pricing') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="cart-item-price">
                                            @if($isFreePromoItem)
                                                @if($line->unitPrice)
                                                    <span style="text-decoration: line-through; color: #999;">{{ $line->unitPrice->formatted() }}</span>
                                                @endif
                                                <span style="color: #28a745; font-weight: 700;">FREE</span>
                                            @else
                                                {{ $line->unitPrice ? $line->unitPrice->formatted() : '$0.00' }} each
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            @if($isPromoItem && $isFreePromoItem)
                                                {{-- Free promo items: locked quantity, no remove (mirrors .NET) --}}
                                                <div class="promo-item-locked-qty">
                                                    {{ $line->quantity }}
                                                </div>
                                            @else
                                                <div class="cart-qty-group">
                                                    <button type="button"
                                                            class="cart-qty-btn"
                                                            wire:click="decrementQuantity({{ $line->id }})"
                                                            wire:loading.attr="disabled" wire:target="decrementQuantity({{ $line->id }})"
                                                            {{ $line->quantity <= 1 ? 'disabled' : '' }}>−</button>
                                                    <input type="number"
                                                           class="cart-qty-input"
                                                           min="1"
                                                           value="{{ $line->quantity }}"
                                                           wire:change="updateQuantity({{ $line->id }}, $event.target.value)" />
                                                    <button type="button"
                                                            class="cart-qty-btn"
                                                            wire:click="incrementQuantity({{ $line->id }})"
                                                            wire:loading.attr="disabled" wire:target="incrementQuantity({{ $line->id }})">+</button>
                                                </div>

                                                <button type="button"
                                                        class="cart-remove-btn"
                                                        wire:click="removeLine({{ $line->id }})"
                                                        wire:loading.attr="disabled" wire:target="removeLine({{ $line->id }})"
                                                        wire:confirm="Remove this item from cart?">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="cart-item-subtotal">
                                            @if($isFreePromoItem)
                                                <span style="color: #28a745;">FREE</span>
                                            @else
                                                {{ $line->subTotal ? $line->subTotal->formatted() : '$0.00' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-lg-4">
                        <div class="cart-summary-card">
                            <h5>Order Summary</h5>
                            <div class="cart-summary-row">
                                <span>Subtotal</span>
                                <span>{{ $this->cart->subTotal->formatted() }}</span>
                            </div>

                            {{-- Promo Discount --}}
                            @if($this->promoDiscount > 0)
                                <div class="cart-summary-row discount">
                                    <span>Promo Discount</span>
                                    <span>-${{ number_format($this->promoDiscount, 2) }}</span>
                                </div>
                            @endif

                            {{-- Free Shipping Badge --}}
                            @if($this->promoFreeShipping)
                                <div class="cart-summary-row discount">
                                    <span>Shipping</span>
                                    <span>FREE</span>
                                </div>
                            @endif

                            <div class="cart-summary-row total">
                                <span>Total</span>
                                @if($this->promoDiscount > 0)
                                    @php
                                        $totalInCents = $this->cart->total->value;
                                        $discountInCents = (int) round($this->promoDiscount * 100);
                                        $adjustedTotal = max(0, $totalInCents - $discountInCents);
                                    @endphp
                                    <span>${{ number_format($adjustedTotal / 100, 2) }}</span>
                                @else
                                    <span>{{ $this->cart->total->formatted() }}</span>
                                @endif
                            </div>

                            {{-- Promo Code Section --}}
                            <div class="promo-code-section">
                                <label>Promo Code</label>

                                @if($this->appliedPromo)
                                    <div class="promo-applied">
                                        <div class="promo-applied-info">
                                            <span class="promo-applied-code">{{ $this->appliedPromo->coupon_code }}</span>
                                            <span class="promo-applied-name">{{ $this->appliedPromo->title ?: $this->appliedPromo->name }}</span>
                                        </div>
                                        <button type="button"
                                                class="promo-remove-btn"
                                                wire:click="removePromoCode"
                                                wire:loading.attr="disabled">
                                            <i class="bi bi-x-circle"></i> Remove
                                        </button>
                                    </div>
                                @else
                                    <div class="promo-input-group">
                                        <input type="text"
                                               wire:model="promoCode"
                                               placeholder="Enter promo code"
                                               wire:keydown.enter="applyPromoCode"
                                               wire:loading.attr="disabled"
                                               @guest('customer') disabled @endguest />
                                        <button type="button"
                                                wire:click="applyPromoCode"
                                                wire:loading.attr="disabled"
                                                @guest('customer') disabled @endguest>
                                            <span wire:loading.remove wire:target="applyPromoCode">Apply</span>
                                            <span wire:loading wire:target="applyPromoCode">Applying...</span>
                                        </button>
                                    </div>
                                    @guest('customer')
                                        <p class="promo-login-note">
                                            Please <a href="{{ route('login') }}">create an account or log in</a> to use a promo code.
                                        </p>
                                    @endguest
                                @endif

                                @if($promoMessage)
                                    <div class="promo-message {{ $promoMessageType }}">
                                        {{ $promoMessage }}
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('checkout.view') }}" class="btn-checkout">
                                Proceed to Checkout
                            </a>
                            <a href="{{ route('store') }}" class="btn-continue-shopping">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="cart-empty">
                    <h4>Your cart is empty</h4>
                    <p>Add some products to your cart to continue shopping.</p>
                    <a href="{{ route('store') }}" class="btn-checkout">Continue Shopping</a>
                </div>
            @endif
        </div>
    </section>
</div>
