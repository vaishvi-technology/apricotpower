<?php

namespace App\Livewire;

use App\Models\FaqCategory;
use Illuminate\View\View;
use Livewire\Component;

class FaqPage extends Component
{
    public function render(): View
    {
        $faqCategories = FaqCategory::active()
            ->ordered()
            ->with(['faqs' => function ($query) {
                $query->active()->ordered();
            }])
            ->get();

        return view('livewire.faq-page', [
            'faqCategories' => $faqCategories,
        ])->layout('layouts.storefront');
    }
}
