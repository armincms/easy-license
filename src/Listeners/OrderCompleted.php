<?php

namespace Armincms\EasyLicense\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Armincms\EasyLicense\Card;

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
                if($orderItem->saleable->delivery === 'system') {
                    return $orderItem->saleable->createCredit(
                        $order->customer, __('Online Payment'), $orderItem->count
                    ); 
                }
                if($orderItem->saleable->delivery === 'card') {
                    $credits = Card::where('license_id', $orderItem->saleable->getKey())->whereHas('manuals', function($query) {
                        return $query->forSales();
                    })->with('manuals')->get()->flatMap->manuals;
                    if ($credits->count() >= $orderItem->count && 
                        collect($orderItem->details)->isEmpty()
                    ) {  
                        $orderItem->update([
                            'details' => $credits->take($orderItem->count)->map->asSold()->each->save()->toArray()
                        ]);
                    } 
                }
            })->filter()->each(function($credit) use ($order) {
                $credit->orders()->sync($order);
            }); 
        }
    }
}
