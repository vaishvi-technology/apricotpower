<?php

namespace App\Filament\Resources\NutrientResource\Pages;

use App\Filament\Resources\NutrientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNutrients extends ListRecords
{
    protected static string $resource = NutrientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
