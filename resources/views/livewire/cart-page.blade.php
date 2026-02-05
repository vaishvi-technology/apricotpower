<div class="cart-page py-5">
    <div class="container">
        <h1 class="mb-4">Shopping Cart</h1>

        @if($this->cart && $this->cart->lines->count() > 0)
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-items">
                        @foreach($this->cart->lines as $line)
                            <div class="cart-item card mb-3">
                                <div class="card-body d-flex align-items-center">
                                    <div class="cart-item-image me-3">
                                        @if($line->purchasable->product->thumbnail)
                                            <img src="{{ $line->purchasable->product->thumbnail->getUrl('small') }}"
                                                 alt="{{ $line->purchasable->product->translateAttribute('name') }}"
                                                 class="img-fluid" style="width: 100px;">
                                        @endif
                                    </div>
                                    <div class="cart-item-details flex-grow-1">
                                        <h5>{{ $line->purchasable->product->translateAttribute('name') }}</h5>
                                        <p class="text-muted mb-1">Quantity: {{ $line->quantity }}</p>
                                        <p class="fw-bold">{{ $line->subTotal->formatted() }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>{{ $this->cart->subTotal->formatted() }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong>{{ $this->cart->total->formatted() }}</strong>
                            </div>
                            <a href="{{ route('checkout.view') }}" class="btn btn-warning w-100">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <h4>Your cart is empty</h4>
                <p class="text-muted">Add some products to your cart to continue shopping.</p>
                <a href="{{ route('store') }}" class="btn btn-warning">Continue Shopping</a>
            </div>
        @endif
    </div>
</div>
