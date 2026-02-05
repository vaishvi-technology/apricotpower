<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class TestimonialPage extends Component
{
    public function render(): View
    {
        return view('livewire.testimonial-page')
            ->layout('layouts.storefront');
    }
}
