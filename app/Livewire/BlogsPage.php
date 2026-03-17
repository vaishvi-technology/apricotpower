<?php

namespace App\Livewire;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class BlogsPage extends Component
{
    public ?int $selectedCategory = null;
    public ?string $selectedTag = null;
    public string $sortBy = 'newest';
    public int $perPage = 9;

    protected $queryString = [
        'selectedCategory' => ['except' => null, 'as' => 'category'],
        'selectedTag'      => ['except' => null, 'as' => 'tag'],
        'sortBy'           => ['except' => 'newest'],
    ];

    public function updatingSelectedCategory(): void
    {
        $this->perPage = 9;
    }

    public function updatingSelectedTag(): void
    {
        $this->perPage = 9;
    }

    public function updatingSortBy(): void
    {
        $this->perPage = 9;
    }

    public function selectTag(?string $slug): void
    {
        $this->selectedTag = ($this->selectedTag === $slug) ? null : $slug;
        $this->perPage = 9;
    }

    public function selectCategory(?int $id): void
    {
        $this->selectedCategory = ($this->selectedCategory === $id) ? null : $id;
        $this->perPage = 9;
    }

    public function clearFilters(): void
    {
        $this->selectedCategory = null;
        $this->selectedTag = null;
        $this->sortBy = 'newest';
        $this->perPage = 9;
    }

    public function loadMore(): void
    {
        $this->perPage += 9;
    }

    public function getCategoriesProperty(): Collection
    {
        return BlogCategory::active()
            ->parentsOnly()
            ->with(['activeChildren' => fn ($q) => $q->withCount('publishedPosts')])
            ->withCount('publishedPosts')
            ->orderBy('sort_order')
            ->get();
    }

    public function getTagsProperty(): Collection
    {
        return BlogTag::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();
    }

    public function getPostsProperty(): Collection
    {
        $query = BlogPost::published()
            ->with(['categories', 'author', 'tags']);

        if ($this->selectedCategory) {
            $query->whereHas('categories', fn ($q) => $q->where('blog_categories.id', $this->selectedCategory));
        }

        if ($this->selectedTag) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $this->selectedTag));
        }

        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('published_at');
                break;
            case 'title_asc':
                $query->orderBy('title');
                break;
            default: // newest
                $query->orderByDesc('is_pinned')->orderByDesc('published_at');
                break;
        }

        return $query->limit($this->perPage)->get();
    }

    public function getTotalPostsProperty(): int
    {
        $query = BlogPost::published();

        if ($this->selectedCategory) {
            $query->whereHas('categories', fn ($q) => $q->where('blog_categories.id', $this->selectedCategory));
        }

        if ($this->selectedTag) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $this->selectedTag));
        }

        return $query->count();
    }

    public function getHasMoreProperty(): bool
    {
        return $this->posts->count() < $this->totalPosts;
    }

    public function render(): View
    {
        return view('livewire.blogs-page')
            ->layout('layouts.storefront', [
                'title' => 'Blog | Apricot Power',
                'metaDescription' => 'Browse our latest health tips, wellness research, and articles from the Apricot Power team.',
            ]);
    }
}
