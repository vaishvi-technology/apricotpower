<div class="store-page home-page">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-utilities.min.css">
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    @endpush

    {{-- Inner Banner --}}
    <section class="inner-banner">
        <div class="inner-banner-head">
            <h3 class="bold-font text-white mb-0">Products</h3>
        </div>
    </section>

    <section class="fuel-product-sec">
        <div class="container">
            {{-- Sort Dropdown --}}
            <div class="row align-items-center mb-4">
                <div class="col-12 col-md-4 offset-md-8">
                    <div class="d-flex align-items-center gap-2">
                        <label for="sortBy" class="form-label mb-0 text-nowrap">Sort By:</label>
                        <select id="sortBy" class="form-select" wire:model.live="sortBy">
                            <option value="">Default</option>
                            <option value="highest_price">Highest Price</option>
                            <option value="lowest_price">Lowest Price</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Products by Category --}}
            @forelse($this->collectionsWithProducts as $collection)
                @if($collection->products->count())
                    <div class="row justify-content-center" id="category-{{ $collection->id }}">
                        <h1 class="product-Category-title secondary-color text-store text-center p-3 mt-5">
                            {{ $collection->translateAttribute('name') }}
                        </h1>

                        @foreach($collection->products as $product)
                            @php
                                $variant = $product->variants->first();
                                $basePrice = $variant?->basePrices->first();
                            @endphp
                            <div class="col-xl-4 col-lg-6 col-md-6 resposive-store">
                                <div class="fuel-product-item">
                                    <div class="fuel-product-item-top">
                                        <div class="fuel-product-img cursor-pointer">
                                            @if($product->defaultUrl)
                                                <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                                                    @if($product->thumbnail)
                                                        <img src="{{ $product->thumbnail->getUrl('medium') }}"
                                                             alt="{{ $product->translateAttribute('name') }}"
                                                             style="width: 200px; height: 250px; object-fit: contain;">
                                                    @endif
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="fuel-product-item-content cursor">
                                        <h3 class="text-theme">
                                            @if($product->defaultUrl)
                                                <a href="{{ route('product.view', $product->defaultUrl->slug) }}"
                                                   style="text-decoration: none; color: inherit;">
                                                    {{ $product->translateAttribute('name') }}
                                                </a>
                                            @else
                                                {{ $product->translateAttribute('name') }}
                                            @endif
                                        </h3>

                                        <div class="hot-buys-item-content-price">
                                            <span class="price">Price:</span>
                                            @if($basePrice)
                                                @if($basePrice->compare_price && $basePrice->compare_price->value > $basePrice->price->value)
                                                    <span class="actual-price">${{ number_format($basePrice->compare_price->value / 100, 2) }}</span>
                                                @endif
                                                <span class="discount-price secondary-color">${{ number_format($basePrice->price->value / 100, 2) }}</span>
                                            @endif
                                        </div>

                                        <div class="hot-buys-item-description">
                                            @if($basePrice?->compare_price && $basePrice->compare_price->value > $basePrice->price->value)
                                                @php
                                                    $savings = $basePrice->compare_price->value - $basePrice->price->value;
                                                @endphp
                                                <p style="height: 20px; color: var(--secondary-color);">
                                                    You Save ${{ number_format($savings / 100, 2) }}
                                                </p>
                                            @else
                                                <p style="height: 20px;">&nbsp;</p>
                                            @endif
                                            @if($variant?->min_quantity > 1)
                                                <p title="You Must Have at least {{ $variant->min_quantity }} items in your cart to receive this discounted base price">
                                                    minimum purchase required (?)
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Feefo Rating --}}
                                        @if($variant?->sku)
                                            <div class="fuel-product-rating" style="height: 30px; margin-top: 10px;">
                                                <div class="feefo-product-stars-widget" data-product-sku="{{ $variant->sku }}"></div>
                                            </div>
                                        @endif

                                        {{-- Action Buttons --}}
                                        <div class="d-flex justify-content-evenly w-100 mt-3">
                                            @if($variant)
                                                <button class="button-with-icon"
                                                        wire:click="buyNow({{ $variant->id }})"
                                                        wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="buyNow({{ $variant->id }})">Buy Now</span>
                                                    <span wire:loading wire:target="buyNow({{ $variant->id }})">
                                                        <div class="spinner-border spinner-border-sm text-white" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </span>
                                                </button>
                                                <button class="button-with-icon"
                                                        wire:click="addToCart({{ $variant->id }})"
                                                        wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="addToCart({{ $variant->id }})">Add To Cart</span>
                                                    <span wire:loading wire:target="addToCart({{ $variant->id }})">
                                                        <div class="spinner-border spinner-border-sm text-white" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @empty
                <div class="row">
                    <div class="col-12 text-center py-5">
                        <h4>No products found</h4>
                        <p class="text-muted">Check back soon for new products.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="https://api.feefo.com/api/javascript/apricot-power" async></script>
    @endpush
</div>
