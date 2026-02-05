<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class BlogDetailPage extends Component
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        return view('livewire.blog-detail-page')
            ->layout('layouts.storefront');
    }
}
