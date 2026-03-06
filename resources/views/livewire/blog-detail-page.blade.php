<div class="blog-detail-page home-page">
    @push('meta')
        @php
            $seoTitle       = $post->meta_title ?: $post->title;
            $seoDescription = $post->meta_description ?: $post->excerpt;
            $seoImage       = $post->featured_image ? asset('storage/' . $post->featured_image) : null;
            $seoUrl         = route('blog.detail', $post->slug);
        @endphp

        {{-- Canonical --}}
        <link rel="canonical" href="{{ $seoUrl }}">

        {{-- Open Graph --}}
        <meta property="og:type"        content="article">
        <meta property="og:title"       content="{{ $seoTitle }}">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url"         content="{{ $seoUrl }}">
        @if($seoImage)
            <meta property="og:image" content="{{ $seoImage }}">
        @endif
        @if($post->published_at)
            <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
        @endif
        @foreach($post->categories as $cat)
            <meta property="article:section" content="{{ $cat->name }}">
        @endforeach

        {{-- Twitter Card --}}
        <meta name="twitter:card"        content="summary_large_image">
        <meta name="twitter:title"       content="{{ $seoTitle }}">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        @if($seoImage)
            <meta name="twitter:image" content="{{ $seoImage }}">
        @endif

        {{-- JSON-LD Article structured data --}}
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BlogPosting",
            "headline": "{{ addslashes($seoTitle) }}",
            "description": "{{ addslashes($seoDescription) }}",
            "url": "{{ $seoUrl }}",
            @if($seoImage)
            "image": "{{ $seoImage }}",
            @endif
            @if($post->published_at)
            "datePublished": "{{ $post->published_at->toIso8601String() }}",
            "dateModified": "{{ $post->updated_at->toIso8601String() }}",
            @endif
            "author": {
                "@type": "Organization",
                "name": "{{ $post->author ? $post->author->full_name : 'Apricot Power' }}"
            },
            "publisher": {
                "@type": "Organization",
                "name": "Apricot Power",
                "url": "{{ url('/') }}"
            }
        }
        </script>
    @endpush
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
        <style>
            .blog-hero {
                position: relative;
                min-height: 380px;
                background: linear-gradient(135deg, #2d5a27 0%, #3d7a34 100%);
                display: flex;
                align-items: flex-end;
                overflow: hidden;
                padding-top: 120px;
            }
            .blog-hero-img {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                opacity: 0.35;
                pointer-events: none;
            }
            .blog-hero-content {
                position: relative;
                z-index: 1;
                padding: 40px 0 30px;
                width: 100%;
            }
            .blog-hero-content h1 {
                color: #fff;
                font-size: 2.2rem;
                font-weight: 700;
                line-height: 1.2;
                margin-bottom: 12px;
            }
            .blog-hero-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
                color: rgba(255,255,255,0.85);
                font-size: 14px;
            }
            .blog-hero-meta span { display: flex; align-items: center; gap: 5px; }

            .blog-layout {
                max-width: 1200px;
                margin: 0 auto;
                padding: 40px 20px;
                display: grid;
                grid-template-columns: 1fr 300px;
                gap: 40px;
            }
            @media (max-width: 900px) {
                .blog-layout { grid-template-columns: 1fr; }
                .blog-sidebar { order: -1; }
            }

            .blog-content {
                background: #fff;
                border-radius: 8px;
                border: 1px solid #e0e0e0;
                padding: 30px;
            }
            .blog-content img { max-width: 100%; height: auto; border-radius: 6px; }
            .blog-content h2, .blog-content h3 { color: #2d5a27; margin-top: 24px; }
            .blog-content p { line-height: 1.8; color: #333; }
            .blog-content a { color: #7cbf3d; }

            .blog-tags-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e0e0e0; }
            .blog-tag { font-size: 12px; padding: 4px 12px; background: #e8f5e9; color: #2e7d32; border-radius: 20px; text-decoration: none; }
            .blog-tag:hover { background: #c8e6c9; }

            .back-link { display: inline-flex; align-items: center; gap: 6px; color: #7cbf3d; font-size: 14px; font-weight: 600; text-decoration: none; margin-bottom: 20px; }
            .back-link:hover { color: #5a9a2a; text-decoration: underline; }

            /* Sidebar */
            .blog-sidebar-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; margin-bottom: 20px; }
            .blog-sidebar-card-header { background: #f8f9fa; padding: 12px 15px; font-weight: 700; font-size: 14px; border-bottom: 1px solid #e0e0e0; color: #333; }
            .blog-sidebar-card-body { padding: 15px; }

            .sidebar-category-item { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
            .sidebar-category-item:last-child { border-bottom: none; }
            .sidebar-category-item a { color: #333; text-decoration: none; }
            .sidebar-category-item a:hover { color: #7cbf3d; }
            .sidebar-category-count { font-size: 12px; color: #888; }

            .related-post-item { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
            .related-post-item:last-child { border-bottom: none; }
            .related-post-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; flex-shrink: 0; background: #e0e0e0; }
            .related-post-thumb-placeholder { width: 60px; height: 60px; border-radius: 4px; background: #e8f5e9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
            .related-post-info a { font-size: 13px; font-weight: 600; color: #333; text-decoration: none; line-height: 1.3; display: block; }
            .related-post-info a:hover { color: #7cbf3d; }
            .related-post-date { font-size: 11px; color: #888; margin-top: 4px; }
        </style>
    @endpush

    {{-- Hero --}}
    <div class="blog-hero">
        @if($post->featured_image)
            <img class="blog-hero-img" src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
        @endif
        <div class="blog-hero-content">
            <div class="container">
                <h1>{{ $post->title }}</h1>
                <div class="blog-hero-meta">
                    @if($post->author)
                        <span><i class="bi bi-person-fill"></i> {{ $post->author->full_name }}</span>
                    @endif
                    @if($post->published_at)
                        <span><i class="bi bi-calendar3"></i> {{ $post->published_at->format('F j, Y') }}</span>
                    @endif
                    <span><i class="bi bi-clock"></i> {{ $post->reading_time }} min read</span>
                    <span><i class="bi bi-eye"></i> {{ number_format($post->views_count) }} views</span>
                    @foreach($post->categories as $cat)
                        <span style="background: {{ $cat->accent_color ?? '#7cbf3d' }}; padding: 2px 10px; border-radius: 20px; font-size: 12px; color: #fff;">
                            <i class="bi bi-folder2"></i> {{ $cat->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Main Layout --}}
    <div class="blog-layout">
        {{-- Main Content --}}
        <article>
            <a href="{{ route('blogs') }}" class="back-link" wire:navigate>
                <i class="bi bi-arrow-left"></i> Back to Blog
            </a>

            <div class="blog-content">
                {!! $post->content !!}

                {{-- Tags --}}
                @if($post->tags->count())
                    <div class="blog-tags-row">
                        <strong style="font-size:13px; color:#555;">Tags:</strong>
                        @foreach($post->tags as $tag)
                            <a href="{{ route('blogs', ['tag' => $tag->slug]) }}" wire:navigate class="blog-tag">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        {{-- Sidebar --}}
        <aside class="blog-sidebar">
            {{-- Categories --}}
            @php
                $parentCategories = \App\Models\BlogCategory::active()
                    ->whereNull('parent_id')
                    ->with(['activeChildren' => fn ($q) => $q->withCount('publishedPosts')])
                    ->withCount('publishedPosts')
                    ->orderBy('sort_order')
                    ->get();
            @endphp
            @if($parentCategories->count())
                <div class="blog-sidebar-card">
                    <div class="blog-sidebar-card-header">Categories</div>
                    <div class="blog-sidebar-card-body p-0">
                        @foreach($parentCategories as $cat)
                            <div class="sidebar-category-item px-3" style="font-weight: 600;">
                                <a href="{{ route('blogs', ['category' => $cat->id]) }}" wire:navigate>{{ $cat->name }}</a>
                                <span class="sidebar-category-count">{{ $cat->published_posts_count }}</span>
                            </div>
                            @foreach($cat->activeChildren as $child)
                                <div class="sidebar-category-item px-3" style="padding-left: 30px;">
                                    <a href="{{ route('blogs', ['category' => $child->id]) }}" wire:navigate>{{ $child->name }}</a>
                                    <span class="sidebar-category-count">{{ $child->published_posts_count }}</span>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Related Posts --}}
            @if($this->relatedPosts->count())
                <div class="blog-sidebar-card">
                    <div class="blog-sidebar-card-header">Related Articles</div>
                    <div class="blog-sidebar-card-body">
                        @foreach($this->relatedPosts as $related)
                            <div class="related-post-item">
                                @if($related->featured_image)
                                    <img class="related-post-thumb"
                                         src="{{ asset('storage/' . $related->featured_image) }}"
                                         alt="{{ $related->title }}" loading="lazy">
                                @else
                                    <div class="related-post-thumb-placeholder">
                                        <i class="bi bi-journal-text" style="color:#7cbf3d;"></i>
                                    </div>
                                @endif
                                <div class="related-post-info">
                                    <a href="{{ route('blog.detail', $related->slug) }}" wire:navigate>
                                        {{ Str::limit($related->title, 60) }}
                                    </a>
                                    @if($related->published_at)
                                        <div class="related-post-date">{{ $related->published_at->format('M j, Y') }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</div>
