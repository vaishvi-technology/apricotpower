@php
$products = [
    [
        'name' => 'Apricot Power B17/Amygdalin 500mg',
        'image' => 'images/home/our-product-img1.png',
        'map_price' => '89.99',
        'sell_price' => '74.99',
        'link' => '/item/595-apricot-power-b17-amygdalin-500mg-capsules',
    ],
    [
        'name' => 'California Bitter Raw Apricot Seeds',
        'image' => 'images/home/our-product-img2.png',
        'map_price' => '39.99',
        'sell_price' => '34.99',
        'link' => '/item/376-california-bitter-raw-apricot-seeds-32-oz',
    ],
    [
        'name' => 'Apricot Seed Capsules',
        'image' => 'images/home/our-product-img3.png',
        'map_price' => '59.99',
        'sell_price' => '49.99',
        'link' => '/item/705-apricot-seed-capsules',
    ],
    [
        'name' => 'Big 3 B17 Pack 500mg',
        'image' => 'images/home/big-3-copy.png',
        'map_price' => '199.99',
        'sell_price' => '169.99',
        'link' => '/item/730-big-3-b17-pack-500mg',
    ],
    [
        'name' => 'AP Fuel California Special',
        'image' => 'images/home/california.png',
        'map_price' => '49.99',
        'sell_price' => '42.99',
        'link' => '/item/991-apfuel-california-special',
    ],
    [
        'name' => 'AP Fuel Mushroom Coffee Mix',
        'image' => 'images/home/mushroom.png',
        'map_price' => '34.99',
        'sell_price' => '29.99',
        'link' => '/item/965-ap-fuel---mushroom-coffee-mix',
    ],
];
@endphp

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
                        @foreach($products as $item)
                            <div class="swiper-slide">
                                <div class="our-products-item">
                                    <div class="our-products-item-img cursor-pointer">
                                        <a href="{{ $item['link'] }}">
                                            <img width="300" height="400" src="{{ asset($item['image']) }}" alt="Product Image">
                                        </a>
                                    </div>
                                    <div class="our-products-item-content cursor">
                                        <h4 class="text-black res-border">
                                            <a href="{{ $item['link'] }}" style="text-decoration: none; color: inherit;">
                                                {{ $item['name'] }}
                                            </a>
                                        </h4>
                                        <div class="our-products-item-content-price">
                                            @if($item['map_price'] === $item['sell_price'])
                                                <span class="discount-price">${{ $item['sell_price'] }}</span>
                                            @else
                                                <span class="actual-price">${{ $item['map_price'] }}</span>
                                                <span class="discount-price">${{ $item['sell_price'] }}</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-evenly">
                                            <a href="{{ $item['link'] }}" class="button-with-icon">Buy Now</a>
                                            <a href="{{ $item['link'] }}" class="button-with-icon">Add To Cart</a>
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
