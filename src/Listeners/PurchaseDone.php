<?php

namespace Armincms\EasyLicense\Listeners;

use Armincms\EasyLicense\Events\CardSold;
use Armincms\EasyLicense\Models\Card;

class PurchaseDone
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $purchase = $event->purchase;
        $license = $purchase->license;

        if ($license->isManual()) {
            // nothing to do
            return;
        }

        if ($license->isAutomate()) {
            // Generate a new card
            $information = $license->generateCardInformation();
            $card = Card::unguarded(fn() => tap(new Card(), fn($card) => $card->forceFill([
                'config->information' => $information,
                'enable' => true,
                'sold_at' => now(),
            ])));

            $card->user()->associate($purchase->user);
            $card->license()->associate($purchase->license);
            $card->save();

            $purchase->forceFill([
                'detail->card' => $card->toArray(),
                'marked_as' => empty(array_filter($information)) ? 'failure' : 'complete',
            ]);
            $purchase->card()->associate($card);
            $purchase->save();

            CardSold::dispatch($card);

            return;
        }

        if ($card = $license->cards()->enable()->availables()->first()) {
            // Pick one of the available cards
            $card->user()->associate($purchase->user);
            $card->forceFill(['sold_at' => now()]);
            $card->save();

            $purchase->forceFill([
                'detail->card' => $card->toArray(),
                'marked_as' => 'complete',
            ]);
            $purchase->card()->associate($card);
            $purchase->save();

            CardSold::dispatch($card);
        } else {
            // Failure on picking card
            $purchase->forceFill([ 'marked_as' => 'failure' ])->save();
        }
    }
}
