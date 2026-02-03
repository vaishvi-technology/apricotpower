<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Models\Collection;

class Home extends Component
{
    public function render(): View
    {
        $eagerLoads = [
            'products.variants.basePrices.currency',
            'products.defaultUrl',
            'products.thumbnail',
        ];

        $bannerProducts = $this->getCollectionProducts('banner', $eagerLoads);
        $ourProducts = $this->getCollectionProducts('featured', $eagerLoads);
        $hotBuysProducts = $this->getCollectionProducts('hot-buys', $eagerLoads);

        return view('livewire.home', compact('bannerProducts', 'ourProducts', 'hotBuysProducts'));
    }

    protected function getCollectionProducts(string $slug, array $eagerLoads): \Illuminate\Support\Collection
    {
        $collection = Collection::whereHas('defaultUrl', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })->with($eagerLoads)->first();

        return $collection?->products ?? collect();
    }
}
