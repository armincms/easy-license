<?php

namespace Armincms\EasyLicense;


class License extends Model 
{  
    use IntractsWithDiscount;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'discount' => 'json',
        'abstract' => 'json',
        'name' => 'json',
    ];

    protected $medias = [
        'image' => [ 
            'disk'  => 'armin.image',
            'schemas' => [
                'easy-license.license', 'easy-license.license.list', '*'
            ]
        ],
    ]; 

    public function credits()
    {
    	return $this->hasMany(Credit::class);
    } 

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function duration()
    {
        return $this->belongsTo(\Armincms\Duration\Duration::class);
    }  
}
