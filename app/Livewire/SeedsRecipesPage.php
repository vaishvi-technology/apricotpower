<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class SeedsRecipesPage extends Component
{
    public function render(): View
    {
        return view('livewire.seeds-recipes-page')
            ->layout('layouts.storefront');
    }
}
