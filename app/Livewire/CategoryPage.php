<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Collection;
class CategoryPage extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getCategoryProperty()
    {
        return Collection::whereHas('urls', function ($query) {
            $query->where('slug', $this->slug);
        })->first();
    }

    public function getProductsProperty()
    {
        if (!$this->category) {
            return collect();
        }

        return $this->category->products()
            ->with(['defaultUrl', 'thumbnail'])
            ->paginate(12);
    }

    public function render(): View
    {
        return view('livewire.category-page')
            ->layout('layouts.storefront');
    }
}
