<div>
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="cart-qty">
            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="decrementQuantity">−</button>
            <input class="qty-input" type="number" min="1" value="{{ $quantity }}" wire:model.live="quantity" />
            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="incrementQuantity">+</button>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="button" class="button-with-icon" wire:click="buyNow" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="buyNow">Buy Now</span>
            <span wire:loading wire:target="buyNow">Processing...</span>
        </button>
        <button type="button" class="button-with-icon button-with-icon-primary" wire:click="addToCart" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="addToCart">Add to Cart</span>
            <span wire:loading wire:target="addToCart">Adding...</span>
        </button>
    </div>

    @if ($errors->has('quantity'))
        <div class="mt-2 p-2 text-center text-danger small bg-danger bg-opacity-10 rounded" role="alert">
            @foreach ($errors->get('quantity') as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
</div>
