<?php

namespace Armincms\EasyLicense\Http\Controllers;

use Armincms\EasyLicense\Events\PurchaseDone;
use Armincms\EasyLicense\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PurchaseDoneController extends Controller
{
    public function __invoke(Request $request)
    {
        $purchase = Purchase::tracking($request->route('number'))->where(['marked_as' => 'checkout'])->with('license', 'user')->firstOrFail();

        $purchase->forceFill(['marked_as' => 'paid'])->save();

        PurchaseDone::dispatch($purchase);

        return redirect($purchase->checkoutPage());
    }
}
