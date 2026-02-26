<?php

namespace App\Livewire\Account;

use App\Services\ImpersonationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;

class BasicInfoPage extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company_name = '';

    // Change Password
    public bool $isImpersonating = false;
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(): void
    {
        $this->isImpersonating = app(ImpersonationService::class)->isImpersonating();
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

    public function updatePassword(): void
    {
        $rules = ['password' => 'required|min:8|confirmed'];

        if (! $this->isImpersonating) {
            $rules['current_password'] = 'required';
        }

        $this->validate($rules);

        $customer = Auth::guard('customer')->user();

        if (! $this->isImpersonating && !Hash::check($this->current_password, $customer->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $customer->update([
            'password' => Hash::make($this->password),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('password_success', 'Your password has been updated.');
    }

    public function render(): View
    {
        return view('livewire.account.basic-info-page')
            ->layout('layouts.storefront');
    }
}
