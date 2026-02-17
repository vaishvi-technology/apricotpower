<?php

namespace App\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class EmailPreferencesPage extends Component
{
    public bool $marketing_emails = false;
    public bool $order_updates = true;
    public bool $newsletter = false;
    public bool $promotional_offers = false;

    public function mount(): void
    {
        $user = Auth::user();
        $preferences = $user->email_preferences ?? [];

        $this->marketing_emails = $preferences['marketing_emails'] ?? false;
        $this->order_updates = $preferences['order_updates'] ?? true;
        $this->newsletter = $preferences['newsletter'] ?? false;
        $this->promotional_offers = $preferences['promotional_offers'] ?? false;
    }

    public function save(): void
    {
        $user = Auth::user();
        $user->update([
            'email_preferences' => [
                'marketing_emails' => $this->marketing_emails,
                'order_updates' => $this->order_updates,
                'newsletter' => $this->newsletter,
                'promotional_offers' => $this->promotional_offers,
            ],
        ]);

        session()->flash('success', 'Your email preferences have been updated.');
    }

    public function render(): View
    {
        return view('livewire.account.email-preferences-page')
            ->layout('layouts.storefront');
    }
}
