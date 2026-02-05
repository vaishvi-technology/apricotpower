<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class FaqPage extends Component
{
    public function render(): View
    {
        return view('livewire.faq-page')
            ->layout('layouts.storefront');
    }
}
