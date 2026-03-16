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

        @media (max-width: 991px) {
            .cart-summary-card {
                margin-top: 30px;
            }
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
        }
    </style>

    {{-- Cart Content --}}
    <section class="cart-section">
        <div class="container">
            @if($this->cart && $this->cart->lines->count() > 0)
                <div class="row">
                    <div class="col-lg-8">
                        @foreach($this->cart->lines as $line)
                            <div class="cart-item-card" wire:key="cart-line-{{ $line->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="cart-item-image me-3">
                                        @if($line->purchasable->product->thumbnail)
                                            <img src="{{ $line->purchasable->product->thumbnail->getUrl('small') }}"
                                                 alt="{{ $line->purchasable->product->translateAttribute('name') }}">
                                        @endif
                                    </div>
                                    <div class="cart-item-details flex-grow-1">
                                        <div class="cart-item-name">{{ $line->purchasable->product->translateAttribute('name') }}</div>
                                        <div class="cart-item-price">{{ $line->unitPrice->formatted() }} each</div>

                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <div class="cart-qty-group">
                                                <button type="button"
                                                        class="cart-qty-btn"
                                                        wire:click="decrementQuantity({{ $line->id }})"
                                                        wire:loading.attr="disabled"
                                                        {{ $line->quantity <= 1 ? 'disabled' : '' }}>−</button>
                                                <input type="number"
                                                       class="cart-qty-input"
                                                       min="1"
                                                       value="{{ $line->quantity }}"
                                                       wire:change="updateQuantity({{ $line->id }}, $event.target.value)" />
                                                <button type="button"
                                                        class="cart-qty-btn"
                                                        wire:click="incrementQuantity({{ $line->id }})"
                                                        wire:loading.attr="disabled">+</button>
                                            </div>

                                            <button type="button"
                                                    class="cart-remove-btn"
                                                    wire:click="removeLine({{ $line->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:confirm="Remove this item from cart?">
                                                <i class="bi bi-trash"></i> Remove
                                            </button>
                                        </div>

                                        <div class="cart-item-subtotal">{{ $line->subTotal->formatted() }}</div>
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
                            <div class="cart-summary-row total">
                                <span>Total</span>
                                <span>{{ $this->cart->total->formatted() }}</span>
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
