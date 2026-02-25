<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use App\Notifications\CustomerWelcomeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;

class RegisterPage extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company_name = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $referred_by = '';
    public string $referred_by_other = '';
    public string $b17_knowledge = '';
    public bool $subscribe_to_list = false;
    public bool $agreed_terms = false;

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:' . (new Customer)->getTable() . ',email',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'password' => 'required|min:8|confirmed',
            'referred_by' => 'nullable|string|max:255',
            'referred_by_other' => 'nullable|string|max:50|required_if:referred_by,Other...',
            'b17_knowledge' => 'nullable|string|max:1000',
            'agreed_terms' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'agreed_terms.accepted' => 'You must agree to the Terms & Conditions.',
        ];
    }

    public function register(): void
    {
        $this->validate();

        $customer = Customer::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'company_name' => $this->company_name ?: null,
            'password' => Hash::make($this->password),
            'referred_by' => $this->referred_by === 'Other...' ? ($this->referred_by_other ?: 'Other') : ($this->referred_by ?: null),
            'b17_knowledge' => $this->b17_knowledge ?: null,
            'subscribe_to_list' => $this->subscribe_to_list,
            'agreed_terms_at' => now(),
            'is_active' => true,
        ]);

        Auth::guard('customer')->login($customer);

        $customer->notify(new CustomerWelcomeNotification());

        session()->regenerate();

        $this->redirect(route('order-history.view'));
    }

    public function render(): View
    {
        return view('livewire.auth.register-page')
            ->layout('layouts.storefront');
    }
}
