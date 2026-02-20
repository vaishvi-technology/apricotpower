<?php

namespace App\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class BasicInfoPage extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company_name = '';

    public function mount(): void
    {
        $customer = Auth::guard('customer')->user();
        $this->first_name = $customer->first_name ?? '';
        $this->last_name = $customer->last_name ?? '';
        $this->email = $customer->email ?? '';
        $this->phone = $customer->phone ?? '';
        $this->company_name = $customer->company_name ?? '';
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . Auth::guard('customer')->id(),
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $customer = Auth::guard('customer')->user();
        $customer->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'company_name' => $this->company_name ?: null,
        ]);

        session()->flash('success', 'Your information has been updated.');
    }

    public function render(): View
    {
        return view('livewire.account.basic-info-page')
            ->layout('layouts.storefront');
    }
}
