<div>
    <!-- Top Header -->
    <div class="text-white py-1 topheader">
        <div class="container d-flex justify-content-between align-items-center flex-wrap">
            <div class="contact-info">
                <strong>Contact Us Today!</strong>
                <span>866-468-7487 (866-GOT-PITS) or Outside The USA: 001+ 707-262-1394</span>
            </div>
            <div class="language-selector d-none d-md-block d-flex align-items-center" wire:ignore>
                <span class="translate-label me-2">Translate:</span>
                <div id="google_translate_element"></div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="main-header navbar navbar-expand-xl">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ url('/') }}" wire:navigate>
                <img src="{{ asset('images/home/logo-white.png') }}" alt="Apricot Power">
            </a>

            <!-- Mobile Cart Icon -->
            <a href="{{ route('cart.view') }}" class="header-link cart-link-mobile d-xl-none" wire:navigate>
                <div class="icon-wrapper">
                    <img src="{{ asset('images/home/cart-icon.png') }}" alt="Cart" class="icon-img">
                    @if($this->cartCount > 0)
                        <span class="cart-badge">{{ $this->cartCount }}</span>
                    @endif
                </div>
                <span class="icon-text text-white">CART</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbar-nav" aria-controls="navbar-nav"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <!-- Main Nav -->
            <div class="collapse navbar-collapse" id="navbar-nav">
                <ul class="navbar-nav me-auto main-navbar-links">
                    <!-- Products Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            PRODUCTS
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="productsDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('store') }}" wire:navigate>All Products</a>
                            </li>
                            @foreach ($this->categories as $category)
                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('store', ['categories' => [$category->id]]) }}"
                                       wire:navigate>
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                    <!-- Retail Locations (hidden) -->
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('retailer-locations') }}" wire:navigate>RETAIL LOCATIONS</a>
                    </li> --}}

                    <!-- Blogs Dropdown -->
                    <!-- Blogs Mega Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ route('blogs') }}" id="blogsDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            BLOGS
                        </a>
                        <div class="dropdown-menu blog-mega-menu p-0" aria-labelledby="blogsDropdown">
                            @if($this->navFeaturedBlogs->isNotEmpty())
                                <div class="blog-mega-header px-3 py-2">
                                    <span>Latest Articles</span>
                                </div>
                                <div class="blog-mega-grid">
                                    @foreach ($this->navFeaturedBlogs as $blogPost)
                                        <a href="{{ route('blog.detail', $blogPost->slug) }}" wire:navigate class="blog-mega-item">
                                            <div class="blog-mega-thumb">
                                                @if($blogPost->featured_image)
                                                    <img src="{{ asset('storage/' . $blogPost->featured_image) }}"
                                                         alt="{{ $blogPost->title }}">
                                                @else
                                                    <div class="blog-mega-thumb-placeholder">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#7cbf3d" viewBox="0 0 16 16">
                                                            <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z"/>
                                                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="blog-mega-info">
                                                <div class="blog-mega-title">{{ Str::limit($blogPost->title, 55) }}</div>
                                                <div class="blog-mega-meta">
                                                    @if($blogPost->category)
                                                        <span class="blog-mega-category">{{ $blogPost->category->name }}</span>
                                                    @endif
                                                    @if($blogPost->published_at)
                                                        <span>{{ $blogPost->published_at->format('M j, Y') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="blog-mega-footer">
                                <a href="{{ route('blogs') }}" wire:navigate>
                                    View All Blog Posts &rarr;
                                </a>
                            </div>
                        </div>
                    </li>

                    <style>
                        .blog-mega-menu {
                            width: 480px;
                            border: none;
                            border-radius: 10px;
                            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
                            overflow: hidden;
                        }
                        .blog-mega-header {
                            background: #f8f9fa;
                            border-bottom: 1px solid #e8e8e8;
                            font-size: 11px;
                            font-weight: 700;
                            text-transform: uppercase;
                            letter-spacing: 0.8px;
                            color: #888;
                        }
                        .blog-mega-grid {
                            padding: 8px 0;
                            max-height: 360px;
                            overflow-y: auto;
                        }
                        .blog-mega-item {
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            padding: 8px 14px;
                            text-decoration: none;
                            color: #333;
                            transition: background 0.15s ease;
                        }
                        .blog-mega-item:hover {
                            background: #f0f7e6;
                            color: #333;
                            text-decoration: none;
                        }
                        .blog-mega-thumb {
                            width: 58px;
                            height: 44px;
                            border-radius: 6px;
                            overflow: hidden;
                            flex-shrink: 0;
                            background: #f0f0f0;
                        }
                        .blog-mega-thumb img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                        }
                        .blog-mega-thumb-placeholder {
                            width: 100%;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            background: #e8f5e9;
                        }
                        .blog-mega-title {
                            font-size: 13px;
                            font-weight: 600;
                            line-height: 1.3;
                            color: #222;
                        }
                        .blog-mega-item:hover .blog-mega-title {
                            color: #7cbf3d;
                        }
                        .blog-mega-meta {
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            margin-top: 3px;
                            font-size: 11px;
                            color: #888;
                        }
                        .blog-mega-category {
                            background: #e8f5e9;
                            color: #2e7d32;
                            padding: 1px 6px;
                            border-radius: 8px;
                            font-weight: 600;
                        }
                        .blog-mega-footer {
                            border-top: 1px solid #e8e8e8;
                            padding: 10px 14px;
                            background: #f8f9fa;
                        }
                        .blog-mega-footer a {
                            font-size: 13px;
                            font-weight: 700;
                            color: #7cbf3d;
                            text-decoration: none;
                        }
                        .blog-mega-footer a:hover {
                            color: #5a9a2a;
                            text-decoration: underline;
                        }
                    </style>

                    <!-- About Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            ABOUT
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('contact') }}" wire:navigate>Contact / About Us</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://apricotpower.ositracker.com/myrefer" target="_blank" rel="noopener noreferrer">Affiliate Program</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://form.jotform.com/200975742179161" target="_blank" rel="noopener noreferrer">Carry Our Products</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('faq') }}" wire:navigate>Common Questions (FAQ)</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('reviews') }}" wire:navigate>Reviews</a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <!-- Search Form -->
                <form class="header-form header-formnew" action="{{ route('search.view') }}" method="GET">
                    <input name="term" type="search" class="form-control"
                           placeholder="Search" value="{{ $this->term }}">
                    <button type="submit" class="header-form-btn">
                        <img src="{{ asset('images/home/search-icon.png') }}" alt="Search">
                    </button>
                </form>

                <!-- User Section -->
                <div class="ms-3 navbar-icons navbar-nav d-flex align-items-center">
                    @guest('customer')
                        <!-- Not Logged In -->
                        <div class="user-section2 user-left d-flex align-items-center gap-3">
                            <a href="{{ route('login') }}" class="header-link" wire:navigate>
                                <img src="{{ asset('images/home/user-icon.png') }}" alt="User" class="login-icon">
                                <span class="icon-text text-login text-dark">LOG IN</span>
                            </a>

                            <a href="{{ route('cart.view') }}" class="header-link cart-link d-none d-xl-flex" wire:navigate>
                                <div class="icon-wrapper">
                                    <img src="{{ asset('images/home/cart-icon.png') }}" alt="Cart">
                                    @if($this->cartCount > 0)
                                        <span class="cart-badge">{{ $this->cartCount }}</span>
                                    @endif
                                </div>
                                <span class="icon-text text-dark">CART</span>
                            </a>
                        </div>
                    @else
                        <!-- Logged In -->
                        <div class="user-section d-flex align-items-center gap-2">
                            <a href="{{ route('cart.view') }}" class="header-link d-none d-xl-flex" wire:navigate>
                                <img src="{{ asset('images/home/cart-icon.png') }}" alt="Cart">
                                @if($this->cartCount > 0)
                                    <span class="cart-count">{{ $this->cartCount }}</span>
                                @endif
                            </a>

                            <div class="dropdown">
                                <button class="btn btn-link dropdown-toggle p-0 text-dark" type="button"
                                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                    </svg>
                                    <span class="user-name ms-1">{{ auth('customer')->user()->first_name ?? 'Account' }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('order-history.view') }}" wire:navigate>Order History</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('basic-info') }}" wire:navigate>Basic Info</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('account-details') }}" wire:navigate>Account Details</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('email-preferences') }}" wire:navigate>Email Preferences</a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
</div>
