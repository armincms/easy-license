<?php

namespace Armincms\EasyLicense\Http\Controllers;
 
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Armincms\EasyLicense\Manufacturer;
use Armincms\EasyLicense\Nova\Manufacturer as Resource;

class ManufacturerController extends Controller
{ 
    public function handle(Request $request, Manufacturer $manufacturer)
    {   
    	$resource = new Resource($manufacturer); 

    	return $resource->drivers()->map(function($driver, $name) {
    		return [
    			'value' => $name,
    			'display' => $driver['title'] ?? $name,
    		];
    	})->values();	
    }
}
