@props(['products'])

<section class="our-products">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="sec-content text-center">
                    <h2>
                        Our <span class="secondary-color">Products</span>
                    </h2>
                    <p class="ourProduct-text">
                        Apricot Seeds, Vitamin B17 (Amygdalin) and more!
                    </p>
                    <hr class="border-buttom border-2" style="border-color: black;">
                </div>
            </div>
            <div class="col-md-12">
                <div class="swiper our-products-swiper">
                    <div class="swiper-wrapper">
                        @foreach($products as $product)
                            @php $thumbnail = $product->getFirstMedia('images'); @endphp
                            <div class="swiper-slide">
                                <div class="our-products-item">
                                    <div class="our-products-item-img cursor-pointer">
                                        <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                                            @if($thumbnail)
                                                <img width="300" height="400" src="{{ $thumbnail->getUrl('medium') }}" alt="{{ $product->translateAttribute('name') }}">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="our-products-item-content cursor">
                                        <h4 class="text-black res-border">
                                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" style="text-decoration: none; color: inherit;">
                                                {{ $product->translateAttribute('name') }}
                                            </a>
                                        </h4>
                                        <div class="our-products-item-content-price">
                                            @php
                                                $basePrice = $product->variants->first()?->basePrices->first();
                                            @endphp
                                            @if($basePrice)
                                                @if($basePrice->compare_price && $basePrice->compare_price->value > $basePrice->price->value)
                                                    <span class="actual-price">{{ $basePrice->compare_price->formatted() }}</span>
                                                @endif
                                                <span class="discount-price">{{ $basePrice->price->formatted() }}</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-evenly">
                                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" class="button-with-icon">Buy Now</a>
                                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" class="button-with-icon">Add To Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="our-products-swiper-buttons">
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
    <div class="justify-content-center d-flex mt-2">
        <a href="/store" class="button-with-icon">View All Products</a>
    </div>
</section>
