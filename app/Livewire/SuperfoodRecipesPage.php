<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class SuperfoodRecipesPage extends Component
{
    public function render(): View
    {
        return view('livewire.superfood-recipes-page')
            ->layout('layouts.storefront');
    }
}
