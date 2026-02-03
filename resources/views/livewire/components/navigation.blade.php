<nav class="main-header navbar navbar-expand-xl">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}" wire:navigate>
            <img src="{{ asset('images/home/logo-white.png') }}" alt="Apricot Power">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbar-nav" aria-controls="navbar-nav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar-nav">
            <ul class="navbar-nav main-navbar-links mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}" wire:navigate>Home</a>
                </li>
                @foreach ($this->collections as $collection)
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('collection.view', $collection->defaultUrl->slug) }}"
                           wire:navigate>
                            {{ $collection->translateAttribute('name') }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <form class="header-form header-formnew" action="{{ route('search.view') }}">
                <button type="submit" class="header-form-btn">
                    <img src="{{ asset('images/home/search-icon.png') }}" alt="Search">
                </button>
                <input name="term" type="search" class="form-control"
                       placeholder="Search products..." value="{{ $this->term }}">
            </form>

            <div class="ms-3 navbar-icons navbar-nav d-flex align-items-center gap-2">
                @livewire('components.cart')
            </div>
        </div>
    </div>
</nav>
