<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class RetailerLocationsPage extends Component
{
    public function render(): View
    {
        return view('livewire.retailer-locations-page')
            ->layout('layouts.storefront');
    }
}
