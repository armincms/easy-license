<?php

namespace Armincms\EasyLicense\Nova;

use Armincms\Bios\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;  

class EasyLicense extends Resource
{ 
    /**
     * The option storage driver name.
     *
     * @var string
     */
    public static $store = '';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Select::make(__('Curency'), 'el_currency')
                ->options(collect(currency()->getActiveCurrencies())->map->name)
                ->displayUsingLabels()
                ->withMeta([
                    'value' => 'IRR'
                ]),
        ];
    }
}
