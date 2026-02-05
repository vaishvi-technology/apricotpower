<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class WholesaleApplicationPage extends Component
{
    public function render(): View
    {
        return view('livewire.wholesale-application-page')
            ->layout('layouts.storefront');
    }
}
