<?php

namespace Armincms\EasyLicense; 

use Core\HttpSite\Concerns\IntractsWithSite;
use Core\HttpSite\Component; 

class Manufacturer extends Model 
{     
    use IntractsWithSite;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];
    
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
