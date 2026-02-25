<?php

namespace Unusualdope\LaravelEcommerce\Mail\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Unusualdope\LaravelEcommerce\Models\Order\Order;

class OrderStatusChanged extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public $url;

    public $content;

    public $email_title;

    public $greeting;

    /**
     * Create a new message instance.
     */
    public function __construct(public int $order_id, $locale)
    {
        App::setLocale($locale);
        $this->order = Order::find($order_id);
        $this->url = route('account-order-detail', $this->order->id);
        $this->content = __('mail.order_status_changed.content',
            [
                'order_reference' => $this->order->reference,
                'prev_status' => Order::previousStatusName($this->order->id),
                'current_status' => $this->order->status->currentLanguage->name ?? '',
            ]);
        $this->email_title = __('mail.order_status_changed.email_title');
        $this->greeting = __('mail.common.greeting',
            [
                'first_name' => $this->order->client->first_name,
                'last_name' => $this->order->client->last_name,
            ]);

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'ecommerce@ggteamwear.com',
            subject: __('mail.order_status_changed.subject', ['order_reference' => $this->order->reference]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        return new Content(
            markdown: 'emails.order.status-changed'
        );
    }
}
