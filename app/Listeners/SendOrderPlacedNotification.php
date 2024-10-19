<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderPlacedNotification
{
    /**
     * Handle the event.
     *
     * @param \App\Events\OrderPlaced $event
     * @return void
     */
    public function handle(OrderPlaced $event)
    {

        Log::info('Order placed notification: Order #' . $event->order->id . ' has been placed by User #' . $event->order->user_id);


        // Mail::to('admin@example.com')->send(new OrderPlacedMail($event->order));
    }
}
