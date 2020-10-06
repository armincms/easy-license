<?php

namespace Armincms\EasyLicense;
 
use Zareismail\Markable\HasActivation;
use Armincms\Concerns\Authorization;
use Armincms\Contracts\Authorizable;

class Product extends Model implements Authorizable
{ 
    use HasActivation, Authorization;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'json',
        'fields' => 'json',
        'abstract' => 'json',
    ];

    protected $medias = [
        'image' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'easy-license.product', 'easy-license.product.list', '*'
            ]
        ],
    ];

    public function manufacturer()
    {
    	return $this->belongsTo(Manufacturer::class);
    } 

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    /**
     * Get the fields available on the product.
     *
     * @return array
     */
    public function prepareFields()
    { 
        return collect($this->fields)->map(function($field) {
            return array_merge(['field' => $field['layout']], $field['attributes']);
        });
    }
}
