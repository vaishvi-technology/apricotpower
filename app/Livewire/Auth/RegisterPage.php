<?php

namespace App\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Component;

class RegisterPage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function render(): View
    {
        return view('livewire.auth.register-page')
            ->layout('layouts.storefront');
    }
}
