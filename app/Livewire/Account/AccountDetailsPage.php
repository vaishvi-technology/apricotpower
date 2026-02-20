<?php

namespace App\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;

class AccountDetailsPage extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Shipping address
    public string $shipping_line_one = '';
    public string $shipping_line_two = '';
    public string $shipping_city = '';
    public string $shipping_state = '';
    public string $shipping_postcode = '';

    public function mount(): void
    {
        $customer = Auth::guard('customer')->user();
        $address = $customer->addresses()->where('shipping_default', true)->first();

        if ($address) {
            $this->shipping_line_one = $address->line_one ?? '';
            $this->shipping_line_two = $address->line_two ?? '';
            $this->shipping_city = $address->city ?? '';
            $this->shipping_state = $address->state ?? '';
            $this->shipping_postcode = $address->postcode ?? '';
        }
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $customer = Auth::guard('customer')->user();

        if (!Hash::check($this->current_password, $customer->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $customer->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('password_success', 'Your password has been updated.');
    }

    public function saveAddress(): void
    {
        $this->validate([
            'shipping_line_one' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_postcode' => 'required|string|max:20',
        ]);

        $customer = Auth::guard('customer')->user();

        $customer->addresses()->updateOrCreate(
            ['shipping_default' => true],
            [
                'line_one' => $this->shipping_line_one,
                'line_two' => $this->shipping_line_two ?: null,
                'city' => $this->shipping_city,
                'state' => $this->shipping_state,
                'postcode' => $this->shipping_postcode,
                'shipping_default' => true,
                'country_id' => 235, // US
            ]
        );

        session()->flash('address_success', 'Your shipping address has been updated.');
    }

    public function render(): View
    {
        return view('livewire.account.account-details-page')
            ->layout('layouts.storefront');
    }
}
