@php
$products = [
    [
        'name' => 'Apricot Power B17/Amygdalin 500mg 100 Caps',
        'image' => 'images/home/hot-buys-product-1.png',
        'map_price' => '89.99',
        'sell_price' => '74.99',
        'you_save' => 'You Save $15.00',
        'min_quantity' => 2,
        'item_code' => 'AP-B17-500',
        'link' => '/item/595-apricot-power-b17-amygdalin-500mg-capsules',
    ],
    [
        'name' => 'California Bitter Raw Apricot Seeds 32oz',
        'image' => 'images/home/hot-buys-product-2.png',
        'map_price' => '39.99',
        'sell_price' => '34.99',
        'you_save' => 'You Save $5.00',
        'min_quantity' => 2,
        'item_code' => 'AP-SEEDS-32',
        'link' => '/item/376-california-bitter-raw-apricot-seeds-32-oz',
    ],
    [
        'name' => 'Apricot Seed Capsules 180 Count',
        'image' => 'images/home/hot-buys-product-3.png',
        'map_price' => '59.99',
        'sell_price' => '49.99',
        'you_save' => 'You Save $10.00',
        'min_quantity' => 2,
        'item_code' => 'AP-SEED-CAPS',
        'link' => '/item/705-apricot-seed-capsules',
    ],
    [
        'name' => 'Big 3 B17 Pack 500mg',
        'image' => 'images/home/hot-buys-product-4.png',
        'map_price' => '199.99',
        'sell_price' => '169.99',
        'you_save' => 'You Save $30.00',
        'min_quantity' => 1,
        'item_code' => 'AP-BIG3-500',
        'link' => '/item/730-big-3-b17-pack-500mg',
    ],
    [
        'name' => 'AP Fuel California Special',
        'image' => 'images/home/california.png',
        'map_price' => '49.99',
        'sell_price' => '42.99',
        'you_save' => 'You Save $7.00',
        'min_quantity' => 2,
        'item_code' => 'AP-FUEL-CA',
        'link' => '/item/991-apfuel-california-special',
    ],
    [
        'name' => 'AP Fuel Mushroom Coffee Mix',
        'image' => 'images/home/mushroom.png',
        'map_price' => '34.99',
        'sell_price' => '29.99',
        'you_save' => 'You Save $5.00',
        'min_quantity' => 2,
        'item_code' => 'AP-MUSH-COF',
        'link' => '/item/965-ap-fuel---mushroom-coffee-mix',
    ],
];
@endphp

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
                        @foreach($products as $item)
                            <div class="swiper-slide">
                                <div class="hot-buys-item left-border">
                                    <div class="cursor-pointer">
                                        <a href="{{ $item['link'] }}">
                                            <img width="300" height="400" src="{{ asset($item['image']) }}" alt="Product">
                                        </a>
                                    </div>
                                    <div class="hot-buys-item-content cursor">
                                        <h4 class="text-theme">
                                            <a href="{{ $item['link'] }}" style="text-decoration: none; color: inherit;">
                                                {{ $item['name'] }}
                                            </a>
                                        </h4>
                                        <div class="hot-buys-item-content-price">
                                            <span class="price">Price:</span>
                                            @if($item['map_price'] === $item['sell_price'])
                                                <span class="discount-price secondary-color">${{ $item['sell_price'] }}</span>
                                            @else
                                                <span class="actual-price">${{ $item['map_price'] }}</span>
                                                <span class="discount-price secondary-color">${{ $item['sell_price'] }}</span>
                                            @endif
                                        </div>
                                        <div class="hot-buys-item-description">
                                            <p>{{ $item['you_save'] }}</p>
                                            <p title="You Must Have at least {{ $item['min_quantity'] }} items in your cart to receive this discounted base price">
                                                minimum purchase required (?)
                                            </p>
                                        </div>
                                        <div class="feefo-product-stars-widget" data-product-sku="{{ $item['item_code'] }}"></div>
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ $item['link'] }}" class="add-to-cart-btn" style="text-decoration: none; text-align: center;">Buy Now</a>
                                            <a href="{{ $item['link'] }}" class="add-to-cart-btn" style="text-decoration: none; text-align: center;">Add To Cart</a>
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
