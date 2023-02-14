<?php

namespace Armincms\EasyLicense\Http\Controllers;

use Armincms\Arminpay\Models\ArminpayGateway;
use Armincms\EasyLicense\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PurchaseSubmitController extends Controller
{
    public function __invoke(Request $request)
    {
        $purchase = Purchase::tracking($request->route('number'))->first();

        return ArminpayGateway::findOrFail($request->input('gate'))->checkout($request, $purchase);
    }
}
