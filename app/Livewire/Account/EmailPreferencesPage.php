<?php

namespace App\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class EmailPreferencesPage extends Component
{
    public bool $subscribe_to_list = false;
    public bool $order_updates = true;
    public bool $newsletter = false;
    public bool $promotional_offers = false;

    public function mount(): void
    {
        $customer = Auth::guard('customer')->user();
        $this->subscribe_to_list = (bool) ($customer->subscribe_to_list ?? false);

        $prefs = $customer->meta?->toArray() ?? [];
        $this->order_updates = $prefs['email_order_updates'] ?? true;
        $this->newsletter = $prefs['email_newsletter'] ?? false;
        $this->promotional_offers = $prefs['email_promotional'] ?? false;
    }

    public function save(): void
    {
        $customer = Auth::guard('customer')->user();

        $customer->subscribe_to_list = $this->subscribe_to_list;

        $meta = $customer->meta?->toArray() ?? [];
        $meta['email_order_updates'] = $this->order_updates;
        $meta['email_newsletter'] = $this->newsletter;
        $meta['email_promotional'] = $this->promotional_offers;
        $customer->meta = $meta;

        $customer->save();

        session()->flash('success', 'Your email preferences have been updated.');
    }

    public function render(): View
    {
        return view('livewire.account.email-preferences-page')
            ->layout('layouts.storefront');
    }
}
