<div class="home-page">
    @assets
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    @endassets

    <x-home.banner :products="$bannerProducts" />
    <x-home.our-products :products="$ourProducts" />
    <x-home.hot-buys :products="$hotBuysProducts" />
    <x-home.free-shipping />
    <x-home.banner-slider />
    <x-home.about />
    <x-home.testimonial />

    @assets
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @endassets

    @script
    <script>
        function initSwipers() {
            // Banner Product Carousel
            if (document.querySelector('.banner-swiper')) {
                new Swiper('.banner-swiper', {
                    loop: true,
                    navigation: {
                        nextEl: '.banner-next',
                        prevEl: '.banner-prev',
                    },
                });
            }

            // Our Products Carousel
            if (document.querySelector('.our-products-swiper')) {
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
            }

            // Hot Buys Carousel
            if (document.querySelector('.hot-buys-swiper')) {
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
            }

            // Banner Slider (promotional)
            if (document.querySelector('.banner-slider-swiper')) {
                new Swiper('.banner-slider-swiper', {
                    loop: true,
                    autoplay: { delay: 5000 },
                    pagination: {
                        el: '.banner-slider-section .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-buttons-banner .swiper-button-next',
                        prevEl: '.swiper-buttons-banner .swiper-button-prev',
                    },
                });
            }
        }

        initSwipers();
    </script>
    @endscript
</div>
