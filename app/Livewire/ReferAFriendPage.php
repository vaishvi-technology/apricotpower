<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class ReferAFriendPage extends Component
{
    public function render(): View
    {
        return view('livewire.refer-a-friend-page')
            ->layout('layouts.storefront');
    }
}
