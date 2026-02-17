<?php

namespace App\Livewire\Account;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Order;

class OrderHistoryPage extends Component
{
    public ?string $id = null;
    public ?Order $selectedOrder = null;

    public function mount(?string $id = null): void
    {
        $this->id = $id;

        if ($this->id) {
            $this->selectedOrder = Order::where('id', $this->id)
                ->whereHas('customer', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->with(['lines.purchasable', 'shippingAddress', 'billingAddress'])
                ->first();
        }
    }

    /**
     * Get the user's orders.
     */
    public function getOrdersProperty(): Collection
    {
        return Order::whereHas('customer', function ($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['lines.purchasable'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function viewOrder(string $id): void
    {
        $this->redirect(route('order-history.view', ['id' => $id]));
    }

    public function render(): View
    {
        return view('livewire.account.order-history-page')
            ->layout('layouts.storefront');
    }
}
