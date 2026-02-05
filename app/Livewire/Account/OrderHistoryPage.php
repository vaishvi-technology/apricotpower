<?php

namespace App\Livewire\Account;

use Illuminate\View\View;
use Livewire\Component;

class OrderHistoryPage extends Component
{
    public ?string $id = null;

    public function mount(?string $id = null): void
    {
        $this->id = $id;
    }

    public function render(): View
    {
        return view('livewire.account.order-history-page')
            ->layout('layouts.storefront');
    }
}
