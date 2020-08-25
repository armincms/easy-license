<?php

namespace Armincms\EasyLicense\Http\Controllers;
 
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Armincms\EasyLicense\Product; 

class LicenseController extends Controller
{ 
    public function handle(Request $request, Product $product)
    {     
    	return  $product->load(['licenses' => function($query) {
		    		return $query->where('delivery', 'card');
		    	}])->licenses->map(function($license) {
		    		return [
		    			'value' => $license->id,
		    			'display' => $license->name,
		    		];
		    	})->values();	
    }
}
