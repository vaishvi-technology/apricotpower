<?php

namespace App\Livewire\Account;

use Illuminate\View\View;
use Livewire\Component;

class EmailPreferencesPage extends Component
{
    public function render(): View
    {
        return view('livewire.account.email-preferences-page')
            ->layout('layouts.storefront');
    }
}
