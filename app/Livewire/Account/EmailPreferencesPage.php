<?php

namespace App\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class EmailPreferencesPage extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public bool $is_subscribed = false;

    public function mount(): void
    {
        $customer = Auth::guard('customer')->user();
        $this->first_name = $customer->first_name ?? '';
        $this->last_name = $customer->last_name ?? '';
        $this->email = $customer->email ?? '';
        $this->is_subscribed = (bool) ($customer->subscribe_to_list ?? false);
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ];
    }

    public function subscribe(): void
    {
        $this->validate();

        $customer = Auth::guard('customer')->user();

        $customer->first_name = $this->first_name;
        $customer->last_name = $this->last_name;
        $customer->email = $this->email;
        $customer->subscribe_to_list = true;
        $customer->save();

        $this->is_subscribed = true;

        session()->flash('success', 'You have been subscribed to the Apricot Power email list!');
    }

    public function unsubscribe(): void
    {
        $customer = Auth::guard('customer')->user();
        $customer->subscribe_to_list = false;
        $customer->save();

        $this->is_subscribed = false;

        session()->flash('success', 'You have been unsubscribed from the Apricot Power email list.');
    }

    public function render(): View
    {
        return view('livewire.account.email-preferences-page')
            ->layout('layouts.storefront');
    }
}
