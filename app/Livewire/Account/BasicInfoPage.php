<?php

namespace App\Livewire\Account;

use Illuminate\View\View;
use Livewire\Component;

class BasicInfoPage extends Component
{
    public function render(): View
    {
        return view('livewire.account.basic-info-page')
            ->layout('layouts.storefront');
    }
}
