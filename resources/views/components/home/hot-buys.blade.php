@props(['products'])

<section class="hot-buys-sec">
    <div class="">
        <div class="col-md-12">
            <div class="sec-content">
                <div class="row align-items-center">
                    <div class="col-12 col-md-8 d-flex justify-content-md-end justify-content-center mb-3 mb-md-0 text-md-end text-center">
                        <h2 class="hot-buy-heading">
                            Hot <span class="primary-color">Buys</span>
                        </h2>
                    </div>
                    <div class="col-12 col-md-4 d-flex justify-content-md-end justify-content-center text-md-end text-center">
                        <img src="{{ asset('images/home/hot-buy.png') }}" alt="Hot Buy" class="hot-buy-image">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 top-buttom-border">
            <div class="hot-buys-slider-content">
                <h3 class="hot-buys-slider-title">OUR SHOP</h3>
                <div class="swiper hot-buys-swiper">
                    <div class="swiper-wrapper">
                        @foreach($products as $product)
                            @php
                                $variant = $product->variants->first();
                                $basePrice = $variant?->basePrices->first();
                                $thumbnail = $product->getFirstMedia('images');
                            @endphp
                            <div class="swiper-slide">
                                <div class="hot-buys-item left-border">
                                    <div class="cursor-pointer">
                                        <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                                            @if($thumbnail)
                                                <img width="300" height="400" src="{{ $thumbnail->getUrl('medium') }}" alt="{{ $product->translateAttribute('name') }}">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="hot-buys-item-content cursor">
                                        <h4 class="text-theme">
                                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" style="text-decoration: none; color: inherit;">
                                                {{ $product->translateAttribute('name') }}
                                            </a>
                                        </h4>
                                        <div class="hot-buys-item-content-price">
                                            <span class="price">Price:</span>
                                            @if($basePrice)
                                                @if($basePrice->compare_price && $basePrice->compare_price->value > $basePrice->price->value)
                                                    <span class="actual-price">{{ $basePrice->compare_price->formatted() }}</span>
                                                @endif
                                                <span class="discount-price secondary-color">{{ $basePrice->price->formatted() }}</span>
                                            @endif
                                        </div>
                                        <div class="hot-buys-item-description">
                                            @if($basePrice?->compare_price && $basePrice->compare_price->value > $basePrice->price->value)
                                                @php
                                                    $savingsAmount = $basePrice->compare_price->value - $basePrice->price->value;
                                                @endphp
                                                <p>You Save ${{ number_format($savingsAmount / 100, 2) }}</p>
                                            @endif
                                            <p class="minimum-purchase-text">minimum purchase required (?)</p>
                                        </div>
                                        @if($variant?->sku)
                                            <div class="feefo-product-stars-widget" data-product-sku="{{ $variant->sku }}"></div>
                                        @endif
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" class="add-to-cart-btn" style="text-decoration: none; text-align: center;">Buy Now</a>
                                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" class="add-to-cart-btn" style="text-decoration: none; text-align: center;">Add To Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="hot-buys-swiper-buttons">
                        <div class="swiper-button-next">
                            <img src="{{ asset('images/home/next-icon.png') }}" alt="">
                        </div>
                        <div class="swiper-button-prev">
                            <img src="{{ asset('images/home/prev-icon.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
