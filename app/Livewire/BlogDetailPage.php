<?php

namespace App\Livewire;

use App\Models\BlogPost;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class BlogDetailPage extends Component
{
    public BlogPost $post;

    public function mount(string $slug): void
    {
        $this->post = BlogPost::published()
            ->with(['categories', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $this->post->incrementViews();
    }

    public function getRelatedPostsProperty(): Collection
    {
        $categoryIds = $this->post->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return BlogPost::published()
            ->with(['categories', 'author'])
            ->whereHas('categories', fn ($q) => $q->whereIn('blog_categories.id', $categoryIds))
            ->where('id', '!=', $this->post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.blog-detail-page')
            ->layout('layouts.storefront', [
                'title' => ($this->post->meta_title ?: $this->post->title) . ' | Apricot Power Blog',
                'metaDescription' => $this->post->meta_description ?: ($this->post->excerpt ?: null),
            ]);
    }
}
