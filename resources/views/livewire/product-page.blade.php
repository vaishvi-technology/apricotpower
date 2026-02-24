<div class="product-page home-page">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
        <style>
            /* Product Page Styles */
            .product-detail-section {
                padding: 30px 0 50px;
                background: #fff;
            }

            .product-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 15px;
            }

            .product-layout {
                display: grid;
                grid-template-columns: 2fr 3fr;
                gap: 40px;
                align-items: start;
            }

            /* Product Image Section */
            .product-image-section {
                position: sticky;
                top: 20px;
            }

            .main-product-image {
                background: #fff;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
                margin-bottom: 20px;
            }

            .main-product-image img {
                max-width: 100%;
                max-height: 350px;
                object-fit: contain;
            }

            /* Product Gallery - Amazon Style */
            .product-gallery {
                display: flex;
                gap: 15px;
            }

            .gallery-thumbnails {
                display: flex;
                flex-direction: column;
                gap: 10px;
                max-height: 350px;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: #ccc #f5f5f5;
            }

            .gallery-thumbnails::-webkit-scrollbar {
                width: 6px;
            }

            .gallery-thumbnails::-webkit-scrollbar-track {
                background: #f5f5f5;
                border-radius: 3px;
            }

            .gallery-thumbnails::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 3px;
            }

            .thumbnail-item {
                width: 65px;
                height: 65px;
                min-height: 65px;
                padding: 4px;
                border: 2px solid #e0e0e0;
                border-radius: 4px;
                background: #fff;
                cursor: pointer;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
                flex-shrink: 0;
            }

            .thumbnail-item:hover {
                border-color: #7cbf3d;
            }

            .thumbnail-item.active {
                border-color: #7cbf3d;
                box-shadow: 0 0 0 2px rgba(124, 191, 61, 0.3);
            }

            .thumbnail-item img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .main-image-wrapper {
                position: relative;
                display: inline-block;
                cursor: zoom-in;
            }

            .main-gallery-image {
                max-width: 100%;
                max-height: 350px;
                object-fit: contain;
                transition: transform 0.3s ease;
            }

            .zoom-hint {
                margin-top: 10px;
                font-size: 12px;
                color: #888;
            }

            .zoom-hint i {
                margin-right: 5px;
            }

            .no-image-placeholder {
                width: 100%;
                height: 300px;
                background: #f5f5f5;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
            }

            .no-image-placeholder i {
                font-size: 60px;
                color: #ccc;
            }

            /* Lightbox Modal */
            .lightbox-modal {
                position: fixed;
                inset: 0;
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .lightbox-backdrop {
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.9);
            }

            .lightbox-content {
                position: relative;
                z-index: 1;
                max-width: 90vw;
                max-height: 90vh;
            }

            .lightbox-content img {
                max-width: 100%;
                max-height: 90vh;
                object-fit: contain;
            }

            .lightbox-close {
                position: absolute;
                top: -40px;
                right: 0;
                background: none;
                border: none;
                color: #fff;
                font-size: 24px;
                cursor: pointer;
                padding: 10px;
                transition: transform 0.2s;
            }

            .lightbox-close:hover {
                transform: scale(1.1);
            }

            /* Tag Badges */
            .product-tag-badges {
                display: flex;
                gap: 10px;
                justify-content: flex-start;
                flex-wrap: wrap;
            }

            .tag-badge {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                color: #fff;
                text-align: center;
                line-height: 1.1;
            }

            .tag-badge-organic {
                background: #8bc34a;
            }

            .tag-badge-nongmo {
                background: #9e9d24;
            }

            .tag-badge-raw {
                background: #a1887f;
            }

            .tag-badge-vegan {
                background: #7cb342;
            }

            .tag-badge-icon {
                font-size: 16px;
                margin-bottom: 2px;
            }

            /* Product Info Section */
            .product-info-section {
                padding: 10px 0;
            }

            .product-title {
                font-size: 28px;
                font-weight: 700;
                color: #333;
                margin-bottom: 15px;
                line-height: 1.3;
            }

            .product-description {
                font-size: 13px;
                font-weight: 400;
                color: #555;
                margin-bottom: 15px;
                line-height: 1.6;
                font-style: italic;
            }

            .product-size {
                font-size: 14px;
                color: #333;
                margin-bottom: 12px;
            }

            .product-size strong {
                font-weight: 600;
            }

            /* Rating */
            .product-rating {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 15px;
            }

            .stars {
                display: flex;
                color: #f5c518;
            }

            .stars i {
                font-size: 18px;
            }

            .stars i.empty {
                color: #ddd;
            }

            .rating-count {
                font-size: 14px;
                color: #007185;
            }

            .rating-count a {
                color: #007185;
                text-decoration: none;
            }

            .rating-count a:hover {
                text-decoration: underline;
            }

            /* Pricing */
            .product-pricing {
                margin-bottom: 5px;
            }

            .price-row {
                display: flex;
                align-items: baseline;
                gap: 10px;
                flex-wrap: wrap;
            }

            .price-label {
                font-size: 14px;
                color: #333;
            }

            .compare-price {
                font-size: 16px;
                color: #666;
                text-decoration: line-through;
            }

            .current-price {
                font-size: 26px;
                font-weight: 600;
                color: #7cbf3d;
            }

            .savings-text {
                font-size: 13px;
                color: #333;
                margin-top: 5px;
            }

            .min-purchase-text {
                font-size: 11px;
                color: #007185;
                margin-top: 5px;
                margin-bottom: 20px;
            }

            .min-purchase-text .tooltip-trigger {
                color: #007185;
                cursor: help;
            }

            /* Quantity Selector */
            .quantity-section {
                margin-bottom: 20px;
            }

            .quantity-label {
                font-size: 14px;
                font-weight: 600;
                color: #333;
                margin-right: 15px;
            }

            .quantity-wrapper {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .quantity-input-group {
                display: flex;
                align-items: center;
                border: 1px solid #ddd;
                border-radius: 5px;
                overflow: hidden;
            }

            .quantity-input-group button {
                width: 40px;
                height: 40px;
                border: none;
                background: #f5f5f5;
                font-size: 18px;
                cursor: pointer;
                transition: background 0.2s;
            }

            .quantity-input-group button:hover {
                background: #e0e0e0;
            }

            .quantity-input-group input {
                width: 60px;
                height: 40px;
                border: none;
                text-align: center;
                font-size: 16px;
                font-weight: 600;
            }

            .quantity-input-group input:focus {
                outline: none;
            }

            /* Add to Cart Button */
            .add-to-cart-section {
                margin-bottom: 25px;
            }

            .btn-add-to-cart {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 12px 40px;
                background: #7cbf3d;
                border: none;
                border-radius: 25px;
                color: #fff;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            }

            .btn-add-to-cart:hover {
                background: #6aab2e;
            }

            .btn-add-to-cart i {
                font-size: 14px;
            }

            .btn-buy-now {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 12px 40px;
                background: #ffc107;
                border: none;
                border-radius: 25px;
                color: #333;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s;
            }

            .btn-buy-now:hover {
                background: #e0a800;
            }

            .btn-buy-now i {
                font-size: 14px;
            }

            .product-buttons {
                display: flex;
                gap: 15px;
                flex-wrap: wrap;
            }

            /* Categories & Tags */
            .product-meta {
                border-top: 1px solid #eee;
                padding-top: 20px;
                margin-top: 10px;
            }

            .meta-row {
                margin-bottom: 10px;
                font-size: 14px;
            }

            .meta-label {
                font-weight: 600;
                color: #333;
            }

            .meta-links a {
                color: #7cbf3d;
                text-decoration: none;
            }

            .meta-links a:hover {
                text-decoration: underline;
            }

            /* Variant Options */
            .variant-options {
                margin-bottom: 20px;
            }

            .variant-label {
                font-size: 14px;
                font-weight: 600;
                color: #333;
                margin-bottom: 8px;
            }

            .variant-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .variant-btn {
                padding: 8px 16px;
                border: 2px solid #ddd;
                border-radius: 5px;
                background: #fff;
                font-size: 13px;
                cursor: pointer;
                transition: all 0.2s;
            }

            .variant-btn:hover {
                border-color: #7cbf3d;
            }

            .variant-btn.active {
                border-color: #7cbf3d;
                background: #7cbf3d;
                color: #fff;
            }

            /* Other Sizes */
            .other-sizes-section {
                margin-top: 25px;
                padding-top: 20px;
                border-top: 1px solid #eee;
            }

            .other-sizes-title {
                font-size: 14px;
                font-weight: 600;
                color: #333;
                margin-bottom: 12px;
            }

            .other-sizes-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .other-size-card {
                width: 80px;
                text-align: center;
                text-decoration: none;
            }

            .other-size-card img {
                width: 70px;
                height: 70px;
                object-fit: contain;
                border: 1px solid #eee;
                border-radius: 5px;
                padding: 5px;
                transition: border-color 0.2s;
            }

            .other-size-card:hover img {
                border-color: #7cbf3d;
            }

            .other-size-name {
                font-size: 10px;
                color: #666;
                margin-top: 5px;
                line-height: 1.2;
            }

            /* Tabs Section */
            .product-tabs-section {
                margin-top: 40px;
                padding-top: 30px;
                border-top: 1px solid #eee;
            }

            .product-tabs .nav-tabs {
                border-bottom: 2px solid #eee;
            }

            .product-tabs .nav-link {
                color: #666;
                font-weight: 600;
                border: none;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                padding: 10px 20px;
            }

            .product-tabs .nav-link:hover {
                color: #7cbf3d;
                border-color: transparent;
            }

            .product-tabs .nav-link.active {
                color: #7cbf3d;
                border-bottom-color: #7cbf3d;
                background: transparent;
            }

            .tab-content {
                padding: 25px 0;
            }

            .tab-content p {
                line-height: 1.7;
                color: #555;
            }

            /* Related Products */
            .related-products-section {
                margin-top: 50px;
                padding-top: 30px;
                border-top: 1px solid #eee;
            }

            .related-products-title {
                font-size: 24px;
                font-weight: 700;
                color: #333;
                margin-bottom: 25px;
            }

            .related-products-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 25px;
            }

            .related-product-card {
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 20px 15px;
                text-align: center;
                transition: box-shadow 0.3s;
            }

            .related-product-card:hover {
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .related-product-image {
                height: 150px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 15px;
            }

            .related-product-image img {
                max-width: 100%;
                max-height: 140px;
                object-fit: contain;
            }

            .related-product-title {
                font-size: 14px;
                font-weight: 600;
                color: #7cbf3d;
                margin-bottom: 10px;
                line-height: 1.3;
            }

            .related-product-title a {
                color: inherit;
                text-decoration: none;
            }

            .related-product-title a:hover {
                text-decoration: underline;
            }

            .related-product-price {
                font-size: 18px;
                font-weight: 600;
                color: #7cbf3d;
            }

            .related-product-compare {
                font-size: 13px;
                color: #666;
                text-decoration: line-through;
                margin-right: 8px;
            }

            /* SKU */
            .product-sku {
                font-size: 12px;
                color: #888;
                margin-top: 15px;
            }

            /* Responsive */
            @media (max-width: 991px) {
                .product-layout {
                    grid-template-columns: 1fr;
                    gap: 30px;
                }

                .product-image-section {
                    position: static;
                }

                .product-gallery {
                    flex-direction: column-reverse;
                }

                .gallery-thumbnails {
                    flex-direction: row;
                    max-height: none;
                    max-width: 100%;
                    overflow-x: auto;
                    overflow-y: hidden;
                    padding-bottom: 10px;
                }

                .gallery-thumbnails::-webkit-scrollbar {
                    height: 6px;
                    width: auto;
                }

                .thumbnail-item {
                    width: 70px;
                    height: 70px;
                    min-width: 70px;
                }

                .related-products-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 576px) {
                .product-title {
                    font-size: 22px;
                }

                .current-price {
                    font-size: 22px;
                }

                .product-buttons {
                    flex-direction: column;
                }

                .btn-add-to-cart,
                .btn-buy-now {
                    width: 100%;
                }

                .related-products-grid {
                    grid-template-columns: 1fr;
                }

                .product-tag-badges {
                    justify-content: center;
                }

                .thumbnail-item {
                    width: 55px;
                    height: 55px;
                    min-width: 55px;
                }

                .main-gallery-image {
                    max-height: 280px;
                }
            }
        </style>
    @endpush

    {{-- Minimal Banner --}}
    <section class="inner-banner" style="padding: 8px 0;">
        <div class="inner-banner-head">
            <h3 class="bold-font text-white mb-0" style="font-size: 16px;">{{ $this->product->translateAttribute('name') }}</h3>
        </div>
    </section>

    {{-- Product Detail Section --}}
    <section class="product-detail-section">
        <div class="product-container">
            <div class="product-layout">
                {{-- Left: Product Image Gallery --}}
                <div class="product-image-section">
                    <div class="product-gallery" x-data="productGallery()">
                        {{-- Thumbnail Strip (Vertical on desktop) --}}
                        @if ($this->images->count() > 1)
                            <div class="gallery-thumbnails">
                                @foreach ($this->images as $media)
                                    <button
                                        type="button"
                                        class="thumbnail-item {{ $this->selectedImageId === $media->id ? 'active' : '' }}"
                                        wire:click="selectImage({{ $media->id }})"
                                        @mouseenter="previewImage('{{ $media->getUrl('large') }}')"
                                        @mouseleave="resetPreview()"
                                    >
                                        <img
                                            src="{{ $media->getUrl('small') }}"
                                            alt="{{ $this->product->translateAttribute('name') }} - Image {{ $loop->iteration }}"
                                            loading="lazy"
                                        />
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        {{-- Main Image Display --}}
                        <div class="main-product-image">
                            @if ($this->selectedImage)
                                <div class="main-image-wrapper" @click="openLightbox('{{ $this->selectedImage->getUrl('large') }}')">
                                    <img
                                        :src="previewSrc || '{{ $this->selectedImage->getUrl('large') }}'"
                                        alt="{{ $this->product->translateAttribute('name') }}"
                                        class="main-gallery-image"
                                    />
                                </div>
                                <div class="zoom-hint">
                                    <i class="bi bi-zoom-in"></i> Click image to enlarge
                                </div>
                            @else
                                <div class="no-image-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tag Badges --}}
                    @if ($this->product->tags->count())
                        <div class="product-tag-badges">
                            @foreach ($this->product->tags->take(4) as $tag)
                                @php
                                    $tagLower = strtolower($tag->value);
                                    $badgeClass = 'tag-badge-organic';
                                    $icon = 'O';
                                    if (str_contains($tagLower, 'non-gmo') || str_contains($tagLower, 'nongmo')) {
                                        $badgeClass = 'tag-badge-nongmo';
                                        $icon = 'NG';
                                    } elseif (str_contains($tagLower, 'raw')) {
                                        $badgeClass = 'tag-badge-raw';
                                        $icon = 'R';
                                    } elseif (str_contains($tagLower, 'vegan')) {
                                        $badgeClass = 'tag-badge-vegan';
                                        $icon = 'V';
                                    } elseif (str_contains($tagLower, 'organic')) {
                                        $badgeClass = 'tag-badge-organic';
                                        $icon = 'O';
                                    }
                                @endphp
                                <div class="tag-badge {{ $badgeClass }}">
                                    <span class="tag-badge-icon">{{ $icon }}</span>
                                    <span>{{ Str::limit($tag->value, 10) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Right: Product Info --}}
                <div class="product-info-section">
                    <h1 class="product-title">{{ $this->product->translateAttribute('name') }}</h1>

                    @if ($this->product->translateAttribute('description'))
                        <p class="product-description">{{ Str::limit(strip_tags($this->product->translateAttribute('description')), 200) }}</p>
                    @elseif ($this->product->translateAttribute('product_descriptor'))
                        <p class="product-description">{{ $this->product->translateAttribute('product_descriptor') }}</p>
                    @endif

                    @if ($this->product->translateAttribute('quantity_size'))
                        <p class="product-size"><strong>Size:</strong> {{ $this->product->translateAttribute('quantity_size') }}</p>
                    @endif

                    {{-- Rating --}}
                    <div class="product-rating">
                        <div class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill {{ $i <= 4 ? '' : 'empty' }}"></i>
                            @endfor
                        </div>
                        <span class="rating-count">
                            @if ($this->variant->sku)
                                <a href="#reviews-section">(0)</a>
                            @else
                                (0)
                            @endif
                        </span>
                        @if ($this->variant->sku)
                            <div class="feefo-product-stars-widget d-none" data-product-sku="{{ $this->variant->sku }}"></div>
                        @endif
                    </div>

                    {{-- Pricing --}}
                    <div class="product-pricing">
                        @if ($this->basePrice)
                            @php
                                $hasDiscount = $this->basePrice->compare_price && $this->basePrice->compare_price->value > $this->basePrice->price->value;
                                $savingsAmount = $hasDiscount ? ($this->basePrice->compare_price->value - $this->basePrice->price->value) / 100 : 0;
                                $savingsPercent = $hasDiscount ? round(($savingsAmount * 100) / ($this->basePrice->compare_price->value / 100)) : 0;
                            @endphp
                            <div class="price-row">
                                <span class="price-label">Price:</span>
                                @if ($hasDiscount)
                                    <span class="compare-price">{{ $this->basePrice->compare_price->formatted() }}</span>
                                @endif
                                <span class="current-price">{{ $this->basePrice->price->formatted() }}</span>
                            </div>
                            @if ($hasDiscount)
                                <div class="savings-text">
                                    You save ${{ number_format($savingsAmount, 2) }} ({{ $savingsPercent }}%)
                                </div>
                            @endif
                            <div class="min-purchase-text">
                                minimum purchase required <span class="tooltip-trigger" data-bs-toggle="tooltip" data-bs-placement="top" title="You must have at least 1 items in your cart to receive this discounted base price.">(?)</span>
                            </div>
                        @else
                            <span class="current-price">Price unavailable</span>
                        @endif
                    </div>

                    {{-- Variant Options --}}
                    @if ($this->productOptions->count())
                        <div class="variant-options">
                            @foreach ($this->productOptions as $option)
                                <div class="mb-3">
                                    <div class="variant-label">{{ $option['option']->translate('name') }}</div>
                                    <div class="variant-buttons"
                                         x-data="{
                                             selectedOption: @entangle('selectedOptionValues').live,
                                             selectedValues: [],
                                         }"
                                         x-init="selectedValues = Object.values(selectedOption);
                                         $watch('selectedOption', value =>
                                             selectedValues = Object.values(selectedOption)
                                         )">
                                        @foreach ($option['values'] as $value)
                                            <button class="variant-btn"
                                                    type="button"
                                                    wire:click="$set('selectedOptionValues.{{ $option['option']->id }}', {{ $value->id }})"
                                                    :class="{ 'active': selectedValues.includes({{ $value->id }}) }">
                                                {{ $value->translate('name') }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Quantity & Add to Cart --}}
                    <div class="quantity-section">
                        <livewire:components.add-to-cart :purchasable="$this->variant" :wire:key="$this->variant->id" />
                    </div>

                    {{-- Categories & Tags --}}
                    <div class="product-meta">
                        @if ($this->product->category)
                            <div class="meta-row">
                                <span class="meta-label">Categories: </span>
                                <span class="meta-links">
                                    <a href="{{ route('store', ['categories' => [$this->product->category->id]]) }}">
                                        {{ $this->product->category->name }}
                                    </a>
                                </span>
                            </div>
                        @endif

                        @if ($this->product->tags->count())
                            <div class="meta-row">
                                <span class="meta-label">Tags: </span>
                                <span class="meta-links">
                                    @foreach ($this->product->tags as $tag)
                                        <a href="{{ route('store', ['tags' => [$tag->id]]) }}">{{ $tag->value }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                </span>
                            </div>
                        @endif

                        <div class="product-sku">SKU: {{ $this->variant->sku }}</div>
                    </div>

                    {{-- Other Sizes --}}
                    @if ($this->alternateProducts->count())
                        <div class="other-sizes-section">
                            <div class="other-sizes-title">Other Sizes</div>
                            <div class="other-sizes-grid">
                                @foreach ($this->alternateProducts as $altProduct)
                                    @if ($altProduct->defaultUrl)
                                        <a href="{{ route('product.view', $altProduct->defaultUrl->slug) }}" class="other-size-card">
                                            @if ($altProduct->thumbnail)
                                                <img src="{{ $altProduct->thumbnail->getUrl('small') }}" alt="{{ $altProduct->translateAttribute('name') }}">
                                            @endif
                                            <div class="other-size-name">{{ Str::limit($altProduct->translateAttribute('name'), 30) }}</div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabs Section --}}
            <div class="product-tabs-section" id="reviews-section">
                <div class="product-tabs">
                    <ul class="nav nav-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="intro-tab" data-bs-toggle="tab"
                                    data-bs-target="#intro-panel" type="button" role="tab">
                                Intro
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                    data-bs-target="#reviews-panel" type="button" role="tab">
                                Reviews
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="intro-panel" role="tabpanel">
                            <div class="responsive-html-content">
                                {!! $this->product->translateAttribute('description') !!}
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reviews-panel" role="tabpanel">
                            @if ($this->variant->sku)
                                <div class="feefo-review-widget-product" data-product-sku="{{ $this->variant->sku }}"></div>
                            @else
                                <p>No reviews available for this product.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Products --}}
            @if ($this->relatedProducts->count())
                <div class="related-products-section">
                    <h2 class="related-products-title">Related Products</h2>
                    <div class="related-products-grid">
                        @foreach ($this->relatedProducts->take(3) as $relProduct)
                            @php
                                $relVariant = $relProduct->variants->first();
                                $relBasePrice = $relVariant?->basePrices->first();
                                $relHasDiscount = $relBasePrice?->compare_price && $relBasePrice->compare_price->value > $relBasePrice->price->value;
                            @endphp
                            <div class="related-product-card">
                                <div class="related-product-image">
                                    @if ($relProduct->defaultUrl)
                                        <a href="{{ route('product.view', $relProduct->defaultUrl->slug) }}">
                                            @if ($relProduct->thumbnail)
                                                <img src="{{ $relProduct->thumbnail->getUrl('medium') }}"
                                                     alt="{{ $relProduct->translateAttribute('name') }}">
                                            @endif
                                        </a>
                                    @endif
                                </div>
                                <h3 class="related-product-title">
                                    @if ($relProduct->defaultUrl)
                                        <a href="{{ route('product.view', $relProduct->defaultUrl->slug) }}">
                                            {{ $relProduct->translateAttribute('name') }}
                                        </a>
                                    @else
                                        {{ $relProduct->translateAttribute('name') }}
                                    @endif
                                </h3>
                                <div class="related-product-price">
                                    @if ($relBasePrice)
                                        @if ($relHasDiscount)
                                            <span class="related-product-compare">{{ $relBasePrice->compare_price->formatted() }}</span>
                                        @endif
                                        {{ $relBasePrice->price->formatted() }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Lightbox Modal --}}
    <div
        x-data="{ show: false, currentSrc: '' }"
        x-show="show"
        x-on:open-lightbox.window="show = true; currentSrc = $event.detail.src"
        x-on:keydown.escape.window="show = false"
        x-cloak
        class="lightbox-modal"
        style="display: none;"
    >
        <div class="lightbox-backdrop" @click="show = false"></div>
        <div class="lightbox-content">
            <button class="lightbox-close" @click="show = false">
                <i class="bi bi-x-lg"></i>
            </button>
            <img :src="currentSrc" alt="Product Image" />
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="https://api.feefo.com/api/javascript/apricot-power" async></script>
        <script>
            // Initialize Bootstrap tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });

            // Alpine.js component for product gallery
            function productGallery() {
                return {
                    previewSrc: null,

                    // Show image preview on thumbnail hover
                    previewImage(src) {
                        this.previewSrc = src;
                    },

                    // Reset preview when leaving thumbnail
                    resetPreview() {
                        this.previewSrc = null;
                    },

                    // Open lightbox with the given image source
                    openLightbox(src) {
                        this.$dispatch('open-lightbox', { src: src });
                    }
                };
            }
        </script>
    @endpush
</div>
