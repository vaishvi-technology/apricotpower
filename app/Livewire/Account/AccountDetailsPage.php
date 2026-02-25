<?php

namespace App\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Country;
use Lunar\Models\State;

class AccountDetailsPage extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Account Information
    public string $referred_by = '';
    public string $b17_knowledge = '';

    // Address form fields
    public ?int $editing_address_id = null;
    public bool $showAddressForm = false;
    public string $address_label = '';
    public string $address_first_name = '';
    public string $address_last_name = '';
    public string $address_line_one = '';
    public string $address_line_two = '';
    public string $address_city = '';
    public ?int $address_country_id = null;
    public string $address_state = '';
    public string $address_postcode = '';
    public string $address_phone = '';
    public bool $address_shipping_default = false;

    public function mount(): void
    {
        $customer = Auth::guard('customer')->user();
        $this->address_first_name = $customer->first_name ?? '';
        $this->address_last_name = $customer->last_name ?? '';
        $this->referred_by = $customer->referred_by ?? '';
        $this->b17_knowledge = $customer->b17_knowledge ?? '';

        // Default to US
        $us = Country::where('iso3', 'USA')->orWhere('iso2', 'US')->first();
        $this->address_country_id = $us?->id;
    }

    public function updatedAddressCountryId(): void
    {
        // Reset state when country changes
        $this->address_state = '';
    }

    public function getCountriesProperty()
    {
        return Country::orderBy('name')->get();
    }

    public function getStatesProperty()
    {
        if (!$this->address_country_id) {
            return collect();
        }

        return State::where('country_id', $this->address_country_id)
            ->orderBy('name')
            ->get();
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

    public function openAddressForm(): void
    {
        $this->resetAddressForm();
        $customer = Auth::guard('customer')->user();
        $this->address_first_name = $customer->first_name ?? '';
        $this->address_last_name = $customer->last_name ?? '';
        $this->showAddressForm = true;
    }

    public function editAddress(int $addressId): void
    {
        $customer = Auth::guard('customer')->user();
        $address = $customer->addresses()->findOrFail($addressId);

        $this->editing_address_id = $address->id;
        $this->address_label = $address->label ?? '';
        $this->address_first_name = $address->first_name ?? '';
        $this->address_last_name = $address->last_name ?? '';
        $this->address_line_one = $address->line_one ?? '';
        $this->address_line_two = $address->line_two ?? '';
        $this->address_city = $address->city ?? '';
        $this->address_country_id = $address->country_id;
        $this->address_state = $address->state ?? '';
        $this->address_postcode = $address->postcode ?? '';
        $this->address_phone = $address->contact_phone ?? '';
        $this->address_shipping_default = (bool) $address->shipping_default;
        $this->showAddressForm = true;
    }

    public function saveAddress(): void
    {
        $this->validate([
            'address_first_name' => 'required|string|max:255',
            'address_last_name' => 'required|string|max:255',
            'address_line_one' => 'required|string|max:255',
            'address_city' => 'required|string|max:255',
            'address_country_id' => 'required|exists:countries,id',
            'address_state' => 'required|string|max:255',
            'address_postcode' => 'required|string|max:20',
        ]);

        $customer = Auth::guard('customer')->user();

        // Enforce 10 address limit when adding new
        if (!$this->editing_address_id && $customer->addresses()->count() >= 10) {
            session()->flash('address_error', 'Address limit reached. You can save up to 10 addresses. Please delete an existing address to add more.');
            $this->resetAddressForm();
            $this->showAddressForm = false;
            return;
        }

        $data = [
            'first_name' => $this->address_first_name,
            'last_name' => $this->address_last_name,
            'label' => $this->address_label ?: null,
            'line_one' => $this->address_line_one,
            'line_two' => $this->address_line_two ?: null,
            'city' => $this->address_city,
            'state' => $this->address_state,
            'postcode' => $this->address_postcode,
            'contact_phone' => $this->address_phone ?: null,
            'shipping_default' => $this->address_shipping_default,
            'country_id' => $this->address_country_id,
        ];

        // If setting as default, unset other defaults first
        if ($this->address_shipping_default) {
            $customer->addresses()
                ->when($this->editing_address_id, fn ($q) => $q->where('id', '!=', $this->editing_address_id))
                ->where('shipping_default', true)
                ->update(['shipping_default' => false]);
        }

        if ($this->editing_address_id) {
            $customer->addresses()->where('id', $this->editing_address_id)->update($data);
            session()->flash('address_success', 'Address updated successfully.');
        } else {
            $customer->addresses()->create($data);
            session()->flash('address_success', 'Address added successfully.');
        }

        $this->resetAddressForm();
        $this->showAddressForm = false;
    }

    public function deleteAddress(int $addressId): void
    {
        $customer = Auth::guard('customer')->user();
        $customer->addresses()->where('id', $addressId)->delete();

        session()->flash('address_success', 'Address deleted successfully.');
    }

    public function setDefaultAddress(int $addressId): void
    {
        $customer = Auth::guard('customer')->user();

        $customer->addresses()->where('shipping_default', true)->update(['shipping_default' => false]);
        $customer->addresses()->where('id', $addressId)->update(['shipping_default' => true]);

        session()->flash('address_success', 'Default shipping address updated.');
    }

    public function cancelAddressForm(): void
    {
        $this->resetAddressForm();
        $this->showAddressForm = false;
    }

    private function resetAddressForm(): void
    {
        $this->editing_address_id = null;
        $this->address_label = '';
        $this->address_first_name = '';
        $this->address_last_name = '';
        $this->address_line_one = '';
        $this->address_line_two = '';
        $this->address_city = '';
        $this->address_state = '';
        $this->address_postcode = '';
        $this->address_phone = '';
        $this->address_shipping_default = false;

        // Reset country to US
        $us = Country::where('iso3', 'USA')->orWhere('iso2', 'US')->first();
        $this->address_country_id = $us?->id;

        $this->resetValidation();
    }

    public function saveAccountInfo(): void
    {
        $customer = Auth::guard('customer')->user();

        $customer->referred_by = $this->referred_by ?: null;
        $customer->b17_knowledge = $this->b17_knowledge ?: null;
        $customer->save();

        session()->flash('account_info_success', 'Account information has been updated.');
    }

    public function render(): View
    {
        $customer = Auth::guard('customer')->user();
        $addresses = $customer->addresses()
            ->with('country')
            ->orderByDesc('shipping_default')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.account.account-details-page', [
            'addresses' => $addresses,
        ])->layout('layouts.storefront');
    }
}
