<div class="product-page home-page">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-utilities.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    @endpush

    {{-- Section 1: Inner Banner --}}
    <section class="inner-banner">
        <div class="inner-banner-head">
            <h3 class="bold-font text-white mb-0">{{ $this->product->translateAttribute('name') }}</h3>
        </div>
    </section>

    {{-- Section 2: Product Detail --}}
    <section class="fuel-product-sec">
        <div class="container">
            <div class="row">
                {{-- Left Column: Product Image --}}
                <div class="col-md-6">
                    <div class="productImage">
                        @if ($this->image)
                            <img src="{{ $this->image->getUrl('large') }}"
                                 alt="{{ $this->product->translateAttribute('name') }}" />
                        @endif
                    </div>
                </div>

                {{-- Right Column: Product Info --}}
                <div class="col-md-6">
                    <div class="productInfo">
                        <h3 class="text-theme mb-2">{{ $this->product->translateAttribute('name') }}</h3>

                        @if ($this->product->translateAttribute('product_descriptor'))
                            <h5 class="mb-2" style="color: #565656;">{{ $this->product->translateAttribute('product_descriptor') }}</h5>
                        @endif

                        @if ($this->product->translateAttribute('quantity_size'))
                            <h6 class="mb-2" style="color: #888;">Size: {{ $this->product->translateAttribute('quantity_size') }}</h6>
                        @endif

                        {{-- Price --}}
                        @if ($this->basePrice)
                            <div class="hot-buys-item-content-price mb-2">
                                <h4 class="mb-0">
                                    <span class="price">Price: </span>
                                    @if ($this->basePrice->compare_price && $this->basePrice->compare_price->value > $this->basePrice->price->value)
                                        <span class="actual-price" style="text-decoration: line-through;">{{ $this->basePrice->compare_price->formatted() }}</span>
                                    @endif
                                    <span class="discount-price secondary-color">{{ $this->basePrice->price->formatted() }}</span>
                                </h4>
                            </div>
                        @endif

                        {{-- Feefo Stars --}}
                        @if ($this->variant->sku)
                            <div class="feefo-product-stars-widget mb-2" data-product-sku="{{ $this->variant->sku }}"></div>
                        @endif

                        {{-- You Save --}}
                        @if ($this->basePrice?->compare_price && $this->basePrice->compare_price->value > $this->basePrice->price->value)
                            <div class="hot-buys-item-description mb-2">
                                @php
                                    $savingsAmount = $this->basePrice->compare_price->value - $this->basePrice->price->value;
                                @endphp
                                <p class="mb-0" style="color: var(--secondary-color); font-weight: 600;">You Save ${{ number_format($savingsAmount / 100, 2) }}</p>
                            </div>
                        @endif

                        {{-- Variant Options --}}
                        @if ($this->productOptions->count())
                            <form class="mb-3">
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($this->productOptions as $option)
                                        <fieldset>
                                            <legend class="mb-1" style="font-size: 14px; font-weight: 600; color: #555;">
                                                {{ $option['option']->translate('name') }}
                                            </legend>
                                            <div class="d-flex flex-wrap gap-2"
                                                 x-data="{
                                                     selectedOption: @entangle('selectedOptionValues').live,
                                                     selectedValues: [],
                                                 }"
                                                 x-init="selectedValues = Object.values(selectedOption);
                                                 $watch('selectedOption', value =>
                                                     selectedValues = Object.values(selectedOption)
                                                 )">
                                                @foreach ($option['values'] as $value)
                                                    <button class="btn btn-sm"
                                                            type="button"
                                                            wire:click="$set('selectedOptionValues.{{ $option['option']->id }}', {{ $value->id }})"
                                                            :class="{
                                                                'btn-warning text-white': selectedValues.includes({{ $value->id }}),
                                                                'btn-outline-secondary': !selectedValues.includes({{ $value->id }})
                                                            }">
                                                        {{ $value->translate('name') }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </fieldset>
                                    @endforeach
                                </div>
                            </form>
                        @endif

                        {{-- Add to Cart Component --}}
                        <div class="mb-3">
                            <livewire:components.add-to-cart :purchasable="$this->variant"
                                                             :wire:key="$this->variant->id" />
                        </div>

                        {{-- Categories --}}
                        @if ($this->product->collections->count())
                            <div class="mb-2">
                                <strong style="font-size: 14px;">Categories: </strong>
                                <span class="cat-align">
                                    @foreach ($this->product->collections as $collection)
                                        <a href="{{ route('collection.view', $collection->defaultUrl->slug) }}"
                                           style="color: var(--primary-color); font-size: 14px;">
                                            {{ $collection->translateAttribute('name') }}@if (!$loop->last), @endif
                                        </a>
                                    @endforeach
                                </span>
                            </div>
                        @endif

                        {{-- Tags --}}
                        @if ($this->product->tags->count())
                            <div class="mb-2">
                                <strong style="font-size: 14px;">Tags: </strong>
                                @foreach ($this->product->tags as $tag)
                                    <span class="badge" style="background-color: var(--primary-color); color: #fff; font-size: 12px;">{{ $tag->value }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Other Sizes (Alternate Products) --}}
                        @if ($this->alternateProducts->count())
                            <div class="addons-section mt-3">
                                <h6 class="mb-2" style="font-weight: 600;">Other Sizes</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($this->alternateProducts as $altProduct)
                                        @if ($altProduct->defaultUrl)
                                            <a href="{{ route('product.view', $altProduct->defaultUrl->slug) }}" style="text-decoration: none;">
                                                <div class="addon-card text-center">
                                                    @if ($altProduct->thumbnail)
                                                        <img class="addon-img" src="{{ $altProduct->thumbnail->getUrl('small') }}" alt="{{ $altProduct->translateAttribute('name') }}">
                                                    @endif
                                                    <div class="addon-name mt-1">{{ $altProduct->translateAttribute('name') }}</div>
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SKU --}}
            <div class="row mt-3">
                <div class="col-12">
                    <h6 style="color: #888;">SKU: {{ $this->variant->sku }}</h6>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="border rounded mobile-screen" style="width: 100% !important;">
                        <ul class="nav nav-tabs" id="uncontrolled-tab-example" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="intro-tab" data-bs-toggle="tab"
                                        data-bs-target="#uncontrolled-tab-example-tabpane-Into" type="button"
                                        role="tab" aria-controls="uncontrolled-tab-example-tabpane-Into" aria-selected="true">
                                    Intro
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                                        data-bs-target="#uncontrolled-tab-example-tabpane-Reviews" type="button"
                                        role="tab" aria-controls="uncontrolled-tab-example-tabpane-Reviews" aria-selected="false">
                                    Reviews
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content p-3">
                            <div class="tab-pane fade show active" id="uncontrolled-tab-example-tabpane-Into"
                                 role="tabpanel" aria-labelledby="intro-tab">
                                <div class="responsive-html-content">
                                    {!! $this->product->translateAttribute('description') !!}
                                </div>
                            </div>
                            <div class="tab-pane fade" id="uncontrolled-tab-example-tabpane-Reviews"
                                 role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="feefo-review-widget-product" data-product-sku="{{ $this->variant->sku }}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Products --}}
            @if ($this->relatedProducts->count())
                <div class="row mt-5">
                    <div class="col-12">
                        <h2 class="RelatedProducts_title">Related Products</h2>
                    </div>
                </div>
                <div class="row">
                    @foreach ($this->relatedProducts as $relProduct)
                        @php
                            $relVariant = $relProduct->variants->first();
                            $relBasePrice = $relVariant?->basePrices->first();
                        @endphp
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="fuel-product-item" style="margin-top: 40px;">
                                <div class="fuel-product-item-top">
                                    <div class="fuel-product-img">
                                        @if ($relProduct->defaultUrl)
                                            <a href="{{ route('product.view', $relProduct->defaultUrl->slug) }}">
                                                @if ($relProduct->thumbnail)
                                                    <img src="{{ $relProduct->thumbnail->getUrl('medium') }}"
                                                         alt="{{ $relProduct->translateAttribute('name') }}"
                                                         style="width: 250px !important; height: 200px !important; object-fit: contain;">
                                                @endif
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="fuel-product-item-content">
                                    <h3>
                                        @if ($relProduct->defaultUrl)
                                            <a href="{{ route('product.view', $relProduct->defaultUrl->slug) }}" style="text-decoration: none; color: inherit;">
                                                {{ $relProduct->translateAttribute('name') }}
                                            </a>
                                        @else
                                            {{ $relProduct->translateAttribute('name') }}
                                        @endif
                                    </h3>
                                    <div class="fuel-product-item-content-price">
                                        @if ($relBasePrice)
                                            @if ($relBasePrice->compare_price && $relBasePrice->compare_price->value > $relBasePrice->price->value)
                                                <span class="actual-price" style="text-decoration: line-through;">{{ $relBasePrice->compare_price->formatted() }}</span>
                                            @endif
                                            <span class="discount-price" style="color: #dc9d03;">{{ $relBasePrice->price->formatted() }}</span>
                                        @endif
                                    </div>
                                    @if ($relBasePrice?->compare_price && $relBasePrice->compare_price->value > $relBasePrice->price->value)
                                        <div class="hot-buys-item-description">
                                            @php
                                                $relSavings = $relBasePrice->compare_price->value - $relBasePrice->price->value;
                                            @endphp
                                            <p class="mb-0">You Save ${{ number_format($relSavings / 100, 2) }}</p>
                                        </div>
                                    @endif
                                    @if ($relVariant?->sku)
                                        <div class="feefo-product-stars-widget" data-product-sku="{{ $relVariant->sku }}"></div>
                                    @endif
                                    <div class="d-flex justify-content-between w-100 mt-2 mb-3">
                                        @if ($relProduct->defaultUrl)
                                            <a href="{{ route('product.view', $relProduct->defaultUrl->slug) }}" class="add-to-cart-btn" style="text-decoration: none; text-align: center;">Buy Now</a>
                                            <a href="{{ route('product.view', $relProduct->defaultUrl->slug) }}" class="add-to-cart-btn" style="text-decoration: none; text-align: center;">Add To Cart</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="https://api.feefo.com/api/javascript/apricot-power" async></script>
    @endpush
</div>
