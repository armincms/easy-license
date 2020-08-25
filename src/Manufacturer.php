<?php

namespace Armincms\EasyLicense; 

use Core\HttpSite\Concerns\IntractsWithSite;
use Core\HttpSite\Component; 

class Manufacturer extends Model 
{     
    protected $translator = 'layeric';

    protected $medias = [
        'image' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'easy-license.manufacturer', 'easy-license.manufacturer.list', '*'
            ]
        ],
    ];

    const TRANSLATION_MODEL = Translation::class; 
    const LOCALE_KEY = 'language';  

    public function component() : Component
    {
        return new Components\Manufacturer;
    }
}
