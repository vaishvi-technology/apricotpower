<div>
    <style>
        .add-to-cart-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .quantity-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quantity-label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .quantity-input-group {
            display: inline-flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .quantity-input-group .qty-btn {
            width: 45px;
            height: 45px;
            border: none;
            background: #f8f8f8;
            font-size: 20px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .quantity-input-group .qty-btn:hover {
            background: #e8e8e8;
        }

        .quantity-input-group .qty-btn:first-child {
            border-right: 1px solid #ddd;
        }

        .quantity-input-group .qty-btn:last-child {
            border-left: 1px solid #ddd;
        }

        .quantity-input-group .qty-input {
            width: 60px;
            height: 45px;
            border: none;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            background: #fff;
            -moz-appearance: textfield;
        }

        .quantity-input-group .qty-input::-webkit-outer-spin-button,
        .quantity-input-group .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .quantity-input-group .qty-input:focus {
            outline: none;
        }

        .product-action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-product-cart {
            flex: 1;
            min-width: 150px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 30px;
            background: #7cbf3d;
            border: none;
            border-radius: 30px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-product-cart:hover {
            background: #6aab2e;
        }

        .btn-product-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-product-cart i {
            font-size: 14px;
        }

        .btn-product-buy {
            flex: 1;
            min-width: 150px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 30px;
            background: #ffc107;
            border: none;
            border-radius: 30px;
            color: #333;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-product-buy:hover {
            background: #e0a800;
        }

        .btn-product-buy:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-product-buy i {
            font-size: 14px;
        }

        .cart-error-message {
            margin-top: 10px;
            padding: 8px 12px;
            text-align: center;
            color: #dc3545;
            font-size: 13px;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 5px;
        }

        @media (max-width: 576px) {
            .product-action-buttons {
                flex-direction: column;
            }

            .btn-product-cart,
            .btn-product-buy {
                width: 100%;
            }

            .quantity-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="add-to-cart-wrapper">
        <div class="quantity-row">
            <span class="quantity-label">Quantity:</span>
            <div class="quantity-input-group">
                <button type="button" class="qty-btn" wire:click="decrementQuantity">−</button>
                <input class="qty-input" type="number" min="1" value="{{ $quantity }}" wire:model.live="quantity" />
                <button type="button" class="qty-btn" wire:click="incrementQuantity">+</button>
            </div>
        </div>

        <div class="product-action-buttons">
            <button type="button" class="btn-product-cart" wire:click="addToCart" wire:loading.attr="disabled">
                <i class="bi bi-lock-fill"></i>
                <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
                <span wire:loading wire:target="addToCart">Adding...</span>
            </button>
            <button type="button" class="btn-product-buy" wire:click="buyNow" wire:loading.attr="disabled">
                <i class="bi bi-lock-fill"></i>
                <span wire:loading.remove wire:target="buyNow">Buy Now</span>
                <span wire:loading wire:target="buyNow">Processing...</span>
            </button>
        </div>
    </div>

    @if ($errors->has('quantity'))
        <div class="cart-error-message">
            @foreach ($errors->get('quantity') as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
</div>
