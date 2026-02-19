<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Customer;

class RegisterPage extends Component
{
    // Field mapping from legacy `accounts` table:
    //   accounts.ShippingNameFirst → first_name
    //   accounts.ShippingNameLast  → last_name
    //   accounts.Email             → email
    //   accounts.Phone             → phone
    //   accounts.Password          → password
    //   accounts.Company           → company_name

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company_name = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'first_name'  => 'required|string|max:50',
            'last_name'   => 'required|string|max:50',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'nullable|string|max:50',
            'company_name'=> 'nullable|string|max:255',
            'password'    => 'required|min:8|confirmed',
        ];
    }

    public function register(): void
    {
        $this->validate();

        // 1. Create the Laravel auth user
        $user = User::create([
            'name'         => trim($this->first_name . ' ' . $this->last_name),
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'email'        => $this->email,
            'password'     => Hash::make($this->password),
            'phone'        => $this->phone ?: null,
            'company_name' => $this->company_name ?: null,
        ]);

        // 2. Create a linked Lunar customer profile
        //    lunar_customers.first_name ← accounts.ShippingNameFirst
        //    lunar_customers.last_name  ← accounts.ShippingNameLast
        //    lunar_customers.company_name ← accounts.Company
        $customer = Customer::create([
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'company_name' => $this->company_name ?: null,
        ]);

        // 3. Link user ↔ customer via lunar_customer_user pivot
        $user->customers()->attach($customer->id);

        Auth::login($user);

        session()->regenerate();

        $this->redirect(route('order-history.view'));
    }

    public function render(): View
    {
        return view('livewire.auth.register-page')
            ->layout('layouts.storefront');
    }
}
