<?php

namespace App\Filament\Resources\InventoryLotResource\Pages;

use App\Filament\Resources\InventoryLotResource;
use App\Models\InventoryMovement;
use App\Models\User;
use App\Services\InventoryService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateInventoryLot extends CreateRecord
{
    protected static string $resource = InventoryLotResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate lot number if empty
        if (empty($data['lot_number'])) {
            $data['lot_number'] = app(InventoryService::class)->generateLotNumber();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Record the initial inventory movement
        InventoryMovement::create([
            'product_id' => $this->record->product_id,
            'product_variant_id' => $this->record->product_variant_id,
            'inventory_lot_id' => $this->record->id,
            'type' => InventoryMovement::TYPE_RECEIVED,
            'quantity' => $this->record->quantity,
            'quantity_before' => 0,
            'quantity_after' => $this->record->quantity,
            'reason' => 'Initial lot creation',
            'user_id' => $this->getCurrentUserId(),
        ]);
    }

    /**
     * Get the current user ID for tracking.
     * Returns null for staff users since their IDs reference a different table.
     */
    protected function getCurrentUserId(): ?int
    {
        $user = Auth::guard('web')->user();
        return $user instanceof User ? $user->id : null;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
