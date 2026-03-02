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

                    <!-- Retail Locations -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('retailer-locations') }}" wire:navigate>RETAIL LOCATIONS</a>
                    </li>

                    <!-- Blogs -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('blogs') }}" wire:navigate>BLOGS</a>
                    </li>

                    <!-- About Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="aboutDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            ABOUT
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('contact') }}" wire:navigate>Contact Us</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('reviews') }}" wire:navigate>Reviews</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('testimonial') }}" wire:navigate>Testimonials</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('wholesale') }}" wire:navigate>Carry Our Products</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://apricotpower.ositracker.com/myrefer" target="_blank" rel="noopener noreferrer">Affiliate Program</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('refer-friend') }}" wire:navigate>Refer A Friend</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('faq') }}" wire:navigate>Common Questions</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('seeds-recipes') }}" wire:navigate>Apricot Seed Recipes</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('superfood-recipes') }}" wire:navigate>B17 Superfood Recipes</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('lifestyle') }}" wire:navigate>Life Style</a>
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
