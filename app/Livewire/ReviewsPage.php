<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class ReviewsPage extends Component
{
    public function render(): View
    {
        return view('livewire.reviews-page')
            ->layout('layouts.storefront');
    }
}
