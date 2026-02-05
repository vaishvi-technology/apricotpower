<?php

namespace App\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Component;

class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function render(): View
    {
        return view('livewire.auth.login-page')
            ->layout('layouts.storefront');
    }
}
