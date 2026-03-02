<?php

namespace App\Console\Commands;

use App\Services\InventoryService;
use Illuminate\Console\Command;

class CheckLowStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'inventory:check-low-stock';

    /**
     * The console command description.
     */
    protected $description = 'Check all products for low stock levels and send notifications to admin users';

    /**
     * Execute the console command.
     */
    public function handle(InventoryService $inventoryService): int
    {
        $this->info('Checking products for low stock levels...');

        $notifiedProducts = $inventoryService->checkAllProductsForLowStock();

        if (empty($notifiedProducts)) {
            $this->info('No low stock notifications sent.');
        } else {
            $this->info('Low stock notifications sent for ' . count($notifiedProducts) . ' product(s).');
            foreach ($notifiedProducts as $productId) {
                $this->line("  - Product ID: {$productId}");
            }
        }

        return Command::SUCCESS;
    }
}
