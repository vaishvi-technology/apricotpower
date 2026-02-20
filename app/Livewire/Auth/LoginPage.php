<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class LoginPage extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
    }

    public function login(): void
    {
        $this->validate();

        if (Auth::guard('customer')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $customer = Auth::guard('customer')->user();

            if (!$customer->is_active) {
                Auth::guard('customer')->logout();
                session()->invalidate();
                session()->regenerateToken();
                $this->addError('email', 'Your account is not active. Please contact an administrator.');
                return;
            }

            session()->regenerate();
            $this->redirect(route('order-history.view'));
            return;
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render(): View
    {
        return view('livewire.auth.login-page')
            ->layout('layouts.storefront');
    }
}
