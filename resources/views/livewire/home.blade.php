<div class="home-page">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-utilities.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    @endpush

    <x-home.banner />
    <x-home.our-products />
    <x-home.hot-buys />
    <x-home.free-shipping />
    <x-home.banner-slider />
    <x-home.about />
    <x-home.testimonial />

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Banner Product Carousel
            new Swiper('.banner-swiper', {
                loop: true,
                navigation: {
                    nextEl: '.banner-next',
                    prevEl: '.banner-prev',
                },
            });

            // Our Products Carousel
            new Swiper('.our-products-swiper', {
                slidesPerView: 3,
                spaceBetween: 20,
                centeredSlides: true,
                loop: true,
                navigation: {
                    nextEl: '.our-products-swiper-buttons .swiper-button-next',
                    prevEl: '.our-products-swiper-buttons .swiper-button-prev',
                },
                breakpoints: {
                    320: { slidesPerView: 1 },
                    768: { slidesPerView: 1 },
                    992: { slidesPerView: 3 },
                },
            });

            // Hot Buys Carousel
            new Swiper('.hot-buys-swiper', {
                spaceBetween: 0,
                navigation: {
                    nextEl: '.hot-buys-swiper-buttons .swiper-button-next',
                    prevEl: '.hot-buys-swiper-buttons .swiper-button-prev',
                },
                breakpoints: {
                    320: { slidesPerView: 1 },
                    736: { slidesPerView: 2 },
                    850: { slidesPerView: 3 },
                    1300: { slidesPerView: 4 },
                },
            });

            // Banner Slider (promotional)
            new Swiper('.banner-slider-swiper', {
                loop: true,
                autoplay: { delay: 5000 },
                navigation: {
                    nextEl: '.banner-slider-buttons .swiper-button-next',
                    prevEl: '.banner-slider-buttons .swiper-button-prev',
                },
            });
        });
        </script>
    @endpush
</div>
