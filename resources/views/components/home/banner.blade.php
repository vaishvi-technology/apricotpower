@props(['products'])

<section class="main-banner">
    <div class="main-banner-innerBG">
        <div class="container">
            <div class="row">
                {{-- Left Content --}}
                <div class="col-xl-6 banner-left-content">
                    <div class="main_banner-content">
                        <h1 class="mt-3">
                            Welcome To <br>
                            <span class="primary-color">Apricot</span>
                            <span class="secondary-color">Power</span>
                        </h1>
                        <p class="text-white">
                            Apricot Power is your reliable source for quality apricot
                            seeds and B17 products.
                        </p>
                        <a href="/store" class="button-with-icon button-with-icon-primary button-with-icon-lg">
                            Shop Now
                        </a>
                        <h3 class="slider-title">Apricot</h3>
                    </div>
                </div>

                {{-- Right Image + Carousel --}}
                <div class="col-xl-6 banner-right-image">
                    <div class="main-banner-image-logo">
                        <img src="{{ asset('images/home/banner-logo.png') }}" alt="Banner Logo">
                    </div>
                    <div class="main-banner-image">
                        <div class="swiper banner-swiper">
                            <div class="swiper-wrapper">
                                @foreach($products as $product)
                                    @php $thumbnail = $product->getFirstMedia('images'); @endphp
                                    <div class="swiper-slide">
                                        <div class="banner-parent">
                                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                                                @if($thumbnail)
                                                    <img src="{{ $thumbnail->getUrl('large') }}" class="banner-img cursor-pointer" alt="{{ $product->translateAttribute('name') }}">
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="our-products-swiper-buttons">
                                <div class="swiper-button-next banner-next">
                                    <img src="{{ asset('images/home/next-icon.png') }}" alt="">
                                </div>
                                <div class="swiper-button-prev banner-prev">
                                    <img src="{{ asset('images/home/prev-icon.png') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
