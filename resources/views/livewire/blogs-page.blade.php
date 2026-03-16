<div class="store-page home-page">
    @push('styles')
        <style>
            .store-header-banner {
                background: linear-gradient(135deg, #2d5a27 0%, #3d7a34 100%);
                padding: 140px 0 50px;
                margin-bottom: 0;
            }
            .store-header-banner h1 { color: #fff; font-size: 2.5rem; font-weight: 700; margin: 0 0 10px 0; }
            .store-header-banner p { color: rgba(255,255,255,0.7); font-size: 1.1rem; margin: 0; }

            .store-container { display: flex; gap: 30px; max-width: 1400px; margin: 0 auto; padding: 20px; }
            .store-sidebar { width: 280px; flex-shrink: 0; }
            .store-main { flex: 1; min-width: 0; }

            .filter-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px; overflow: hidden; }
            .filter-card-header { background: #f8f9fa; padding: 12px 15px; font-weight: 700; font-size: 14px; color: #333; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center; }
            .filter-card-header .clear-btn { font-size: 12px; color: #7cbf3d; cursor: pointer; font-weight: 500; }
            .filter-card-header .clear-btn:hover { text-decoration: underline; }
            .filter-card-body { padding: 15px; max-height: 300px; overflow-y: auto; }

            .filter-item { display: flex; align-items: center; margin-bottom: 10px; cursor: pointer; }
            .filter-item:last-child { margin-bottom: 0; }
            .filter-checkbox { width: 18px; height: 18px; border: 2px solid #ccc; border-radius: 3px; margin-right: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; flex-shrink: 0; }
            .filter-item:hover .filter-checkbox { border-color: #7cbf3d; }
            .filter-item.active .filter-checkbox { background: #7cbf3d; border-color: #7cbf3d; }
            .filter-item.active .filter-checkbox::after { content: '\2713'; color: #fff; font-size: 12px; font-weight: bold; }
            .filter-label { font-size: 14px; color: #333; flex: 1; }
            .filter-count { font-size: 12px; color: #888; margin-left: 5px; }

            .results-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; }
            .results-count { font-size: 14px; color: #666; }
            .results-count strong { color: #333; }
            .sort-dropdown { display: flex; align-items: center; gap: 10px; }
            .sort-dropdown label { font-size: 14px; color: #666; white-space: nowrap; }
            .sort-dropdown select { padding: 8px 30px 8px 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; background: #fff; cursor: pointer; }
            .sort-dropdown select:focus { border-color: #7cbf3d; outline: none; }

            /* Blog Grid */
            .blog-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

            /* Blog Card */
            .blog-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column; }
            .blog-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); transform: translateY(-2px); }
            .blog-card-image { position: relative; height: 200px; overflow: hidden; background: #f0f0f0; }
            .blog-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
            .blog-card:hover .blog-card-image img { transform: scale(1.05); }
            .blog-card-image .no-image { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%); }
            .blog-card-image .no-image i { font-size: 48px; color: #7cbf3d; }

            .blog-card-title { padding: 12px 15px 6px; font-weight: 700; font-size: 15px; line-height: 1.3; }
            .blog-card-title a { color: #333; text-decoration: none; }
            .blog-card-title a:hover { color: #7cbf3d; }

            .blog-card-categories { padding: 0 15px 8px; display: flex; flex-wrap: wrap; gap: 5px; }
            .blog-category-badge { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; color: #fff; text-decoration: none; display: inline-block; line-height: 1.4; letter-spacing: 0.3px; transition: opacity 0.2s ease, transform 0.2s ease; }
            .blog-category-badge:hover { opacity: 0.85; transform: translateY(-1px); color: #fff; }

            .blog-card-body { padding: 12px 15px; flex: 1; display: flex; flex-direction: column; }
            .blog-card-meta { font-size: 12px; color: #888; margin-bottom: 8px; display: flex; gap: 10px; flex-wrap: wrap; }
            .blog-card-excerpt { font-size: 13px; color: #555; line-height: 1.5; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
            .blog-card-tags { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 10px; }
            .blog-tag { font-size: 11px; padding: 2px 8px; background: #e8f5e9; color: #2e7d32; border-radius: 10px; text-decoration: none; }
            .blog-tag:hover, .blog-tag.active { background: #7cbf3d; color: #fff; }

            /* Empty state */
            .no-posts { text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 10px; grid-column: 1 / -1; }
            .no-posts i { font-size: 60px; color: #ccc; margin-bottom: 20px; }

            /* Mobile */
            .mobile-filter-toggle { display: none; width: 100%; padding: 12px 20px; background: #7cbf3d; border: none; border-radius: 8px; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; margin-bottom: 20px; }

            @media (max-width: 1200px) { .blog-grid { grid-template-columns: repeat(2, 1fr); } }
            @media (max-width: 992px) { .store-sidebar { width: 250px; } }
            @media (max-width: 768px) {
                .store-container { flex-direction: column; }
                .store-sidebar { width: 100%; display: none; }
                .store-sidebar.show { display: block; }
                .mobile-filter-toggle { display: flex; align-items: center; justify-content: center; }
                .blog-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
                .results-header { flex-direction: column; gap: 15px; align-items: flex-start; }
            }
            @media (max-width: 576px) { .blog-grid { grid-template-columns: 1fr; } }
        </style>
    @endpush

    {{-- Header Banner --}}
    <section class="store-header-banner">
        <div class="container text-center">
            <h1>Apricot Power Blog</h1>
            <p>Health tips, research, and wellness articles</p>
        </div>
    </section>

    <div class="store-container">
        {{-- Mobile Filter Toggle --}}
        <button class="mobile-filter-toggle" onclick="document.querySelector('.store-sidebar').classList.toggle('show')">
            <i class="bi bi-funnel" style="margin-right:8px;"></i>
            Filters
        </button>

        {{-- Sidebar --}}
        <aside class="store-sidebar">
            {{-- Categories --}}
            @if($this->categories->count())
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span>Categories</span>
                        @if($selectedCategory)
                            <span class="clear-btn" wire:click="selectCategory(null)">Clear</span>
                        @endif
                    </div>
                    <div class="filter-card-body">
                        @foreach($this->categories as $category)
                            <div class="filter-item {{ $selectedCategory === $category->id ? 'active' : '' }}"
                                 wire:click="selectCategory({{ $category->id }})">
                                <div class="filter-checkbox"></div>
                                <span class="filter-label" style="font-weight: 600;">{{ $category->name }}</span>
                                <span class="filter-count">({{ $category->published_posts_count }})</span>
                            </div>
                            @foreach($category->activeChildren as $child)
                                <div class="filter-item {{ $selectedCategory === $child->id ? 'active' : '' }}"
                                     wire:click="selectCategory({{ $child->id }})"
                                     style="padding-left: 20px;">
                                    <div class="filter-checkbox"></div>
                                    <span class="filter-label">{{ $child->name }}</span>
                                    <span class="filter-count">({{ $child->published_posts_count }})</span>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tags --}}
            @if($this->tags->count())
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span>Tags</span>
                        @if($selectedTag)
                            <span class="clear-btn" wire:click="selectTag(null)">Clear</span>
                        @endif
                    </div>
                    <div class="filter-card-body">
                        @foreach($this->tags as $tag)
                            <div class="filter-item {{ $selectedTag === $tag->slug ? 'active' : '' }}"
                                 wire:click="selectTag('{{ $tag->slug }}')">
                                <div class="filter-checkbox"></div>
                                <span class="filter-label">{{ $tag->name }}</span>
                                <span class="filter-count">({{ $tag->posts_count }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Clear All --}}
            @if($selectedCategory || $selectedTag)
                <button class="btn btn-outline-secondary btn-sm w-100" wire:click="clearFilters">
                    Clear All Filters
                </button>
            @endif
        </aside>

        {{-- Main --}}
        <main class="store-main">
            {{-- Results Header --}}
            <div class="results-header">
                <div class="results-count">
                    Showing <strong>{{ $this->posts->count() }}</strong>
                    of <strong>{{ $this->totalPosts }}</strong> articles
                </div>
                <div class="sort-dropdown">
                    <label for="sortBy">Sort by:</label>
                    <select id="sortBy" wire:model.live="sortBy">
                        <option value="newest">Newest</option>
                        <option value="oldest">Oldest</option>
                        <option value="title_asc">A – Z</option>
                    </select>
                </div>
            </div>

            {{-- Blog Grid --}}
            <div class="blog-grid">
                @forelse($this->posts as $post)
                    <div class="blog-card">
                        <a href="{{ route('blog.detail', $post->slug) }}" wire:navigate class="text-decoration-none">
                            <div class="blog-card-image">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                                         alt="{{ $post->title }}" loading="lazy">
                                @else
                                    <div class="no-image">
                                        <i class="bi bi-journal-text"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="blog-card-title">
                            <a href="{{ route('blog.detail', $post->slug) }}" wire:navigate>
                                {{ Str::limit($post->title, 70) }}
                            </a>
                        </div>
                        @if($post->categories->count())
                            <div class="blog-card-categories">
                                @foreach($post->categories as $cat)
                                    <a href="{{ route('blogs', ['category' => $cat->id]) }}"
                                       wire:navigate
                                       class="blog-category-badge"
                                       style="background-color: {{ $cat->accent_color ?? '#7cbf3d' }};">
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <div class="blog-card-body">
                            <div class="blog-card-meta">
                                @if($post->author)
                                    <span><i class="bi bi-person"></i> {{ $post->author->full_name }}</span>
                                @endif
                                @if($post->published_at)
                                    <span><i class="bi bi-calendar3"></i> {{ $post->published_at->format('M j, Y') }}</span>
                                @endif
                                <span><i class="bi bi-eye"></i> {{ number_format($post->views_count) }}</span>
                            </div>
                            @if($post->excerpt)
                                <p class="blog-card-excerpt">{{ $post->excerpt }}</p>
                            @endif
                            @if($post->tags->count())
                                <div class="blog-card-tags">
                                    @foreach($post->tags->take(4) as $tag)
                                        <a href="{{ route('blogs', ['tag' => $tag->slug]) }}"
                                           wire:navigate class="blog-tag {{ $selectedTag === $tag->slug ? 'active' : '' }}">{{ $tag->name }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="no-posts">
                        <i class="bi bi-journal-x"></i>
                        <h4>No articles found</h4>
                        <p class="text-muted">
                            @if($selectedCategory)
                                No articles in this category yet.
                            @else
                                Check back soon for new content.
                            @endif
                        </p>
                        @if($selectedCategory)
                            <button class="btn btn-outline-secondary btn-sm" wire:click="clearFilters">Clear Filters</button>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Infinite Scroll Sentinel --}}
            @if($this->hasMore)
                <div x-data x-intersect="$wire.loadMore()" class="d-flex justify-content-center mt-4 mb-4">
                    <div wire:loading wire:target="loadMore" class="text-center">
                        <div class="spinner-border text-success spinner-border-sm" role="status"></div>
                        <span class="text-muted ms-2" style="font-size: 14px;">Loading more articles...</span>
                    </div>
                </div>
            @endif
        </main>
    </div>

</div>
