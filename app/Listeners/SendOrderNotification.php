<?php

namespace App\Listeners;

use App\Events\OrderComplete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/*
 * SendOrderNotification implements OrderNOtification.
*/
class SendOrderNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderComplete  $event
     * @return void
     */
    public function handle(OrderComplete $event)
    {
        //
    }
}
