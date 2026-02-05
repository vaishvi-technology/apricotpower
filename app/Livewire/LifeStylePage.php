<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class LifeStylePage extends Component
{
    public function render(): View
    {
        return view('livewire.life-style-page')
            ->layout('layouts.storefront');
    }
}
