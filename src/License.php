<?php

namespace Armincms\EasyLicense;

use Armincms\Orderable\Contracts\Orderable;
use Armincms\Orderable\Contracts\Saleable;

class License extends Model implements Orderable, Saleable
{  
    use IntractsWithDiscount, IntractsWithCredits;

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

    /**
     * Get the sale price currency.
     * 
     * @return decimal
     */
    public function currency(): string
    {
        return Nova\EasyLicense::currency();
    }

    /**
     * Get the sale price of the item.
     * 
     * @return decimal
     */
    public function salePrice(): float
    {
        return $this->price;
    }

    /**
     * Get the real price of the item.
     * 
     * @return decimal
     */
    public function oldPrice(): float
    {
        return $this->salePrice();
    }

    /**
     * Get the item name.
     * 
     * @return decimal
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the item description.
     * 
     * @return decimal
     */
    public function description(): string
    {
        return $this->name();
    }
}
