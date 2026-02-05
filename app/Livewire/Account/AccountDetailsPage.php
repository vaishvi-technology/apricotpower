<?php

namespace App\Livewire\Account;

use Illuminate\View\View;
use Livewire\Component;

class AccountDetailsPage extends Component
{
    public function render(): View
    {
        return view('livewire.account.account-details-page')
            ->layout('layouts.storefront');
    }
}
