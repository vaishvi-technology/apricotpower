@php
$banners = [
    [
        'image' => 'images/home/product-banner-img-1.png',
        'link' => '/store',
        'alt' => 'Banner 1',
    ],
    [
        'image' => 'images/home/product-banner-img-2.png',
        'link' => '/store',
        'alt' => 'Banner 2',
    ],
];
@endphp

<section class="banner-slider-section">
    <div class="swiper banner-slider-swiper">
        <div class="swiper-wrapper">
            @foreach($banners as $banner)
                <div class="swiper-slide">
                    <div class="banner-image-wrapper">
                        <a href="{{ $banner['link'] }}">
                            <img src="{{ asset($banner['image']) }}" alt="{{ $banner['alt'] }}" class="banner-image">
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="our-products-swiper-buttons banner-slider-buttons">
            <div class="swiper-button-next">
                <img src="{{ asset('images/home/next-icon.png') }}" alt="">
            </div>
            <div class="swiper-button-prev">
                <img src="{{ asset('images/home/prev-icon.png') }}" alt="">
            </div>
        </div>
    </div>
</section>
