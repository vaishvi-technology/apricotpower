@php
$banners = [
    [
        'image' => 'images/home/homepage-slider1.png',
        'link' => '/store',
        'alt' => 'Mushroom Coffee',
    ],
    [
        'image' => 'images/home/homepage-slider2.png',
        'link' => '/store',
        'alt' => 'Megazyme B-15 and B17',
    ],
    [
        'image' => 'images/home/homepage-slider3.png',
        'link' => '/store',
        'alt' => 'Apricot Seeds',
    ],
    [
        'image' => 'images/home/homepage-slider4.png',
        'link' => '/store',
        'alt' => 'Happy Healthy B17',
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
        <div class="swiper-pagination"></div>
        <div class="our-products-swiper-buttons swiper-buttons-banner">
            <div class="swiper-button-next laptop-button-next">
                <img src="{{ asset('images/home/next-icon.png') }}" alt="">
            </div>
            <div class="swiper-button-prev laptop-button-prev">
                <img src="{{ asset('images/home/prev-icon.png') }}" alt="">
            </div>
        </div>
    </div>
</section>
