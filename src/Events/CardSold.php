<?php

namespace Armincms\EasyLicense\Events;

use Armincms\EasyLicense\Models\Card;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardSold
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public Card $card) {

    }
}
