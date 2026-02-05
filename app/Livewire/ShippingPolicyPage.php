<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class ShippingPolicyPage extends Component
{
    public function render(): View
    {
        return view('livewire.shipping-policy-page')
            ->layout('layouts.storefront');
    }
}
