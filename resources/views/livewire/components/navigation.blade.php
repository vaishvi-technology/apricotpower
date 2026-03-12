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
                    <li class="nav-item dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button"
                           @click.prevent="open = !open" aria-expanded="false">
                            PRODUCTS
                        </a>
                        <ul class="dropdown-menu" :class="{ 'show': open }" aria-labelledby="productsDropdown">
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
                    <li class="nav-item dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <a class="nav-link dropdown-toggle" href="{{ route('blogs') }}" id="blogsDropdown" role="button"
                           @click.prevent="open = !open" aria-expanded="false">
                            BLOGS
                        </a>
                        <div class="dropdown-menu blog-mega-menu p-0" :class="{ 'show': open }" aria-labelledby="blogsDropdown">
                            @if($this->navFeaturedBlogs->isNotEmpty())
                                <div class="blog-mega-header px-3 py-2">
                                    <span>Latest Articles</span>
                                </div>
                                <div class="blog-mega-grid">
                                    @foreach ($this->navFeaturedBlogs as $blogPost)
                                        <a href="{{ route('blog.detail', $blogPost->slug) }}" wire:navigate class="blog-mega-item">
                                            <div class="blog-mega-info">
                                                <div class="blog-mega-title">{{ Str::limit($blogPost->title, 45) }}</div>
                                                <div class="blog-mega-meta">
                                                    @foreach($blogPost->categories->take(2) as $cat)
                                                        <span class="blog-mega-category" style="background: {{ $cat->accent_color ?? '#7cbf3d' }}; color: #fff; padding: 1px 8px; border-radius: 10px; font-size: 10px;">{{ $cat->name }}</span>
                                                    @endforeach
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
                            width: 320px;
                            border: none;
                            border-radius: 8px;
                            box-shadow: 0 6px 20px rgba(0,0,0,0.13);
                            overflow: hidden;
                        }
                        .blog-mega-header {
                            background: #f8f9fa;
                            border-bottom: 1px solid #e8e8e8;
                            font-size: 10px;
                            font-weight: 700;
                            text-transform: uppercase;
                            letter-spacing: 0.8px;
                            color: #888;
                            padding: 6px 12px;
                        }
                        .blog-mega-grid {
                            padding: 4px 0;
                            max-height: 300px;
                            overflow-y: auto;
                        }
                        .blog-mega-item {
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            padding: 6px 12px;
                            text-decoration: none;
                            color: #333;
                            transition: background 0.15s ease;
                        }
                        .blog-mega-item:hover {
                            background: #f0f7e6;
                            color: #333;
                            text-decoration: none;
                        }
                        .blog-mega-title {
                            font-size: 12px;
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
                            gap: 5px;
                            margin-top: 2px;
                            font-size: 10px;
                            color: #888;
                        }
                        .blog-mega-category {
                            background: #e8f5e9;
                            color: #2e7d32;
                            padding: 1px 5px;
                            border-radius: 8px;
                            font-weight: 600;
                        }
                        .blog-mega-footer {
                            border-top: 1px solid #e8e8e8;
                            padding: 8px 12px;
                            background: #f8f9fa;
                        }
                        .blog-mega-footer a {
                            font-size: 12px;
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
                    <li class="nav-item dropdown" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button"
                           @click.prevent="open = !open" aria-expanded="false">
                            ABOUT
                        </a>
                        <ul class="dropdown-menu" :class="{ 'show': open }" aria-labelledby="aboutDropdown">
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

                            <div class="dropdown" x-data="{ open: false }" @click.outside="open = false">
                                <button class="btn btn-link dropdown-toggle p-0 text-dark" type="button"
                                        id="userDropdown" @click="open = !open" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                    </svg>
                                    <span class="user-name ms-1">{{ auth('customer')->user()->first_name ?? 'Account' }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" :class="{ 'show': open }" aria-labelledby="userDropdown">
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
