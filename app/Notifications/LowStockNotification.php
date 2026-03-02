<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Product $product;
    protected int $currentStock;
    protected int $threshold;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->currentStock = $product->available_stock;
        $this->threshold = $product->notify_at;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $productName = $this->product->name ?? $this->product->translateAttribute('name') ?? 'Product #' . $this->product->id;
        $adminUrl = url('/admin/products/' . $this->product->id . '/inventory-lots');

        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $productName)
            ->greeting('Low Stock Alert')
            ->line("The following product has reached its low stock threshold:")
            ->line('')
            ->line("**Product:** {$productName}")
            ->line("**Current Stock:** {$this->currentStock}")
            ->line("**Notification Threshold:** {$this->threshold}")
            ->line('')
            ->action('View Product Inventory', $adminUrl)
            ->line('Please restock this item to maintain inventory levels.')
            ->salutation('— Apricot Power Inventory System');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->currentStock,
            'threshold' => $this->threshold,
        ];
    }
}
