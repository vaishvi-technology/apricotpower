<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerWelcomeNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Apricot Power!')
            ->greeting('Welcome, ' . $notifiable->first_name . '!')
            ->line('Thank you for creating an account with Apricot Power. We\'re thrilled to have you as part of our community.')
            ->line('With your account you can:')
            ->line('• View your order history and track shipments')
            ->line('• Manage your shipping and billing addresses')
            ->line('• Update your account details and preferences')
            ->action('Visit Your Account', url(route('order-history.view')))
            ->line('If you have any questions, feel free to contact us.')
            ->salutation('Thank you, The Apricot Power Team');
    }
}
