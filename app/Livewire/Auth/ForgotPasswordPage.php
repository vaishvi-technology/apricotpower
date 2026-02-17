<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Livewire\Component;

class ForgotPasswordPage extends Component
{
    public string $email = '';
    public bool $emailSent = false;

    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->emailSent = true;
            session()->flash('status', __($status));
        } else {
            $this->addError('email', __($status));
        }
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password-page')
            ->layout('layouts.storefront');
    }
}
