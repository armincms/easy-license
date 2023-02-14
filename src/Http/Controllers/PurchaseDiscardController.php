<?php

namespace Armincms\EasyLicense\Http\Controllers;

use Armincms\EasyLicense\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PurchaseDiscardController extends Controller
{
    public function __invoke(Request $request)
    {
        $purchase = Purchase::tracking($request->route('number'))->first();

        $purchase->forceFill(['marked_as' => 'discard'])->save();

        return redirect('/');
    }
}
