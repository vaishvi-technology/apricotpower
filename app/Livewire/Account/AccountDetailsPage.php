<?php

namespace App\Livewire\Account;

use App\Models\CustomerGroup;
use App\Services\ImpersonationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Admin\Models\Staff;
use Lunar\Models\Country;
use Lunar\Models\State;

class AccountDetailsPage extends Component
{
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

    // Admin options (only used during impersonation)
    public bool $isImpersonating = false;
    public bool $isWholesale = false;

    // Account Settings fields
    public ?int $customer_group_id = null;
    public ?int $sales_rep_id = null;
    public bool $is_tax_exempt = false;
    public ?string $last_login_at = null;
    public ?string $last_order_at = null;

    // Wholesale / Billing fields
    public bool $is_online_wholesaler = false;
    public string $store_date = '';
    public int $store_count = 1;
    public bool $net_terms_approved = false;
    public string $credit_limit_option = '';
    public string $credit_limit = '';
    public string $accounts_payable_email = '';
    public bool $include_in_retailer_map = false;
    public string $retailer_name = '';
    public string $retailer_street = '';
    public string $retailer_city = '';
    public string $retailer_country = '';
    public string $retailer_state = '';
    public string $retailer_phone = '';
    public string $retailer_toll_free_phone = '';
    public string $retailer_website = '';
    public string $retailer_email = '';
    public string $retailer_products_sold = '';


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

        // Load admin fields when impersonating
        $this->isImpersonating = app(ImpersonationService::class)->isImpersonating();
        if ($this->isImpersonating) {
            $this->isWholesale = $customer->customerGroups()->where('is_wholesale', true)->exists();

            // Account Settings
            $this->customer_group_id = $customer->customerGroups()->first()?->id;
            $this->sales_rep_id = $customer->sales_rep_id;
            $this->is_tax_exempt = (bool) $customer->is_tax_exempt;
            $this->last_login_at = $customer->last_login_at?->format('M d, Y h:i A');
            $this->last_order_at = $customer->last_order_at?->format('M d, Y h:i A');

            // Wholesale / Billing
            $this->is_online_wholesaler = (bool) $customer->is_online_wholesaler;
            $this->store_count = (int) ($customer->store_count ?? 1);
            $this->net_terms_approved = (bool) $customer->net_terms_approved;
            $this->credit_limit = $customer->credit_limit ?? '';
            $presetLimits = ['', '500', '1000', '3000', '4000', '5000', '7000', '10000'];
            $this->credit_limit_option = in_array($this->credit_limit, $presetLimits) ? $this->credit_limit : 'custom';
            $this->accounts_payable_email = $customer->accounts_payable_email ?? '';

            $profile = $customer->retailerProfile;
            if ($profile) {
                $this->include_in_retailer_map = (bool) $profile->include_in_retailer_map;
                $this->retailer_name = $profile->name ?? '';
                $this->retailer_street = $profile->street ?? '';
                $this->retailer_city = $profile->city ?? '';
                $this->retailer_country = $profile->country ?? '';
                $this->retailer_state = $profile->state ?? '';
                $this->retailer_phone = $profile->phone ?? '';
                $this->retailer_toll_free_phone = $profile->toll_free_phone ?? '';
                $this->retailer_website = $profile->website ?? '';
                $this->retailer_email = $profile->email ?? '';
                $this->retailer_products_sold = $profile->products_sold ?? '';
            }

        }
    }

    public function updatedAddressCountryId(): void
    {
        // Reset state when country changes
        $this->address_state = '';
    }

    public function updatedCreditLimitOption(): void
    {
        if ($this->credit_limit_option !== 'custom') {
            $this->credit_limit = $this->credit_limit_option;
        } else {
            $this->credit_limit = '';
        }
    }

    public function updatedRetailerCountry(): void
    {
        $this->retailer_state = '';
    }

    public function updatedCustomerGroupId(): void
    {
        if ($this->customer_group_id) {
            $this->isWholesale = CustomerGroup::where('id', $this->customer_group_id)
                ->where('is_wholesale', true)
                ->exists();
        } else {
            $this->isWholesale = false;
        }
    }

    public function getCustomerGroupsProperty()
    {
        return CustomerGroup::where('is_active', true)->orderBy('name')->get();
    }

    public function getStaffMembersProperty()
    {
        return Staff::orderBy('first_name')->orderBy('last_name')->get();
    }

    public function getRetailerCountriesProperty()
    {
        return Country::orderBy('name')->get();
    }

    public function getRetailerStatesProperty()
    {
        if (! $this->retailer_country) {
            return collect();
        }

        $country = Country::where('name', $this->retailer_country)->first();
        if (! $country) {
            return collect();
        }

        return State::where('country_id', $country->id)->orderBy('name')->get();
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

    public function saveAccountSettings(): void
    {
        if (! $this->isImpersonating) {
            abort(403);
        }

        $this->validate([
            'customer_group_id' => 'nullable|exists:customer_groups,id',
            'sales_rep_id' => 'nullable|exists:staff,id',
        ]);

        $customer = Auth::guard('customer')->user();

        $customer->update([
            'sales_rep_id' => $this->sales_rep_id,
            'is_tax_exempt' => $this->is_tax_exempt,
        ]);

        if ($this->customer_group_id) {
            $customer->customerGroups()->sync([$this->customer_group_id]);
        } else {
            $customer->customerGroups()->detach();
        }

        session()->flash('admin_settings_success', 'Account settings have been updated.');
    }

    public function saveWholesaleBilling(): void
    {
        if (! $this->isImpersonating) {
            abort(403);
        }

        $this->validate([
            'credit_limit' => 'nullable|numeric|min:0',
            'store_date' => 'nullable|date',
            'store_count' => 'nullable|integer|min:1|max:100',
            'accounts_payable_email' => 'nullable|email|max:255',
            'retailer_name' => 'nullable|string|max:255',
            'retailer_street' => 'nullable|string|max:255',
            'retailer_city' => 'nullable|string|max:255',
            'retailer_country' => 'nullable|string|max:255',
            'retailer_state' => 'nullable|string|max:255',
            'retailer_phone' => 'nullable|string|max:50',
            'retailer_toll_free_phone' => 'nullable|string|max:50',
            'retailer_website' => 'nullable|url|max:255',
            'retailer_email' => 'nullable|email|max:255',
            'retailer_products_sold' => 'nullable|string',
        ]);

        $customer = Auth::guard('customer')->user();

        $customer->update([
            'is_online_wholesaler' => $this->is_online_wholesaler,
            'store_count' => $this->store_count,
            'net_terms_approved' => $this->net_terms_approved,
            'credit_limit' => $this->credit_limit !== '' ? $this->credit_limit : null,
            'accounts_payable_email' => $this->accounts_payable_email ?: null,
        ]);


        $customer->retailerProfile()->updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'include_in_retailer_map' => $this->include_in_retailer_map,
                'name' => $this->retailer_name ?: null,
                'street' => $this->retailer_street ?: null,
                'city' => $this->retailer_city ?: null,
                'country' => $this->retailer_country ?: null,
                'state' => $this->retailer_state ?: null,
                'phone' => $this->retailer_phone ?: null,
                'toll_free_phone' => $this->retailer_toll_free_phone ?: null,
                'website' => $this->retailer_website ?: null,
                'email' => $this->retailer_email ?: null,
                'products_sold' => $this->retailer_products_sold ?: null,
            ]
        );

        session()->flash('admin_wholesale_success', 'Wholesale / Billing settings have been updated.');
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
