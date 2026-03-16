<?php

namespace App\Lunar\Filament\Resources\ProductResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Lunar\Admin\Filament\Resources\ProductResource\Pages\ManageProductPricing as BaseManageProductPricing;

class ManageProductPricing extends BaseManageProductPricing
{
    public function form(Form $form): Form
    {
        if (! count($this->basePrices)) {
            $this->basePrices = $this->getBasePrices();
        }

        // Only include base price section, remove tax class and tax ref
        $form->schema([
            $this->getBasePriceFormSection(),
        ])->statePath('');

        $this->callLunarHook('extendForm', $form);

        return $form;
    }
}
