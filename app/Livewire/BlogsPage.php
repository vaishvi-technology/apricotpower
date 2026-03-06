<?php

namespace App\Livewire;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BlogsPage extends Component
{
    use WithPagination;

    public string $searchQuery = '';
    public ?int $selectedCategory = null;
    public ?string $selectedTag = null;
    public string $sortBy = 'newest';

    protected $queryString = [
        'searchQuery'      => ['except' => '', 'as' => 'q'],
        'selectedCategory' => ['except' => null, 'as' => 'category'],
        'selectedTag'      => ['except' => null, 'as' => 'tag'],
        'sortBy'           => ['except' => 'newest'],
    ];

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedTag(): void
    {
        $this->resetPage();
    }

    public function selectTag(?string $slug): void
    {
        $this->selectedTag = ($this->selectedTag === $slug) ? null : $slug;
        $this->resetPage();
    }

    public function selectCategory(?int $id): void
    {
        $this->selectedCategory = ($this->selectedCategory === $id) ? null : $id;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->searchQuery = '';
        $this->selectedCategory = null;
        $this->selectedTag = null;
        $this->sortBy = 'newest';
        $this->resetPage();
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

    public function getPinnedPostsProperty(): Collection
    {
        return BlogPost::published()
            ->pinned()
            ->with(['categories', 'author'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
    }

    public function getPostsProperty(): LengthAwarePaginator
    {
        $query = BlogPost::published()
            ->with(['categories', 'author', 'tags']);

        if (!empty($this->searchQuery)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('content', 'like', '%' . $this->searchQuery . '%');
            });
        }

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

        return $query->paginate(9);
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
