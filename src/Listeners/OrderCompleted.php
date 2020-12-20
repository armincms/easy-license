<?php

namespace Armincms\EasyLicense\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderCompleted
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    { 
        if($event->order->orderable_type == \Armincms\EasyLicense\License::class) {
            $order = $event->order->loadMissing('saleables.saleable', 'customer');

            $order->saleables->flatMap(function($orderItem) use ($order) {
                return $orderItem->saleable->createCredit($order->customer, __('Online Payment'), $orderItem->count);
            })->each(function($credit) use ($order) {
                $credit->orders()->sync($order);
            }); 
        }
    }
}
