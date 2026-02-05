<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class BlogsPage extends Component
{
    public function render(): View
    {
        return view('livewire.blogs-page')
            ->layout('layouts.storefront');
    }
}
