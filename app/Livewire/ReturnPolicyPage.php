<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class ReturnPolicyPage extends Component
{
    public function render(): View
    {
        return view('livewire.return-policy-page')
            ->layout('layouts.storefront');
    }
}
