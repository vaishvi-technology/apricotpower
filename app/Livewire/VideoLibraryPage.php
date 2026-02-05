<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class VideoLibraryPage extends Component
{
    public function render(): View
    {
        return view('livewire.video-library-page')
            ->layout('layouts.storefront');
    }
}
