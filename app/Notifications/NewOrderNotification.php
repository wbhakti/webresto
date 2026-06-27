<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Pesanan Baru')
            ->icon('/images/logo.png') // optional
            ->body(
                'Pesanan #' . $this->order->id_transaction .
                ' dari ' . $this->order->nama
            )
            ->action('Lihat', url('/dashboard/dayTransaction'))
            ->data([
                'url' => url('/dashboard/dayTransaction'),
            ]);
    }
}