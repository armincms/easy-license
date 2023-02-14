<?php

namespace Armincms\EasyLicense\Events;

use Armincms\EasyLicense\Models\Purchase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseDone
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public Purchase $purchase) {

    }
}
