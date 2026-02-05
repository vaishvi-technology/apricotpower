<?php

namespace App\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Component;

class ForgotPasswordPage extends Component
{
    public string $email = '';

    public function render(): View
    {
        return view('livewire.auth.forgot-password-page')
            ->layout('layouts.storefront');
    }
}
