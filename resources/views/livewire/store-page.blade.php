<div class="store-page home-page">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-utilities.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
        <style>
            /* Store Header Banner */
            .store-header-banner {
                background: linear-gradient(135deg, #2d5a27 0%, #3d7a34 100%);
                padding: 50px 0;
                margin-bottom: 0;
                position: relative;
                z-index: 1;
            }

            .store-header-banner h1 {
                color: #fff;
                font-size: 2.5rem;
                font-weight: 700;
                margin: 0 0 10px 0;
            }

            .store-header-banner p {
                color: rgba(255, 255, 255, 0.7);
                font-size: 1.1rem;
                margin: 0;
            }

            /* Amazon-style Store Layout */
            .store-container {
                display: flex;
                gap: 30px;
                max-width: 1400px;
                margin: 0 auto;
                padding: 20px;
            }

            /* Sidebar Styles */
            .store-sidebar {
                width: 280px;
                flex-shrink: 0;
            }

            .store-main {
                flex: 1;
                min-width: 0;
            }

            .filter-card {
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                margin-bottom: 20px;
                overflow: hidden;
            }

            .filter-card-header {
                background: #f8f9fa;
                padding: 12px 15px;
                font-weight: 700;
                font-size: 14px;
                color: #333;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .filter-card-header .clear-btn {
                font-size: 12px;
                color: #7cbf3d;
                cursor: pointer;
                font-weight: 500;
            }

            .filter-card-header .clear-btn:hover {
                text-decoration: underline;
            }

            .filter-card-body {
                padding: 15px;
                max-height: 300px;
                overflow-y: auto;
            }

            .filter-item {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
                cursor: pointer;
            }

            .filter-item:last-child {
                margin-bottom: 0;
            }

            .filter-checkbox {
                width: 18px;
                height: 18px;
                border: 2px solid #ccc;
                border-radius: 3px;
                margin-right: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
                flex-shrink: 0;
            }

            .filter-item:hover .filter-checkbox {
                border-color: #7cbf3d;
            }

            .filter-item.active .filter-checkbox {
                background: #7cbf3d;
                border-color: #7cbf3d;
            }

            .filter-item.active .filter-checkbox::after {
                content: '\2713';
                color: #fff;
                font-size: 12px;
                font-weight: bold;
            }

            .filter-label {
                font-size: 14px;
                color: #333;
                flex: 1;
            }

            .filter-count {
                font-size: 12px;
                color: #888;
                margin-left: 5px;
            }

            /* Search Box */
            .search-box {
                position: relative;
                margin-bottom: 20px;
            }

            .search-box input {
                width: 100%;
                padding: 12px 40px 12px 15px;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                font-size: 14px;
                transition: border-color 0.2s ease;
            }

            .search-box input:focus {
                border-color: #7cbf3d;
                outline: none;
            }

            .search-box .search-icon {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                color: #888;
            }

            /* Active Filters */
            .active-filters {
                background: #f0f7e6;
                border: 1px solid #7cbf3d;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }

            .active-filters-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }

            .active-filters-title {
                font-weight: 600;
                font-size: 14px;
                color: #333;
            }

            .clear-all-btn {
                font-size: 12px;
                color: #dc3545;
                cursor: pointer;
                font-weight: 500;
            }

            .clear-all-btn:hover {
                text-decoration: underline;
            }

            .filter-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .filter-tag {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 5px 10px;
                background: #fff;
                border: 1px solid #7cbf3d;
                border-radius: 15px;
                font-size: 12px;
                color: #333;
            }

            .filter-tag .remove-tag {
                cursor: pointer;
                color: #dc3545;
                font-weight: bold;
            }

            /* Results Header */
            .results-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #e0e0e0;
            }

            .results-count {
                font-size: 14px;
                color: #666;
            }

            .results-count strong {
                color: #333;
            }

            .sort-dropdown {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .sort-dropdown label {
                font-size: 14px;
                color: #666;
                white-space: nowrap;
            }

            .sort-dropdown select {
                padding: 8px 30px 8px 12px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 14px;
                background: #fff;
                cursor: pointer;
            }

            .sort-dropdown select:focus {
                border-color: #7cbf3d;
                outline: none;
            }

            /* Product Grid */
            .products-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }

            /* Product Card */
            .product-card {
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                overflow: hidden;
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
            }

            .product-card:hover {
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .product-card-image {
                position: relative;
                padding: 15px;
                background: #fafafa;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 200px;
            }

            .product-card-image img {
                max-width: 100%;
                max-height: 170px;
                object-fit: contain;
            }

            .product-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: 600;
            }

            .badge-sale {
                background: #dc3545;
                color: #fff;
            }

            .badge-new {
                background: #28a745;
                color: #fff;
            }

            .product-card-content {
                padding: 15px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .product-card-title {
                font-size: 14px;
                font-weight: 600;
                color: #0066c0;
                margin-bottom: 5px;
                line-height: 1.4;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .product-card-title a {
                color: inherit;
                text-decoration: none;
            }

            .product-card-title a:hover {
                color: #c45500;
                text-decoration: underline;
            }

            .product-card-subtitle {
                font-size: 12px;
                color: #666;
                margin-bottom: 8px;
            }

            /* Product Tags Display */
            .product-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 4px;
                margin-bottom: 8px;
            }

            .product-tag {
                font-size: 10px;
                padding: 2px 6px;
                background: #e8f5e9;
                color: #2e7d32;
                border-radius: 3px;
            }

            /* Rating */
            .product-rating {
                display: flex;
                align-items: center;
                gap: 5px;
                margin-bottom: 8px;
            }

            .stars {
                display: flex;
                color: #ff9800;
            }

            .stars i {
                font-size: 12px;
            }

            .stars i.empty {
                color: #ddd;
            }

            .rating-count {
                font-size: 12px;
                color: #007185;
            }

            /* Pricing */
            .product-pricing {
                margin-bottom: 10px;
            }

            .price-row {
                display: flex;
                align-items: baseline;
                gap: 8px;
                flex-wrap: wrap;
            }

            .current-price {
                font-size: 18px;
                font-weight: 400;
                color: #0f1111;
            }

            .current-price sup {
                font-size: 12px;
                top: -0.5em;
            }

            .compare-price {
                font-size: 13px;
                color: #565959;
                text-decoration: line-through;
            }

            .savings-text {
                font-size: 12px;
                color: #cc0c39;
                font-weight: 500;
            }

            /* Action Buttons */
            .product-actions {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-top: auto;
            }

            .btn-add-cart {
                width: 100%;
                padding: 8px 15px;
                background: #ffd814;
                border: 1px solid #fcd200;
                border-radius: 20px;
                color: #0f1111;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .btn-add-cart:hover {
                background: #f7ca00;
            }

            .btn-buy-now {
                width: 100%;
                padding: 8px 15px;
                background: #ffa41c;
                border: 1px solid #ff8f00;
                border-radius: 20px;
                color: #0f1111;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .btn-buy-now:hover {
                background: #fa8900;
            }

            .btn-disabled {
                background: #e0e0e0 !important;
                border-color: #ccc !important;
                color: #888 !important;
                cursor: not-allowed !important;
            }

            /* Status Badge */
            .status-badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                margin-bottom: 8px;
            }

            .status-published {
                background: #e8f5e9;
                color: #2e7d32;
            }

            .status-draft {
                background: #fff3e0;
                color: #e65100;
            }

            /* Mobile Filter Toggle */
            .mobile-filter-toggle {
                display: none;
                width: 100%;
                padding: 12px 20px;
                background: #7cbf3d;
                border: none;
                border-radius: 8px;
                color: #fff;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                margin-bottom: 20px;
            }

            .mobile-filter-toggle i {
                margin-right: 8px;
            }

            /* No Products */
            .no-products {
                text-align: center;
                padding: 60px 20px;
                background: #f8f9fa;
                border-radius: 10px;
            }

            .no-products i {
                font-size: 60px;
                color: #ccc;
                margin-bottom: 20px;
            }

            /* Responsive */
            @media (max-width: 1200px) {
                .products-grid {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 992px) {
                .store-sidebar {
                    width: 250px;
                }
            }

            @media (max-width: 768px) {
                .store-container {
                    flex-direction: column;
                }

                .store-sidebar {
                    width: 100%;
                    display: none;
                }

                .store-sidebar.show {
                    display: block;
                }

                .mobile-filter-toggle {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .products-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 15px;
                }

                .results-header {
                    flex-direction: column;
                    gap: 15px;
                    align-items: flex-start;
                }
            }

            @media (max-width: 576px) {
                .products-grid {
                    grid-template-columns: 1fr;
                }

                .product-card-image {
                    min-height: 180px;
                }
            }
        </style>
    @endpush

    {{-- Header Section --}}
    <section class="store-header-banner">
        <div class="container text-center">
            <h1>
                @if($selectedCategory)
                    @php
                        $cat = $this->categories->firstWhere('id', $selectedCategory);
                    @endphp
                    {{ $cat ? $cat->name : 'Products' }}
                @else
                    All Products
                @endif
            </h1>
            <p>Discover our premium selection of health products</p>
        </div>
    </section>

    {{-- Main Content --}}
    <div class="store-container">
        {{-- Mobile Filter Toggle --}}
        <button class="mobile-filter-toggle" onclick="document.querySelector('.store-sidebar').classList.toggle('show')">
            <i class="bi bi-funnel"></i>
            Filters @if($this->activeFilterCount > 0)<span class="badge bg-white text-dark ms-2">{{ $this->activeFilterCount }}</span>@endif
        </button>

        {{-- Sidebar --}}
        <aside class="store-sidebar">
            {{-- Search Box --}}
            <div class="search-box">
                <input type="text"
                       wire:model.live.debounce.300ms="searchQuery"
                       placeholder="Search products..."
                       aria-label="Search products">
                <i class="bi bi-search search-icon"></i>
            </div>

            {{-- Active Filters --}}
            @if($this->activeFilterCount > 0)
                <div class="active-filters">
                    <div class="active-filters-header">
                        <span class="active-filters-title">Active Filters ({{ $this->activeFilterCount }})</span>
                        <span class="clear-all-btn" wire:click="clearFilters">Clear All</span>
                    </div>
                    <div class="filter-tags">
                        @if($selectedCategory)
                            @php $cat = $this->categories->firstWhere('id', $selectedCategory); @endphp
                            @if($cat)
                                <span class="filter-tag">
                                    {{ $cat->name }}
                                    <span class="remove-tag" wire:click="selectCategory(null)">&times;</span>
                                </span>
                            @endif
                        @endif
                        @foreach($selectedTags as $tagId)
                            @php $tag = $this->tags->firstWhere('id', $tagId); @endphp
                            @if($tag)
                                <span class="filter-tag">
                                    {{ $tag->value }}
                                    <span class="remove-tag" wire:click="toggleTag({{ $tagId }})">&times;</span>
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Categories Filter --}}
            @if($this->categories->count())
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span>Category</span>
                        @if($selectedCategory)
                            <span class="clear-btn" wire:click="clearCategoryFilter">Clear</span>
                        @endif
                    </div>
                    <div class="filter-card-body">
                        @foreach($this->categories as $category)
                            <div class="filter-item {{ $selectedCategory === $category->id ? 'active' : '' }}"
                                 wire:click="selectCategory({{ $category->id }})">
                                <div class="filter-checkbox"></div>
                                <span class="filter-label">{{ $category->name }}</span>
                                <span class="filter-count">({{ $category->products_count }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tags Filter --}}
            @if($this->tags->count())
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span>Product Tags</span>
                        @if(count($selectedTags) > 0)
                            <span class="clear-btn" wire:click="clearTagFilters">Clear</span>
                        @endif
                    </div>
                    <div class="filter-card-body">
                        @foreach($this->tags as $tag)
                            <div class="filter-item {{ in_array($tag->id, $selectedTags) ? 'active' : '' }}"
                                 wire:click="toggleTag({{ $tag->id }})">
                                <div class="filter-checkbox"></div>
                                <span class="filter-label">{{ $tag->value }}</span>
                                <span class="filter-count">({{ $tag->products_count }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Status Filter --}}
            <div class="filter-card">
                <div class="filter-card-header">
                    <span>Status</span>
                </div>
                <div class="filter-card-body">
                    <div class="filter-item {{ $status === 'published' ? 'active' : '' }}"
                         wire:click="setStatus('published')">
                        <div class="filter-checkbox"></div>
                        <span class="filter-label">Published</span>
                    </div>
                    <div class="filter-item {{ $status === 'draft' ? 'active' : '' }}"
                         wire:click="setStatus('draft')">
                        <div class="filter-checkbox"></div>
                        <span class="filter-label">Draft</span>
                    </div>
                    <div class="filter-item {{ $status === 'all' ? 'active' : '' }}"
                         wire:click="setStatus('all')">
                        <div class="filter-checkbox"></div>
                        <span class="filter-label">All</span>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <main class="store-main">
            {{-- Results Header --}}
            <div class="results-header">
                <div class="results-count">
                    Showing <strong>{{ $this->products->firstItem() ?? 0 }}-{{ $this->products->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $this->products->total() }}</strong> results
                    @if(!empty($searchQuery))
                        for "<strong>{{ $searchQuery }}</strong>"
                    @endif
                </div>
                <div class="sort-dropdown">
                    <label for="sortBy">Sort by:</label>
                    <select id="sortBy" wire:model.live="sortBy">
                        <option value="default">Featured</option>
                        <option value="newest">Newest Arrivals</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="name_asc">Name: A to Z</option>
                        <option value="name_desc">Name: Z to A</option>
                        <option value="best_selling">Best Sellers</option>
                    </select>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="products-grid">
                @forelse($this->products as $product)
                    @php
                        $variant = $product->variants->first();
                        $basePrice = $variant?->basePrices->first();
                        $hasDiscount = $basePrice?->compare_price && $basePrice->compare_price->value > $basePrice->price->value;
                        $savingsPercent = $hasDiscount ? round((($basePrice->compare_price->value - $basePrice->price->value) / $basePrice->compare_price->value) * 100) : 0;
                        $savingsAmount = $hasDiscount ? ($basePrice->compare_price->value - $basePrice->price->value) / 100 : 0;
                        $priceWhole = $basePrice ? floor($basePrice->price->value / 100) : 0;
                        $priceCents = $basePrice ? ($basePrice->price->value % 100) : 0;
                    @endphp

                    <div class="product-card">
                        {{-- Product Image --}}
                        <div class="product-card-image">
                            @if($hasDiscount && $savingsPercent > 0)
                                <span class="product-badge badge-sale">{{ $savingsPercent }}% OFF</span>
                            @endif

                            @if($product->defaultUrl)
                                <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                                    @if($product->thumbnail)
                                        <img src="{{ $product->thumbnail->getUrl('medium') }}"
                                             alt="{{ $product->translateAttribute('name') }}"
                                             loading="lazy">
                                    @else
                                        <div style="width: 150px; height: 150px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-image" style="font-size: 40px; color: #ccc;"></i>
                                        </div>
                                    @endif
                                </a>
                            @endif
                        </div>

                        {{-- Product Content --}}
                        <div class="product-card-content">
                            {{-- Status Badge --}}
                            <span class="status-badge {{ $product->status === 'published' ? 'status-published' : 'status-draft' }}">
                                {{ $product->status }}
                            </span>

                            {{-- Title --}}
                            <h3 class="product-card-title">
                                @if($product->defaultUrl)
                                    <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                                        {{ $product->translateAttribute('name') }}
                                    </a>
                                @else
                                    {{ $product->translateAttribute('name') }}
                                @endif
                            </h3>

                            {{-- Subtitle --}}
                            @if($product->translateAttribute('product_descriptor'))
                                <p class="product-card-subtitle">
                                    {{ Str::limit($product->translateAttribute('product_descriptor'), 60) }}
                                </p>
                            @endif

                            {{-- Product Tags --}}
                            @if($product->tags->count())
                                <div class="product-tags">
                                    @foreach($product->tags->take(3) as $tag)
                                        <span class="product-tag">{{ $tag->value }}</span>
                                    @endforeach
                                    @if($product->tags->count() > 3)
                                        <span class="product-tag">+{{ $product->tags->count() - 3 }}</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Rating --}}
                            <div class="product-rating">
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill {{ $i <= 4 ? '' : 'empty' }}"></i>
                                    @endfor
                                </div>
                                <span class="rating-count">(0)</span>
                                @if($variant?->sku)
                                    <div class="feefo-product-stars-widget d-none" data-product-sku="{{ $variant->sku }}"></div>
                                @endif
                            </div>

                            {{-- Pricing --}}
                            <div class="product-pricing">
                                @if($basePrice)
                                    <div class="price-row">
                                        <span class="current-price">
                                            ${{ $priceWhole }}<sup>{{ str_pad($priceCents, 2, '0', STR_PAD_LEFT) }}</sup>
                                        </span>
                                        @if($hasDiscount)
                                            <span class="compare-price">${{ number_format($basePrice->compare_price->value / 100, 2) }}</span>
                                        @endif
                                    </div>
                                    @if($hasDiscount)
                                        <div class="savings-text">
                                            Save ${{ number_format($savingsAmount, 2) }} ({{ $savingsPercent }}% off)
                                        </div>
                                    @endif
                                @else
                                    <span class="current-price">Price unavailable</span>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="product-actions">
                                @if($variant && $product->status === 'published')
                                    <button class="btn-add-cart"
                                            wire:click="addToCart({{ $variant->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="addToCart({{ $variant->id }})">
                                        <span wire:loading.remove wire:target="addToCart({{ $variant->id }})">
                                            Add to Cart
                                        </span>
                                        <span wire:loading wire:target="addToCart({{ $variant->id }})">
                                            Adding...
                                        </span>
                                    </button>
                                    <button class="btn-buy-now"
                                            wire:click="buyNow({{ $variant->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="buyNow({{ $variant->id }})">
                                        <span wire:loading.remove wire:target="buyNow({{ $variant->id }})">
                                            Buy Now
                                        </span>
                                        <span wire:loading wire:target="buyNow({{ $variant->id }})">
                                            Processing...
                                        </span>
                                    </button>
                                @else
                                    <button class="btn-add-cart btn-disabled" disabled>
                                        Not Available
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="no-products" style="grid-column: 1 / -1;">
                        <i class="bi bi-box-seam"></i>
                        <h4>No products found</h4>
                        <p class="text-muted">
                            @if(!empty($searchQuery))
                                No products match your search "{{ $searchQuery }}".
                            @elseif($this->activeFilterCount > 0)
                                No products match your selected filters.
                                <br><a href="#" wire:click.prevent="clearFilters" style="color: #7cbf3d;">Clear all filters</a>
                            @else
                                Check back soon for new products.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($this->products->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $this->products->links() }}
                </div>
            @endif
        </main>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="https://api.feefo.com/api/javascript/apricot-power" async></script>
    @endpush
</div>
